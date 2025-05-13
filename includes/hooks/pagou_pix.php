<?php

require_once(dirname(__FILE__,3) . "/modules/gateways/pagou_pix/vendor/autoload.php");

date_default_timezone_set('America/Sao_Paulo');

//////////////////////////////////////////////////////////////////////////////////////////
// API Carbon ////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

use Carbon\Carbon;

//////////////////////////////////////////////////////////////////////////////////////////
// API Laravel DataBase //////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

use WHMCS\Database\Capsule;

//////////////////////////////////////////////////////////////////////////////////////////
// API WHMCS Gateway 8.0.0 ///////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

use WHMCS\Module\GatewaySetting;

//////////////////////////////////////////////////////////////////////////////////////////
// API PAGOU MMHOSPEDAGEM ////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

use MMHospedagem\App\Pagou\Pix;

//////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////

add_hook("EmailPreSend", 1, function ($MMHospedagem) {

    $pagou = (new Pix());

    $MMHospedagem_Template_Email = [];

    $email_template = $MMHospedagem['messagename'];

    $invoice_id = $MMHospedagem['relid'];

    $target_templates = [
        'Invoice Created',
        'Invoice Payment Reminder',
        'First Invoice Overdue Notice',
        'Second Invoice Overdue Notice',
        'Third Invoice Overdue Notice'
    ];

    $whmcs_url = rtrim(\App::getSystemUrl(),"/");

    if(in_array($email_template, $target_templates)) {

        foreach (Capsule::table('pagou_pix_config')->where(['Nome_Modulo' => 'pagou_pix'])->get() as $MMHospedagem) {
            $MMHospedagem_EmitirPix_TodosClientes = $MMHospedagem->emitir;
        }

        $ExistePix = Capsule::table("pagou_pix_cobrancas")->where(["invoice" => $invoice_id, "status" => "ABERTO"])->orderBy("id", "desc")->limit(1)->first();

        foreach (Capsule::table('tblinvoices')->where(['id' => $invoice_id])->get() as $fatura) {
            $iddocliente = $fatura->userid;
            $datadevencimentofatura = $fatura->duedate;
            $valordafaturacomponto = $fatura->total;
			$formadepagamento = $fatura->paymentmethod;
        }

        if((empty($ExistePix->location))) {

            if(($MMHospedagem_EmitirPix_TodosClientes == "on")) {
                $pagou->criar_cobranca($invoice_id);
            } else {

                if(($formadepagamento == "pagou_pix")) {
                    $pagou->criar_cobranca($invoice_id);
                }

            }

        }

        if((!empty($ExistePix->location))) {

            $json = base64_decode($ExistePix->json);
            $json = json_decode($json, true);
    
            if((preg_replace('/\D/', '', number_format($json["amount"], 2, '.', '')) <> preg_replace('/\D/', '', $valordafaturacomponto))) {
    
                // Cancela o PIX antigo
                $pagou->cancelar_cobranca($invoice_id);
    
                // Gera um novo
                $pagou->criar_cobranca($invoice_id);
    
            }
    
        }

        $ExistePix = Capsule::table("pagou_pix_cobrancas")->where(["invoice" => $invoice_id, "status" => "ABERTO"])->orderBy("id", "desc")->limit(1)->first();

        $MMHospedagem_Template_Email["pagou_pix_copiarcolar"] = '<input style="width: 100%; border: 1px solid #ccc; padding: 5px;" disabled="disabled" type="text" value="' . $ExistePix->location . '">';
        $MMHospedagem_Template_Email["pagou_pix_qrcode"] = '<img style="width: 130px; height="130px" padding: 0; margin: 0;" src="' . $whmcs_url . "/pagou_pix.php?metodo=pix&id=" . $invoice_id . '">';

    }
    
    return $MMHospedagem_Template_Email;

});

add_hook("EmailTplMergeFields", 1, function ($vars) {

    $merge_fields = [];

    $merge_fields["pagou_pix_copiarcolar"] = "Exibe o texto copiar e colar do PIX no template de e-mail";
    $merge_fields["pagou_pix_qrcode"] = "Adiciona uma imagem do QRCode do PIX no template de e-mail";
            
    return $merge_fields;

});

add_hook('AdminInvoicesControlsOutput', 1, function($vars) {

    if(($vars["paymentmethod"] == "pagou_pix")) {

        $pagou = (new Pix());

        $invoice = $vars["invoiceid"];
        $idcliente = $vars["userid"];

        $ExistePix = Capsule::table('pagou_pix_cobrancas')->where(["invoice" => $invoice, "status" => "PAGO"])->orderBy("id", "desc")->limit(1)->first();

        if((!empty($ExistePix->location))) {

            $json = base64_decode($ExistePix->json_confirmacao);
            $json = json_decode($json, true);

            foreach (Capsule::table('tblcustomfieldsvalues')->where(['fieldid' => $pagou->origem_cpfcnpj, 'relid' => $idcliente])->get() as $dados) {
                $cpfcnpj = preg_replace('/\D/', '', $dados->value);
            }

            if(($cpfcnpj != preg_replace('/\D/', '',$json["data"]["payer"]["bank"]["document"]))) {

                $response = file_get_contents("https://brasilapi.com.br/api/banks/v1/" . $json["data"]["payer"]["bank"]["code"]);
                $banco = json_decode($response, true);

                return '<div class="alert alert-warning" role="alert" style="text-align: left;">
                    <strong>Atenção:</strong> o pagamento desta fatura foi realizado por um titular diferente do CPF ou CNPJ cadastrado na conta.<br>
                    Verifique os dados abaixo para confirmar a origem do pagamento.<hr>
                    <table class="table table-bordered">
                        
                        <tbody>
                            <tr>
                                <td>Nome do Pagador</td>
                                <td>' . $json["data"]["payer"]["bank"]["name"] . '</td>
                            </tr>
                            <tr>
                                <td>Documento (CPF/CNPJ)</td>
                                <td>' . $json["data"]["payer"]["bank"]["document"] . '</td>
                            </tr>
                            <tr>
                                <td>Banco</td>
                                <td>' . ($banco["name"] != "" ? $banco["name"] : "-") . '</td>
                            </tr>
                            <tr>
                                <td>Agência</td>
                                <td>' . $json["data"]["payer"]["bank"]["agency"] . '</td>
                            </tr>
                            <tr>
                                <td>Conta</td>
                                <td>' . $json["data"]["payer"]["bank"]["account"] . '</td>
                            </tr>
                        </tbody>
                    </table>

                </div>';

            }

        }

    }

});