<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-chevron-circle-right"></i> {$nome_modulo}</li>
        <li class="breadcrumb-item active" aria-current="page">Configurações</li>
    </ol>
</nav>

<div class="panel panel-default" style="margin: 0px;">

    <div class="panel-heading">
        Configurações do Sistema
    </div>

    <div class="panel-body" style="width: 100%;">
        <div class="row" style="width: 100%; margin: 0;">
            <div class="col-md-8" style="width: 100%;">

                <fieldset id="pagou_pix_config">

                    <input type="hidden" name="pagou_pix_acao" value="Configuracoes_Gateway">

                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        <label for="debug" class="col-sm-2 col-form-label">Debug</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">

                            <label class="switch">
                                <input type="checkbox" name="debug" id="debug" {if $debug == "on"}checked{/if}>
                                <span class="slider round" style="height: 34px; width: 61px;"></span>
                            </label>

                        </div>
                    </div>

                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        <label for="sandbox" class="col-sm-2 col-form-label">Sandbox</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" name="sandbox" id="sandbox" {if $sandbox == "on"}checked{/if}>
                                <span class="slider round" style="height: 34px; width: 61px;"></span>
                            </label>
                            <br>
                            <small id="emailHelp" class="form-text text-muted">
                                Ao ativar esta opção, os PIX gerados serão emitidos em ambiente de testes (modo sandbox), sem transações financeiras reais.
                            </small>
                        </div>
                    </div>

                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        <label for="apikey" class="col-sm-2 col-form-label">API Key</label>
                        <div class="col-sm-10">
                            <input type="text" name="apikey" id="apikey" class="form-control" value="{$apikey}" required>
                            <small id="emailHelp" class="form-text text-muted">
                                Para gerar sua <strong>Chave de API (ApiKey)</strong>, <a class="badge badge-secondary" href="https://app.pagou.com.br/configuracoes/api/" target="_blank">clique aqui</a> e acesse a área de configurações da sua conta.
                            </small>
                        </div>
                    </div>

                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        <label for="origem_cpfcnpj" class="col-sm-2 col-form-label">Origem CPF / CNPJ</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">

                            <label class="form-control" style="height: auto;">
                                <select name="origem_cpfcnpj" id="origem_cpfcnpj" style="border: none; font-weight: 300;">
                                    {$origem_cpfcnpj['cpfcnpj']}
                                </select>
                            </label>

                        </div>
                    </div>

                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        <label for="limite_pagamento" class="col-sm-2 col-form-label">Dias Limite</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">

                            <label class="form-control" style="height: auto;">
                                <select name="limite_pagamento" id="limite_pagamento" style="border: none; font-weight: 300;" required>
                                    <option value="1" {if $carencia_pagamento == "1"}selected{/if}>1 Dia</option>
                                    <option value="2" {if $carencia_pagamento == "2"}selected{/if}>2 Dias</option>
                                    <option value="3" {if $carencia_pagamento == "3"}selected{/if}>3 Dias</option>
                                    <option value="4" {if $carencia_pagamento == "4"}selected{/if}>4 Dias</option>
                                    <option value="5" {if $carencia_pagamento == "5"}selected{/if}>5 Dias</option>
                                    <option value="6" {if $carencia_pagamento == "6"}selected{/if}>6 Dias</option>
                                    <option value="7" {if $carencia_pagamento == "7"}selected{/if}>7 Dias</option>
                                    <option value="8" {if $carencia_pagamento == "8"}selected{/if}>8 Dias</option>
                                    <option value="9" {if $carencia_pagamento == "9"}selected{/if}>9 Dias</option>
                                    <option value="10" {if $carencia_pagamento == "10"}selected{/if}>10 Dias</option>
                                    <option value="11" {if $carencia_pagamento == "11"}selected{/if}>11 Dias</option>
                                    <option value="12" {if $carencia_pagamento == "12"}selected{/if}>12 Dias</option>
                                    <option value="13" {if $carencia_pagamento == "13"}selected{/if}>13 Dias</option>
                                    <option value="14" {if $carencia_pagamento == "14"}selected{/if}>14 Dias</option>
                                    <option value="15" {if $carencia_pagamento == "15"}selected{/if}>15 Dias</option>
                                    <option value="16" {if $carencia_pagamento == "16"}selected{/if}>16 Dias</option>
                                    <option value="17" {if $carencia_pagamento == "17"}selected{/if}>17 Dias</option>
                                    <option value="18" {if $carencia_pagamento == "18"}selected{/if}>18 Dias</option>
                                    <option value="19" {if $carencia_pagamento == "19"}selected{/if}>19 Dias</option>
                                    <option value="20" {if $carencia_pagamento == "20"}selected{/if}>20 Dias</option>
                                    <option value="21" {if $carencia_pagamento == "21"}selected{/if}>21 Dias</option>
                                    <option value="22" {if $carencia_pagamento == "22"}selected{/if}>22 Dias</option>
                                    <option value="23" {if $carencia_pagamento == "23"}selected{/if}>23 Dias</option>
                                    <option value="24" {if $carencia_pagamento == "24"}selected{/if}>24 Dias</option>
                                    <option value="25" {if $carencia_pagamento == "25"}selected{/if}>25 Dias</option>
                                    <option value="26" {if $carencia_pagamento == "26"}selected{/if}>26 Dias</option>
                                    <option value="27" {if $carencia_pagamento == "27"}selected{/if}>27 Dias</option>
                                    <option value="28" {if $carencia_pagamento == "28"}selected{/if}>28 Dias</option>
                                    <option value="29" {if $carencia_pagamento == "29"}selected{/if}>29 Dias</option>
                                    <option value="30" {if $carencia_pagamento == "30"}selected{/if}>30 Dias</option>
                                </select>
                            </label>

                            <small class="form-text text-muted">
                                O limite será contado a partir da data de vencimento da fatura. Por exemplo, se a fatura vencer no dia <strong>01/01/2050</strong> e o limite definido for de <strong>2 dias</strong>, o cliente poderá pagar até <strong>03/01/2050</strong>. Após esse período, o QR Code PIX será automaticamente expirado e um novo código deverá ser gerado.
                            </small>

                        </div>
                    </div>

                    <!-- Desconto -->
                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        
                        <label for="desconto" class="col-sm-2 col-form-label">Desconto</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" name="desconto" id="desconto" {if $desconto == "on"}checked{/if}>
                                <span class="slider round" style="height: 34px; width: 61px;"></span>
                            </label>
                            <br>
                            <small id="emailHelp" class="form-text text-muted">
                                Ao ativar esta opção, habilita um desconto automático sobre o valor total do PIX, conforme o tipo e valor definidos.
                            </small>
                        </div>       

                        <div id="div_desconto" style="display: none; background-color: #f1f1f1; border-radius: 5px;">

                            <label for="desconto_tipo" class="col-sm-2 col-form-label">Tipo</label>
                            <!-- Rounded switch -->
                            <div class="col-sm-10">

                                <label class="form-control" style="height: auto;">
                                    <select name="desconto_tipo" id="desconto_tipo" style="border: none; font-weight: 300;">
                                        <option value="fixed" {if $desconto_tipo == "fixed"}selected{/if}>Fixo</option>
                                        <option value="percentage" {if $desconto_tipo == "percentage"}selected{/if}>Porcentagem</option>
                                    </select>
                                </label>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Fixo</td>
                                            <td>Um valor em reais (R$).</td>
                                        </tr>
                                        <tr>
                                            <td>Porcentagem</td>
                                            <td>Um percentual (%) sobre o valor total do PIX.</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        
                            <label for="desconto_valor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                                <input type="text" name="desconto_valor" id="desconto_valor" class="form-control" value="{$desconto_valor}">
                            </div>

                        </div>
                    </div>

                    <!-- Multa -->
                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        
                        <label for="multa" class="col-sm-2 col-form-label">Multa</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" name="multa" id="multa" {if $multa == "on"}checked{/if}>
                                <span class="slider round" style="height: 34px; width: 61px;"></span>
                            </label>
                            <br>
                            <small id="emailHelp" class="form-text text-muted">
                                Ao ativar esta opção, aplica uma multa ao valor do PIX em caso de pagamento após o vencimento, conforme as regras definidas.
                            </small>
                        </div>       

                        <div id="div_multa" style="display: none;">

                            <label for="multa_tipo" class="col-sm-2 col-form-label">Tipo</label>
                            <!-- Rounded switch -->
                            <div class="col-sm-10">

                                <label class="form-control" style="height: auto;">
                                    <select name="multa_tipo" id="multa_tipo" style="border: none; font-weight: 300;">
                                        <option value="fixed" {if $multa_tipo == "fixed"}selected{/if}>Fixo</option>
                                        <option value="percentage" {if $multa_tipo == "percentage"}selected{/if}>Porcentagem</option>
                                    </select>
                                </label>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Fixo</td>
                                            <td>Valor fixo em reais (R$).</td>
                                        </tr>
                                        <tr>
                                            <td>Porcentagem</td>
                                            <td>Percentual (%) calculado sobre o valor total do PIX.</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        
                            <label for="multa_valor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                                <input type="text" name="multa_valor" id="multa_valor" class="form-control" value="{$multa_valor}">
                            </div>

                        </div>
                    </div>

                    <!-- Juros -->
                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        
                        <label for="juros" class="col-sm-2 col-form-label">Juros</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" name="juros" id="juros" {if $juros == "on"}checked{/if}>
                                <span class="slider round" style="height: 34px; width: 61px;"></span>
                            </label>
                            <br>
                            <small id="emailHelp" class="form-text text-muted">
                                Ao ativar esta opção, aplica juros diários ao valor do PIX em caso de atraso, conforme o tipo e valor configurados.
                            </small>
                        </div>       

                        <div id="div_juros" style="display: none;">

                            <label for="juros_tipo" class="col-sm-2 col-form-label">Tipo</label>
                            <!-- Rounded switch -->
                            <div class="col-sm-10">

                                <label class="form-control" style="height: auto;">
                                    <select name="juros_tipo" id="juros_tipo" style="border: none; font-weight: 300;">
                                        <option value="fixed" {if $juros_tipo == "fixed"}selected{/if}>Fixo</option>
                                        <option value="percentage_month_calendar_days" {if $juros_tipo == "percentage_month_calendar_days"}selected{/if}>Porcentagem</option>
                                    </select>
                                </label>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Tipo</th>
                                            <th scope="col">Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Fixo</td>
                                            <td>Valor fixo em reais (R$).</td>
                                        </tr>
                                        <tr>
                                            <td>Porcentagem</td>
                                            <td>Percentual (%) calculado sobre o valor total do PIX.</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        
                            <label for="juros_valor" class="col-sm-2 col-form-label">Valor</label>
                            <div class="col-sm-10">
                                <input type="text" name="juros_valor" id="juros_valor" class="form-control" value="{$juros_valor}">
                            </div>

                        </div>
                    </div>

                    <div class="form-group row" style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-top: -2px;">
                        <label for="emitir_all" class="col-sm-2 col-form-label">Emitir Pix</label>
                        <!-- Rounded switch -->
                        <div class="col-sm-10">
                            <label class="switch">
                                <input type="checkbox" name="emitir_all" id="emitir_all" {if $emitir_pix == "on"}checked{/if}>
                                <span class="slider round" style="height: 34px; width: 61px;"></span>
                            </label>
                            <br>
                            <small id="emailHelp" class="form-text text-muted">
                                Ao ativar esta opção, os PIX serão emitidos para todos os clientes independente da forma de pagamento definida no serviço.
                            </small>
                        </div>
                    </div>
                    
                    <button class="btn btn-success btn-sm" id="salvar_config_pagou_pix" type="button">
                        Salvar alterações
                    </button>

                    <div id="pagou_pix_conteudo_config"></div>

                    <script type="text/javascript">

                        $(document).ready(function () {
                            var iconCarregando = $('<span class="destaque"><center><img src="../modules/gateways/pagou_pix/templates/admin/imagens/loading.svg" style="width: 51px; height: 51px;"></center></span>');

                            $("#salvar_config_pagou_pix").on('click', function () {
                                var dados = $("#pagou_pix_config").find("input, select").serialize(); // ← sem FormData

                                $.ajax({
                                    url: "../modules/gateways/pagou_pix/funcoes.php",
                                    type: "POST",
                                    data: dados,
                                    beforeSend: function () {
                                        $('#pagou_pix_conteudo_config').html(iconCarregando);
                                    },
                                    complete: function () {
                                        $(iconCarregando).remove();
                                    },
                                    success: function (data) {
                                        $('#pagou_pix_conteudo_config').html('<p>' + data + '</p>');
                                    },
                                    error: function (xhr, er) {
                                        $('#pagou_pix_conteudo_config').html('<p class="destaque">Erro ' + xhr.status + ' - ' + xhr.statusText + '<br />Tipo: ' + er + '</p>');
                                    }
                                });
                            });
                        });

                        $(document).ready(function () {
                            function toggleFields(checkboxId, divId) {
                                if ($('#' + checkboxId).is(':checked')) {
                                    $('#' + divId).show();
                                } else {
                                    $('#' + divId).hide();
                                }
                            }
                            
                            toggleFields('desconto', 'div_desconto');
                            toggleFields('multa', 'div_multa');
                            toggleFields('juros', 'div_juros');

                            $('#desconto').on('change', function () {
                                toggleFields('desconto', 'div_desconto');
                            });

                            $('#multa').on('change', function () {
                                toggleFields('multa', 'div_multa');
                            });

                            $('#juros').on('change', function () {
                                toggleFields('juros', 'div_juros');
                            });
                        });
                            
                    </script>

                </fieldset>

            </div>
        </div>
    </div>

</div>