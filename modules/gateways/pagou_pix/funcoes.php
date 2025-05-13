<?php

date_default_timezone_set('America/Sao_Paulo');

require_once(dirname(__FILE__,4) . "/init.php");
require_once(dirname(__FILE__) . "/vendor/autoload.php");

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

if((!empty($_POST))) {

    if(($_POST["pagou_pix_acao"] == "Configuracoes_Gateway")) {

        if (!Session::get("adminid")) {
            http_response_code(403);
            exit('Acesso não autorizado');
        }

        try {

            Capsule::table('pagou_pix_config')->where(['Nome_Modulo' => 'pagou_pix'])->update([
                "debug" => $_POST["debug"],
                "sandbox" => $_POST["sandbox"],
                "apikey" => $_POST["apikey"],
                "origem_cpfcnpj" => $_POST["origem_cpfcnpj"],
                "desconto" => $_POST["desconto"],
                "desconto_tipo" => $_POST["desconto_tipo"],
                "desconto_valor" => $_POST["desconto_valor"],
                "juros" => $_POST["juros"],
                "juros_tipo" => $_POST["juros_tipo"],
                "juros_valor" => $_POST["juros_valor"],
                "multa" => $_POST["multa"],
                "multa_tipo" => $_POST["multa_tipo"],
                "multa_valor" => $_POST["multa_valor"],
                "tempolimite" => $_POST["limite_pagamento"],
                "emitir" => $_POST["emitir_all"]
            ]);

            exit('<div class="alert alert-success" role="alert">
                <i class="fad fa-info-circle" style="font-size: 40px; float: left; margin-right: 10px;"></i> Sucesso! <br>Aguarde a pagina esta sendo atualizada...
            </div>
            
            <meta http-equiv="refresh" content="3">'); 
            
        } catch (\Exception $e) {
            
            exit('<div class="alert alert-danger" role="alert">
                <i class="fad fa-exclamation-circle" style="font-size: 40px; float: left; margin-right: 10px;"></i> Ops algo deu errado! <br>Aguarde a pagina esta sendo atualizada...
            </div>
            
            <meta http-equiv="refresh" content="3">');
            
        }

    }

    if(($_POST["pagou_pix_acao"] == "Ver_Logs")) {

        if (!Session::get("adminid")) {
            http_response_code(403);
            exit('Acesso não autorizado');
        }

        echo '<script>
            $(document).ready(function(){
                
                $(\'#TabelaPixLogs\').DataTable({
                    "order": [[ 0, "desc" ]],
                    "className": "mdl-data-table__cell--non-numeric",
                    "language": {
                        "lengthMenu": "Mostrando _MENU_ registros por página",
                        "zeroRecords": "Nada encontrado",
                        "info": "Mostrando página _PAGE_ de _PAGES_",
                        "infoEmpty": "Nenhum registro disponível",
                        "infoFiltered": "(filtrado de _MAX_ registros no total)",
                    }
                });
                
            });
        </script>
        
        <table class="table table-bordered" id="TabelaPixLogs">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Data</th>
                    <th scope="col">Log</th>
                </tr>
            </thead>
            <tbody>';

            foreach (Capsule::table('pagou_pix_logs')->whereDate('data','>=', $_POST["data_inicial_logs"])->whereDate('data','<=', $_POST["data_final_logs"])->get() as $log) {

                $data = $log->data;
                $data = date('d/m/Y \à\s H:i:s', strtotime($data));
            
                echo '<tr>
                    <th scope="row">' . $log->id . '</th>
                    <td>' . $data . '</td>
                    <td>' . $log->log . '</td>
                    
                </tr>';

            }
                
            echo '</tbody>
        </table>';


    }

    if(($_POST["acao"] == "recarregarPIX")) {

        foreach (Capsule::table('pagou_pix_cobrancas')->where(["invoice" => $_POST["invoice"]])->orderBy("id", "desc")->limit(1)->get() as $pix) {

            $json = base64_decode($pix->json);
            $json = json_decode($json, true);

            if(($pix->status != "PAGO")) {

                echo '<img src="data:image/png;base64,' . $json["payload"]["image"] . '">';

            } else {

                echo '<img src="../modules/gateways/pagou_pix/templates/cliente/imagens/success.png">
                
                <meta http-equiv="refresh" content="0;url=./viewinvoice.php?id=' . $pix->invoice . '">';

            }
            
        }

    }

}