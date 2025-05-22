<?php

namespace MMHospedagem\App\Pagou;

//////////////////////////////////////////////////////////////////////////////////////////
use DateTime;
use DateTimeZone;
use WHMCS\Session;
use Carbon\Carbon;
use WHMCS\Database\Capsule;
use \WHMCS\Module\GatewaySetting;
use WHMCS\Billing\Payment\Transaction\Information;
//////////////////////////////////////////////////////////////////////////////////////////

class Pix {

    public $versao;
    public $debug;
    public $sandbox;
    public $nome_modulo;
    public $github;
    public $apikey;
    public $origem_cpfcnpj;
    public $desconto;
    public $desconto_tipo;
    public $desconto_valor;
    public $juros;
    public $juros_tipo;
    public $juros_valor;
    public $multa;
    public $multa_tipo;
    public $multa_valor;
    public $carencia_pagamento;
    public $tokenSeguranca;
    public $seg_webhook;
    public $emitir;

    public function __construct() {

        $this->versao = "1.0.0";
        $this->nome_modulo = "Pagou [PIX]";        
        $this->seg_webhook = "MTg2LjIwOS4xMTMuMTQx";

        $versao_instalada = "";
        $sandbox = "";
        $debug = "";
        $apikey = "";
        $origem = "";
        $desconto = "";
        $desconto_tipo = "";
        $desconto_valor = "";
        $juros = "";
        $juros_tipo = "";
        $juros_valor = "";
        $multa = "";
        $multa_tipo = "";
        $multa_valor = "";
        $carencia_pagamento = "";
        $emitir = "";

        try {

            foreach (Capsule::table('pagou_pix_atualizacao')->where(['Nome_Modulo' => 'pagou_pix'])->get() as $MMHospedagem) {
                $versao_instalada = $MMHospedagem->versao;
            }

        } catch (\Throwable $th) {}        

        $this->versao_instalada = $versao_instalada;

        $github = $this->send_github();

        $this->github = $github["tag_name"];

        try {
            foreach (Capsule::table('pagou_pix_config')->where(['Nome_Modulo' => 'pagou_pix'])->get() as $MMHospedagem) {
                $sandbox = $MMHospedagem->sandbox;
                $debug = $MMHospedagem->debug;
                $apikey = $MMHospedagem->apikey;
                $origem = $MMHospedagem->origem_cpfcnpj;
                $desconto = $MMHospedagem->desconto;
                $desconto_tipo = $MMHospedagem->desconto_tipo;
                $desconto_valor = $MMHospedagem->desconto_valor;
                $juros = $MMHospedagem->juros;
                $juros_tipo = $MMHospedagem->juros_tipo;
                $juros_valor = $MMHospedagem->juros_valor;
                $multa = $MMHospedagem->multa;
                $multa_tipo = $MMHospedagem->multa_tipo;
                $multa_valor = $MMHospedagem->multa_valor;
                $carencia_pagamento = $MMHospedagem->tempolimite;
                $emitir = $MMHospedagem->emitir;
            }
        } catch (\Throwable $th) {}

        $DB_TOKEN_SERUGANCA = [];

        try {
            $DB_TOKEN_SERUGANCA = Capsule::table('pagou_pix_seguranca')->where(['Nome_Modulo' => 'pagou_pix'])->get();
        } catch (\Throwable $th) {}
        
        $this->debug = $debug;
        $this->sandbox = $sandbox;        
        $this->url = ($this->sandbox == "on" ? "https://sandbox-api.pagou.com.br" : "https://api.pagou.com.br");
        $this->apikey = $apikey;
        $this->origem_cpfcnpj = $origem;
        $this->tokenSeguranca = $DB_TOKEN_SERUGANCA[0]->token;
        $this->desconto = $desconto;
        $this->desconto_tipo = $desconto_tipo;
        $this->desconto_valor = $desconto_valor;
        $this->juros = $juros;
        $this->juros_tipo = $juros_tipo;
        $this->juros_valor = $juros_valor;
        $this->multa = $multa;
        $this->multa_tipo = $multa_tipo;
        $this->multa_valor = $multa_valor;
        $this->carencia_pagamento = $carencia_pagamento;
        $this->emitir = $emitir;

    }

