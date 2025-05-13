<?php

date_default_timezone_set('America/Sao_Paulo');

require_once(dirname(__FILE__) . "/pagou_pix/vendor/autoload.php");

////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
use Carbon\Carbon;
use WHMCS\Session;
use WHMCS\Database\Capsule;
use WHMCS\Module\GatewaySetting;
use WHMCS\Billing\Payment\Transaction\Information;
use MMHospedagem\App\Pagou\Pix;
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////

function pagou_pix_config() {

    $pagou = (new Pix());
    $smarty = (new Smarty());

    $pagou->database();

    $smarty->assign('pagou_pix_dir_logs', dirname(__FILE__) . "/pagou_pix/MMHospedagem/Logs/");
    $smarty->assign('mm_template_admin', dirname(__FILE__) . "/pagou_pix/templates/admin/");
    $smarty->assign("debug" , $pagou->debug);
    $smarty->assign("sandbox", $pagou->sandbox);
    $smarty->assign("nome_modulo", $pagou->nome_modulo);
    $smarty->assign("versao_modulo", $pagou->versao);
    $smarty->assign("versao_instalada", $pagou->versao_instalada);
    $smarty->assign("versao_github", $pagou->github);
    $smarty->assign("origem_cpfcnpj", $pagou->OrigemCPFCNPJ());
    $smarty->assign("apikey", $pagou->apikey);

    $smarty->assign("desconto", $pagou->desconto);
    $smarty->assign("desconto_tipo", $pagou->desconto_tipo);
    $smarty->assign("desconto_valor", $pagou->desconto_valor);

    $smarty->assign("juros", $pagou->juros);
    $smarty->assign("juros_tipo", $pagou->juros_tipo);
    $smarty->assign("juros_valor", $pagou->juros_valor);

    $smarty->assign("multa", $pagou->multa);
    $smarty->assign("multa_tipo", $pagou->multa_tipo);
    $smarty->assign("multa_valor", $pagou->multa_valor);

    $smarty->assign("carencia_pagamento", $pagou->carencia_pagamento);

    $smarty->assign("emitir_pix", $pagou->emitir);

    return [
        "FriendlyName" => [
            "Type" => "System", 
            "Value"=>"Pagou - PIX"
        ],
        'pagou_pix_pagina' => [
            'FriendlyName' => '',
            'Description' => $smarty->fetch(dirname(__FILE__) . "/pagou_pix/templates/admin/index.tpl")
        ] 
    ];

}

function pagou_pix_link($params) {

    $pagou = (new Pix());
    $smarty = (new Smarty());

    $invoice = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];
    $email = $params['clientdetails']['email'];
    $cep = $params['clientdetails']['postcode'];

    $fatura = localAPI("GetInvoice", [
        "invoiceid" => $invoice
    ]);

    if(($amount < 5.00)) {
        return '<div class="alert alert-danger" role="alert">Não é possível utilizar esta forma de pagamento. O valor mínimo permitido é de R$5.00 reais.</div>';
    }

    $ExistePix = Capsule::table('pagou_pix_cobrancas')->where(['invoice' => $invoice])->orderBy("id", "desc")->limit(1)->first();
    
    // Se não existe um pix cria
    if((empty($ExistePix->location))) {
        $pagou->criar_cobranca($invoice);
    }

    // Se o valor da fatura for diferente do PIX emitido, o sistema ira emitir um novo

    if((!empty($ExistePix->location))) {

        $json = base64_decode($ExistePix->json);
        $json = json_decode($json, true);

        if((preg_replace('/\D/', '', number_format($json["amount"], 2, '.', '')) <> preg_replace('/\D/', '', $amount))) {

            // Cancela o PIX antigo
            $pagou->cancelar_cobranca($invoice);

            // Gera um novo
            $pagou->criar_cobranca($invoice);

        }

    }

    // Monta o QRCODE para o cliente pagar

    $ExistePix = Capsule::table('pagou_pix_cobrancas')->where(['invoice' => $invoice])->orderBy("id", "desc")->limit(1)->first();

    if((!empty($ExistePix->location))) {

        $json = base64_decode($ExistePix->json);
        $json = json_decode($json, true);

        $smarty->assign('numero_fatura', $invoice);
        $smarty->assign('copiar_colar_pix', $ExistePix->location);
        $smarty->assign('url_qrcode', "data:image/png;base64," . $json["payload"]["image"]);

        return $smarty->display(dirname(__FILE__) . "/pagou_pix/templates/cliente/invoice.tpl");

    }

    return '<div class="alert alert-danger" role="alert">Algo deu errado na emissão do seu PIX.</div>';

}

function pagou_pix_refund($params) {

    $pagou = (new Pix());
    $smarty = (new Smarty());

    $invoice = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];
    $email = $params['clientdetails']['email'];
    $cep = $params['clientdetails']['postcode'];

    $id_transacao = $pagou->get_transation_id($invoice,"REEMBOLSADO");

    $send = $pagou->reembolsar_cobranca(
        $params['transid'],
        $params['amount']
    );

    if(!empty($send["error"])) {
        return [
            'status' => 'error',
            'rawdata' => $send["error"]
        ];
    }

    return [
        'status' => 'success',
        'rawdata' => $id_transacao,
        'transid' => $id_transacao
    ];

}