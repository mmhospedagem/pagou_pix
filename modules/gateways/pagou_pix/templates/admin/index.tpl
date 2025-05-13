<link href="../modules/gateways/pagou_pix/templates/admin/css/mmhospedagem.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans" type="text/css" media="all" />

{if $debug == "on"}
    <div class="alert alert-warning d-flex align-items-start" role="alert">
        <div style="font-size: 1.8em; margin-right: 15px;">
             <h5 class="alert-heading mb-1"><i class="fas fa-exclamation-triangle text-warning"></i> <strong>Modo Debug Ativo</strong></h5>
        </div>
        <div>            
            <p class="mb-2">
                O sistema de debug está ativado. Não é recomendado utilizar este sistema em produção.
            </p>
            <p class="mb-2">
                Os logs de depuração estão disponíveis em:
            </p>
            <input class="form-control font-monospace w-100" disabled value="{$pagou_pix_dir_logs}">
        </div>        
    </div>
{/if}

<div class="topo">
    <img src="../modules/gateways/pagou_pix/templates/admin/imagens/logo.svg" width="100">
</div>

<div class="mmhospedagem">

    <div class="conteudo_left">

        <div class="list-group" id="pagou_MenuLista" role="tablist">
            <a class="list-group-item list-group-item-action" href="#pagou_pix_infor" role="tab" data-toggle="tab" id="tabLink2" data-tab-id="2" aria-expanded="true">
                Informações
            </a>
            <a class="list-group-item list-group-item-action" href="#pagou_pix_config" role="tab" data-toggle="tab" id="tabLink2" data-tab-id="2" aria-expanded="true">
                Configurações
            </a>
            <a class="list-group-item list-group-item-action" href="#pagou_pix_logs" role="tab" data-toggle="tab" id="tabLink2" data-tab-id="2" aria-expanded="true">
                Logs
            </a>
        </div>
    </div>

    <div class="conteudo_right">
        <div class="tab-content">

            <div class="tab-pane active" id="pagou_pix_infor" role="tabpanel">
                {include file="`$mm_template_admin`/paginas/home.tpl"}
            </div>

            <div class="tab-pane" id="pagou_pix_config" role="tabpanel">
                {include file="`$mm_template_admin`/paginas/config.tpl"}
            </div>

            <div class="tab-pane" id="pagou_pix_logs" role="tabpanel">
                {include file="`$mm_template_admin`/paginas/logs.tpl"}
            </div>

        </div>
    </div>

</div>