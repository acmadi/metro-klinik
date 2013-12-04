<?php
include_once '../models/transaksi.php';
include_once '../models/masterdata.php';
include_once '../inc/functions.php';

$apt = apotek_atribute();
$attr= nota_billing_load_data($_GET['id_billing']);
foreach ($attr['atribute'] as $rows);
?>
<title>Nota</title>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body onload="cetak();">
<div class="layout-print-struk">
    <table style="border-bottom: 1px solid #000;" width="100%">
        <tr><td align="center" style="text-transform: uppercase; font-size: 12px;"><?= $apt->nama ?></td> </tr>
        <tr><td align="center" style="font-size: 12px;"><?= $apt->alamat ?></td> </tr>
        <tr><td align="center" style="font-size: 12px;">Telp. <?= $apt->telp ?></td> </tr>
    </table>
    <table width="100%" style="border-bottom: 1px solid #000;">
        <tr><td width="40%">Nomor:</td><td><?= $rows->id ?></td></tr>
        <tr><td>Waktu:</td><td style="white-space: nowrap"><?= datetimefmysql($rows->waktu, TRUE) ?></td></tr>
        <tr><td>Pelanggan:</td><td style="white-space: nowrap"><?= $rows->no_rm ?> / <?= $rows->pelanggan ?></td></tr>
    </table>
    <table width="100%" style="border-bottom: 1px solid #000;">
        <tr>
            <th>Nama Barang</th>
            <th>Qty</th>
            <th align="right">Harga</th>
            <th>Subtotal</th>
        </tr>
        <?php 
        $total_brg = 0;
        foreach ($attr['list_barang'] as $key => $data) { 
            $total_brg = $total_brg + ($data->harga_jual*$data->qty);
            ?>
        <tr>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->qty ?></td>
            <td align="right"><?= rupiah($data->harga_jual) ?></td>
            <td align="right"><?= rupiah($data->qty*$data->harga_jual) ?></td>
        </tr>
        <?php 
        } ?>
        <tr><td colspan="3"><b>Total Barang:</b></td><td align="right"><b><?= rupiah($total_brg) ?></b></td></tr>
    </table>
    
    <table width="100%" style="border-bottom: 1px solid #000;">
        <tr>
            <th>Nama Layanan</th>
            <th>Qty</th>
            <th align="right">Harga</th>
            <th>Subtotal</th>
        </tr>
        <?php 
        $total_jasa = 0;
        foreach ($attr['list_jasa'] as $key => $data) { 
            $total_jasa = $total_jasa + ($data->nominal*$data->frek);
            ?>
        <tr valign="top">
            <td><?= $data->nama ?></td>
            <td align="center"><?= $data->frek ?></td>
            <td align="right"><?= rupiah($data->nominal) ?></td>
            <td align="right"><?= rupiah($data->nominal*$data->frek) ?></td>
        </tr>
        <?php 
        } ?>
        <tr><td colspan="3"><b>Total Jasa:</b></td><td align="right"><b><?= rupiah($total_jasa) ?></b></td></tr>
    </table>
    <?php
    
    $diskon = ($rows->total*($rows->diskon/100));
    $kembali = $rows->uang_serah-(($total_brg+$total_jasa)-$diskon);
    if ($kembali <= 0) {
        $kembalian = '0';
    } else {
        $kembalian = $kembali;
    }
    ?>
    <table width="100%">
        <tr><td>Sub Total:</td><td align="right"><?= rupiah($total_brg+$total_jasa) ?></td></tr>
        <tr><td><b>DISKON PROMO <?= $rows->diskon ?> %</b>:</td><td align="right"><?= rupiah($diskon) ?></td></tr>
        <tr><td>Total:</td><td align="right"><?= rupiah(($total_brg+$total_jasa)-$diskon) ?></td></tr>
        <tr><td>Bayar:</td><td align="right"><?= rupiah($rows->uang_serah) ?></td></tr>
        <tr><td>Kembali:</td><td align="right"><?= rupiah($kembalian) ?></td></tr>
        
    </table>
    <br/>
    <center style="border-top: 1px solid #000; border-bottom: 1px solid #000;">
        TAMPIL MEMPESONA<br/>DENGAN WAJAH DAN GIGI SEHAT<br/>TERIMA KASIH
    </center>
</div>
</body>