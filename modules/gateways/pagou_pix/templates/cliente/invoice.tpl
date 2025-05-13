<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{if $copiar_colar_pix != ""}
    <div class="container-fluid invoice-container">
        <div class="row invoice-header">
            <div class="d-flex col-12">

                <div class="col-4">
                    
                    <div id="div_pix">
                        <img src="{$url_qrcode}">
                    </div>

                    <script>
                        $(document).ready(function() {
                            let fechData = function() {
                                $.post("./modules/gateways/pagou_pix/funcoes.php", {
                                    acao: "recarregarPIX",
                                    invoice: "{$numero_fatura}"
                                },
                                function(data,status){
                                    $("#div_pix").html(data);
                                });
                            }
                            setInterval(fechData, 5000);
                        });
                    </script>

                </div>

                <div class="col-8">
                    
                    <img src="../modules/gateways/pagou_pix/templates/admin/imagens/logo.svg" style="width: 123px; margin-bottom: 16px;">
                    <h4><span style="font-weight: 100 !important;">Pagamento Seguro com</span> Pagou</h4>
                    <p class="small-text mb-0">Sua transação está totalmente protegida pela Pagou. Utilizamos tecnologia de criptografia avançada para garantir que seus dados pessoais e financeiros permaneçam seguros durante todo o processo. Pague com tranquilidade, sabendo que você está utilizando um ambiente confiável e protegido.</p>
                    
                </div>

            </div>
            <div class="col-12" style="margin-top: 15px;">
                
                <input style="border-radius: 25px;" class="form-control w-100" type="text" value="{$copiar_colar_pix}" id="ChavePix" readonly="readonly" onclick="Copiar_ChavePIX();">

                <div style="display: none; text-align: center; padding: 10px; margin: 2px 0px; border-radius: 5px; background-color: #5cb85c; color: #FFF;" id="Alerta_ChavePIX">Chave Copiar e Colar copiada com sucesso!</div>
                
                <script>
                    function Copiar_ChavePIX() {

                        var copyText = document.getElementById("ChavePix");
                        copyText.select();
                        copyText.setSelectionRange(0, 99999)
                        document.execCommand("copy");
                        $("#Alerta_ChavePIX").show();
                        setTimeout(function () {
                            $("#Alerta_ChavePIX").hide();
                        }, 5000);
                    }
                </script>
                
            </div>
        </div>
    </div>
{/if}