    public function sys_debug($retorno = "") {

        $dataAtual = new DateTime();

        $formatoData = $dataAtual->format('d-m-Y');

        $caminhoDiretorio = dirname(__FILE__,3) . '/Logs/' . $formatoData;

        if (!is_dir($caminhoDiretorio)) {
           
            mkdir($caminhoDiretorio, 0775, true);
        
            chmod($caminhoDiretorio, 0775);

            $caminhoArquivoLog = $caminhoDiretorio . "/mm_log_" . md5($formatoData) . ".txt";

            chmod($caminhoArquivoLog, 0775);

            file_put_contents($caminhoArquivoLog, "=====================================================================\n", FILE_APPEND);
            file_put_contents($caminhoArquivoLog, "Arquivo Log DEBUG\n", FILE_APPEND);
            file_put_contents($caminhoArquivoLog, "Data: {$formatoData}\n", FILE_APPEND);
            file_put_contents($caminhoArquivoLog, "Formato: JSON\n", FILE_APPEND);
            file_put_contents($caminhoArquivoLog, "=====================================================================\n", FILE_APPEND);
            file_put_contents($caminhoArquivoLog, "Hora || Logs\n", FILE_APPEND);
            file_put_contents($caminhoArquivoLog, "=====================================================================\n", FILE_APPEND);

        }

        if(($retorno != "") || ($retorno != null)) {
            file_put_contents("{$caminhoDiretorio}/mm_log_" . md5($formatoData) . ".txt","[" . date("H:i:s") . "] - " . json_encode($retorno) . "\n", FILE_APPEND);
        }

    }

    public function OrigemCPFCNPJ() {

		$NumeroCPFCNPJ = [];

        foreach (Capsule::table('pagou_pix_config')->where(['Nome_Modulo' => 'pagou_pix'])->get() as $dados) {
            $origem = $dados->origem_cpfcnpj;
        }

        $NumeroCPFCNPJ['cpfcnpj'] = "<option value=\"\">Selecione uma opção</option>";

        foreach (Capsule::table('tblcustomfields')->get() as $custom) {

            $selected = ($custom->id == $origem ? 'selected' : '');

            $NumeroCPFCNPJ['cpfcnpj'] .= "<option value=\"".$custom->id ."\" ".$selected."> " . $custom->fieldname . "</option>";

        }
		
		return $NumeroCPFCNPJ;
        
	}

    public function database() {

        if (!Capsule::schema()->hasTable('pagou_pix_atualizacao')) {

            Capsule::schema()->create('pagou_pix_atualizacao', function($table) {
                $table->string('Nome_Modulo');
                $table->string('versao');
            });

            Capsule::table('pagou_pix_atualizacao')->insert([
                'Nome_Modulo' => 'pagou_pix',
                'versao' => $this->versao
            ]);
            
        }

        if (!Capsule::schema()->hasTable('pagou_pix_seguranca')) {

            Capsule::schema()->create('pagou_pix_seguranca', function($table) {
                $table->string('Nome_Modulo');
                $table->text('token');
            });

            Capsule::table('pagou_pix_seguranca')->insert([
                'Nome_Modulo' => 'pagou_pix',
                'token' => bin2hex(random_bytes(32))
            ]);
            
        }

        if (!Capsule::schema()->hasTable('pagou_pix_config')) {

            Capsule::schema()->create('pagou_pix_config', function($table) {                
                $table->string('Nome_Modulo');                    
                $table->string('debug')->nullable();
                $table->string('sandbox')->nullable();
                $table->text('apikey');
                $table->string('origem_cpfcnpj');
                $table->string('desconto')->nullable();
                $table->string('desconto_tipo')->nullable();
                $table->string('desconto_valor')->nullable();
                $table->string('juros')->nullable();
                $table->string('juros_tipo')->nullable();
                $table->string('juros_valor')->nullable();
                $table->string('multa')->nullable();
                $table->string('multa_tipo')->nullable();
                $table->string('multa_valor')->nullable();
                $table->string('tempolimite')->nullable();
                $table->string('emitir')->nullable();
            });

            Capsule::table('pagou_pix_config')->insert([
                'Nome_Modulo' => 'pagou_pix',
            ]);        
            
        }

        if (!Capsule::schema()->hasTable('pagou_pix_logs')) {
                
            Capsule::schema()->create('pagou_pix_logs', function($table) {
                $table->increments('id');
                $table->longtext('log');
                $table->datetime('data');
            });
                
        }

        if (!Capsule::schema()->hasTable('pagou_pix_cobrancas')) {

            Capsule::schema()->create('pagou_pix_cobrancas', function($table) {
                $table->increments('id');
                $table->string('invoice');
                $table->string('criacao');
                $table->integer('expiracao');
                $table->string('txid');
                $table->string('end_txid');
                $table->string('location');
                $table->longtext('json');
                $table->longtext('json_confirmacao');
                $table->string('status');
                $table->datetime('data');
                $table->datetime('update_up');
            });

        }

    }

