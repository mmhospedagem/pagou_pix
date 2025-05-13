# Gateway Pagou - Pix

O módulo PIX permite o recebimento de pagamentos via QrCode, Chave PIX ou Código PIX no WHMCS, seu cliente poderá efetuar o pagamento em segundos de forma prática e rápida e o melhor com retorno automático.

# Adicionar QRCode na Fatura

- Para adicionar o QRCode no invoicepdf.tpl adicione o codigo abaixo onde voce deseja exibir o QRCode

- Na linha 2 do arquivo invoicepdf.tpl adicione

```
use Carbon\Carbon;
use WHMCS\Database\Capsule;
use WHMCS\Config\Setting;
```

- No final do arquivo invoicepdf.tp adicione

```
$ExistePix = Capsule::table('pagou_pix_cobrancas')->where(['invoice' => $invoicenum])->orderBy("id", "desc")->limit(1)->first();

if(($ExistePix->location != "") && ($ExistePix->location != null)) {

    if ( ($status == 'Unpaid') ) {

        $pdf->Ln(10);
        
        $tblpix = '<div style="padding: 10px; background-color: #FFF; color: #333; font-size: 12px; text-align: left; text-align: center;">
            <img style="width: 130px; padding: 0; margin: 0;" src="' . Setting::getValue('SystemURL') . "/pagou_pix.php?metodo=pix&id=" . $invoicenum . '">
        </div>';

        $pdf->writeHTML($tblpix, true, false, false, false, '');

    }

}
```