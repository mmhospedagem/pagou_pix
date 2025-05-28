<?php

require_once(dirname(__FILE__,3) . "/vendor/autoload.php");
require_once(dirname(__FILE__,6) . "/init.php");
require_once(dirname(__FILE__,6) . "/includes/gatewayfunctions.php");
require_once(dirname(__FILE__,6) . "/includes/invoicefunctions.php");

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
header("access-control-allow-origin: *");
date_default_timezone_set('America/Sao_Paulo');
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
$webhook_recebido = json_decode(file_get_contents('php://input'), true);
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////

$pagou = (new Pix());

if(($_GET["token"] != $pagou->tokenSeguranca)) {
    $pagou->Logs("[ERROR][WEBHOOK] Acesso negado ao acessar o arquivo webhook, token de acesso não autorizado - IP: {$_SERVER["REMOTE_ADDR"]}");
    http_response_code(403);
    exit();
}

$listaIP = [
    base64_decode($pagou->seg_webhook)
];

$clientIP = $_SERVER['REMOTE_ADDR'];

$ipValido = false;

foreach ($listaIP as $range) {
    if (strpos($range, '/') !== false) {
        if ($MMHospedagem_Classes->cidr_match($clientIP, $range)) {
            $ipValido = true;
            break;
        }
    } else {
        if ($clientIP === $range) {
            $ipValido = true;
            break;
        }
    }
}

if ($ipValido) {

    if(($webhook_recebido["name"] == "qrcode.completed")) {

        $txid = $webhook_recebido["data"]["id"];
        
        $dateTime = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $dataAtual = $dateTime->format('Y-m-d H:i:s');
        
        $consultar_qrcode = $pagou->consultar_cobranca($txid);

        ////////////////////////////////////////////////////////////////////////
        // PAGO
        ////////////////////////////////////////////////////////////////////////
        if(($consultar_qrcode["status"] == 4)) {
    
            foreach (Capsule::table("pagou_pix_cobrancas")->where(["txid" => $txid, "status" => "ABERTO"])->get() as $key => $cobranca) {
            
                http_response_code(200);
        
                $fatura = localAPI("GetInvoice", [
                    "invoiceid" => $cobranca->invoice
                ]);
        
                if(($fatura["status"] == "Unpaid")) {
        
                    $valorFatura_WHMCS = number_format($fatura["balance"], 2, '.', '');
                    $valorPago_Cliente = number_format($webhook_recebido["data"]["amount"], 2, '.', '');
        
                    if(($valorPago_Cliente > $valorFatura_WHMCS) && ($valorFatura_WHMCS != "0.00")) {
    
                        $dataAtual = (new DateTime())->format('Y-m-d');
                        
                        if(($dataAtual > $fatura["duedate"])) {

                            if(($pagou->multa == "on") || ($pagou->juros == "on")) {

                                $taxaJurosMulta = number_format(($valorPago_Cliente - $valorFatura_WHMCS), 2, '.', '');
    
                                localAPI("UpdateInvoice",[
                                    "invoiceid" => $cobranca->invoice,
                                    "newitemdescription" => [0 => "Juros e Multa por atraso"],
                                    "newitemamount" => [0 => "{$taxaJurosMulta}"],
                                    "newitemtaxed" => [0 => true]
                                ]);

                            }
    
                        }

                    }

                    if(($pagou->desconto == "on")) {

                        $valorDesconto = number_format(($valorPago_Cliente - $valorFatura_WHMCS), 2, '.', '');

                        localAPI("UpdateInvoice",[
                            "invoiceid" => $cobranca->invoice,
                            "newitemdescription" => [0 => "Desconto pagamento até a data de vencimento"],
                            "newitemamount" => [0 => "-{$valorDesconto}"],
                            "newitemtaxed" => [0 => false]
                        ]);

                    }

                    Capsule::table("pagou_pix_cobrancas")->where(["txid" => $txid, "status" => "ABERTO"])->update([
                        "json_confirmacao" => json_encode($webhook_recebido),
                        "status" => "PAGO",
                        "update_up" => $dataAtual
                    ]);

                    localAPI("AddInvoicePayment", [
                        'invoiceid' => $cobranca->invoice,
                        'transid' => $webhook_recebido["data"]["e2e_id"],
                        'amount' => number_format($consultar_qrcode["amount"], 2, '.', ''),
                        'fees' => number_format($consultar_qrcode["fee"], 2, '.', ''),
                        'gateway' => 'pagou_pix',
                        'date' => date('Y-m-d H:i:s')
                    ]);
        
                }

            }

        }

    }

    if(($webhook_recebido["name"] == "qrcode.refunded")) {

        $txid = $webhook_recebido["data"]["id"];

        $dateTime = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $dataAtual = $dateTime->format('Y-m-d H:i:s');

        $new_txid = $this->get_transation_id($invoice,"REEMBOLSADO");

        foreach (Capsule::table("pagou_pix_cobrancas")->where(["txid" => $txid, "status" => "PAGO"])->get() as $key => $cobranca) {
            
            http_response_code(200);
    
            $fatura = localAPI("GetInvoice", [
                "invoiceid" => $cobranca->invoice
            ]);
    
            if(($fatura["status"] == "Paid")) {

                paymentReversed($new_txid, $cobranca->end_txid, 0, 'pagou_pix');

                Capsule::table("pagou_pix_cobrancas")->where(["txid" => $txid, "status" => "PAGO"])->update([
                    "json_confirmacao" => json_encode($webhook_recebido),
                    "status" => "REEMBOLSADO",
                    "update_up" => $dataAtual
                ]);

            }

        }        

    }

} else {

    $pagou->Logs("[ERROR][WEBHOOK] Acesso negado ao acessar o arquivo webhook, IP de acesso não autorizado - IP: {$_SERVER["REMOTE_ADDR"]}");
    http_response_code(403);
    exit();

}