    public function Hooks(){

        if ($handle = opendir(dirname(__FILE__,2).'/Hooks')) {
            
            while (false !== ($entry = readdir($handle))) {
                if(substr($entry,strlen($entry)-4,strlen($entry)) == ".php"){
                    $file[] = require_once(dirname(__FILE__,2) . '/Hooks/' . $entry);
                }
            }

            closedir($handle);

        }

        return $file;

    }

    private function send($method,$resource,$request = []) {

		$endpoint = $this->url . $resource;

		$headers = [
            "Accept: application/json",
			"Content-type: application/json",
            "User-Agent: MMHospedagem/{$this->versao}",
            "X-API-KEY: " . $this->apikey
		];

		$curl = curl_init();

        curl_setopt_array($curl,[
        	CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        switch ($method) {

        	case "POST":
        	case "PUT":

        		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));
        		break;

        }

        $response = curl_exec($curl);
        curl_close($curl);

        $send = json_decode($response,true);

        if(($this->debug == "on")) {
            $this->sys_debug($send);
        }
		
       	return $send;

	}

    public function send_github() {

        $endpoint = "https://api.github.com/repos/mmhospedagem/pagou_pix/releases/latest";

		$headers = [
            "Accept: application/json",
			"Content-type: application/json",
            "User-Agent: MMHospedagem-Client"
		];

		$curl = curl_init();

        curl_setopt_array($curl,[
        	CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

       	return json_decode($response, true);

    }

    public function Logs($log) {

        return Capsule::table('pagou_pix_logs')->insert([
            "log" => $log,
            "data" => date("Y-m-d H:i:sa")
        ]);

    }

    public function get_transation_id($invoice,$tipo) {

        $capitalLetters = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $numbers = (((date('Ymd') / 12) * 24) + mt_rand(800, 9999));
        $numbers .= 1234567890;
        $characters = $capitalLetters.$numbers;
        $transationID = substr("PAGOU_PIX_{$tipo}_" . str_shuffle($characters) . $invoice, 0, 28);
    
        return $transationID;
 
    }

    private function formatarValorParaAPI($valor) {
        return round(floatval(preg_replace('/[^0-9.]/', '', $valor)), 2);
    }

    // Gestão de cobranças

    public function criar_cobranca($invoice) {

        $id_transacao = $this->get_transation_id($invoice,"EMITIDO");

        $fatura = localAPI("GetInvoice", [
            "invoiceid" => (int)$invoice
        ]);

        if(($fatura["status"] == "error")) {
            $this->Logs($fatura["message"]);
        }

        if(($fatura["result"] == "success")) {

            if(($fatura["total"] < 5.00)) {
                $this->Logs("[Fatura: {$invoice}] Não é possível utilizar esta forma de pagamento. O valor mínimo permitido é de R$5.00 reais.");
                return '';
            }

            $cliente = localAPI("GetClientsDetails", [
                "clientid" => (int)$fatura["userid"]
            ]);

            foreach (Capsule::table('tblcustomfieldsvalues')->where(['fieldid' => $this->origem_cpfcnpj, 'relid' => $fatura["userid"]])->get() as $dados) {
                $cpfcnpj = preg_replace('/\D/', '', $dados->value);
            }

            if(empty($cpfcnpj)) {
                $this->Logs("[Fatura: {$invoice}] Documento CPF ou CNPJ do cliente não informado.");
                return '';
            }

            // Valor da fatura
            $request["amount"] = $this->formatarValorParaAPI($fatura["total"]);

            // Descricão
            $request["description"] = "Pagamento da fatura de número {$invoice}";

            // dados do cliente
            $request["payer"]["name"] = $cliente["fullname"];
            $request["payer"]["document"] = $cpfcnpj;

            // webhook
            $request["notification_url"] = rtrim(\App::getSystemUrl(),"/") . "/modules/gateways/pagou_pix/MMHospedagem/Webhook/index.php?token={$this->tokenSeguranca}";

            // Vencimento
            $request["due_date"] = $fatura["duedate"];
            $request["expiration"] = (int)$this->carencia_pagamento;
    
            // Desconto
            if(($this->desconto == "on")) {
                $request["discount"]["amount"] = $this->formatarValorParaAPI($this->desconto_valor);
                $request["discount"]["type"] = $this->desconto_tipo;
                $request["discount"]["limit_date"] = $fatura["duedate"];
            }

            // Multa
            if(($this->multa == "on")) {
                $request["fine"]["amount"] = $this->formatarValorParaAPI($this->multa_valor);
                $request["fine"]["type"] = $this->multa_tipo;
            }

            // Juros
            if(($this->juros == "on")) {
                $request["interest"]["amount"] = $this->formatarValorParaAPI($this->juros_valor);
                $request["interest"]["type"] = $this->juros_tipo;
            }

            $send = $this->send("POST","/v1/pix/due",$request);

            if(!empty($send["error"])) {
                
                $this->Logs("[Fatura: {$invoice}] {$send["error"]}");

                if((!empty($send["errors"]))) {
                    foreach ($send["errors"] as $erro) {
                        $this->Logs("[Fatura: {$invoice}] {$erro}");
                    }
                }

                return '';

            }

            $dateTime = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            $dataAtual = $dateTime->format('Y-m-d H:i:s');

            Capsule::table('pagou_pix_cobrancas')->insert([
                "invoice" => $invoice,
                "txid" => $send["id"],
                "location" => $send["payload"]["data"],
                "json" => base64_encode(json_encode($send)),
                "status" => "ABERTO",
                "data" => $dataAtual
            ]);

            localAPI('AddTransaction', [
                'paymentmethod' => 'pagou_pix',
                'invoiceid' => $invoice,
                'transid' => $id_transacao,
                'date' => date('d/m/Y'),
                'description' => $id_transacao
            ]);

            $this->Logs("[Fatura: {$invoice}] PIX Emitido com sucesso.");

            return $send;

        }

    }

    public function consultar_cobranca($txid) {
        return $this->send("GET","/v1/pix/{$txid}");
    }

    public function cancelar_cobranca($invoice) {
        
        $cobranca = Capsule::table('pagou_pix_cobrancas')->where(["invoice" => $invoice, "status" => "ABERTO"])->orderBy("id", "desc")->limit(1)->first();

        $id_transacao = $this->get_transation_id($invoice,"CANCELADO");

        $fatura = localAPI("GetInvoice", [
            "invoiceid" => (int)$invoice
        ]);

        if(($fatura["status"] == "error")) {
            $this->Logs($fatura["message"]);
        }

        if(($fatura["result"] == "success")) {

            $send = $this->send("DELETE","/v1/pix/{$cobranca->id}");

            if(!empty($send["error"])) {
                $this->Logs("[Fatura: {$invoice}] {$send["error"]}");
                return '';
            }

            Capsule::table('pagou_pix_cobrancas')->where(["invoice" => $invoice])->orderBy("id", "desc")->limit(1)->delete();

            localAPI('AddTransaction', [
                'paymentmethod' => 'pagou_pix',
                'invoiceid' => $invoice,
                'transid' => $id_transacao,
                'date' => date('d/m/Y'),
                'description' => $id_transacao
            ]);

        }

    }

    public function reembolsar_cobranca($txid,$valor) {

        $cobranca = Capsule::table('pagou_pix_cobrancas')->where(["end_txid" => $invoice, "status" => "PAGO"])->orderBy("id", "desc")->limit(1)->first();

        if((!empty($cobranca->location))) {

            $dateTime = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            $dataAtual = $dateTime->format('Y-m-d H:i:s');

            /*
            reason:
            RefundReasonBankError = 1
            RefundReasonFraudSuspect = 2
            RefundReasonRequestByCustomer = 3
            RefundReasonPixError = 4
            */

            $request = [
                "amount" => $this->formatarValorParaAPI($valor),
                "description" => "Fatura {$invoice} reembolsada por solicitação do ADMINISTRADOR.",
                "reason" => (int)3
            ];

            $send = $this->send("DELETE","/v1/pix/$txid}/refund",$request);

            Capsule::table('pagou_pix_cobrancas')->where(["invoice" => $invoice, "status" => "PAGO"])->orderBy("id", "desc")->update([
                "status" => "REEMBOLSADO",
                "update_up" => $dataAtual
            ]);

            return $send;

        }        

    }

}