<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-chevron-circle-right"></i> {$nome_modulo}</li>
        <li class="breadcrumb-item active" aria-current="page">Logs do Sistema</li>
    </ol>
</nav>

<div class="panel panel-default" style="margin: 0px;">

    <div class="panel-heading">
        Logs
    </div>

    <div class="panel-body" style="width: 100%;">
        <div class="row" style="width: 100%; margin: 0;">
            <div class="col-md-8" style="width: 100%;">
            
                <fieldset id="post_logs_pagou_pix">

                        <input type="hidden" name="pagou_pix_acao" value="Ver_Logs">
                        
                        <div style="float: left; width: 40%; margin-right: 1%;">
                            <label>Data inicio</label>
                            <input id="data_inicial_logs" class="form-control" name="data_inicial_logs" type="date" required>
                        </div>
                        <div style="float: left; width: 40%; margin-right: 1%;">
                            <label>Data final</label>
                            <input id="data_final_logs" class="form-control" name="data_final_logs" type="date" required>
                        </div>
                        <div style="float: left; padding: 23px 0px;">
                            <button type="button" class="btn btn-success btn-sm" id="ver_logs_pagou_pix">Ver logs</button>
                        </div>

                        <div class="MMHospedagem_Clear_Fix"></div>

                        <div id="pagou_pix_conteudo_logs"></div>

                        <! -- Input sem integração --> 

                        <script type="text/javascript">

                            $(document).ready(function () {
                                var iconCarregando = $('<span class="destaque"><center><img src="../modules/gateways/pagou_pix/templates/admin/imagens/loading.svg" style="width: 51px; height: 51px;"></center></span>');

                                $("#ver_logs_pagou_pix").on('click', function () {
                                    var dados = $("#post_logs_pagou_pix").find("input, select").serialize(); // ← sem FormData

                                    $.ajax({
                                        url: "../modules/gateways/pagou_pix/funcoes.php",
                                        type: "POST",
                                        data: dados,
                                        beforeSend: function () {
                                            $('#pagou_pix_conteudo_logs').html(iconCarregando);
                                        },
                                        complete: function () {
                                            $(iconCarregando).remove();
                                        },
                                        success: function (data) {
                                            $('#pagou_pix_conteudo_logs').html('<p>' + data + '</p>');
                                        },
                                        error: function (xhr, er) {
                                            $('#pagou_pix_conteudo_logs').html('<p class="destaque">Erro ' + xhr.status + ' - ' + xhr.statusText + '<br />Tipo: ' + er + '</p>');
                                        }
                                    });
                                });
                            });                                   
                                
                        </script>

                </fieldset>
            
            </div>
        </div>
    </div>
</div>