<?php
include_once '../models/transaksi.php';
include_once '../models/cetak-transaksi.php';
include_once '../inc/functions.php';
$attr_array = print_pemesanan($_GET['id']);
$label = get_bottom_label();
$apa   = get_apa_from_karyawan();
foreach ($attr_array as $attr);
?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
window.onunload = refreshParent;
function refreshParent() {
    window.opener.location.reload();
}
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body onload="cetak();" class="default-printing">
<?= header_surat() ?>
<br/>
<table>
    <tr><td><?= $attr->id ?></td></tr>
    <tr><td>Kepada:</td></tr>
    <tr><td>Yth. <?= $attr->supplier ?></td></tr>
    <tr><td><?= $attr->alamat_supplier ?></td></tr>
    <tr><td></td></tr>
</table>
<br/>
Dengan Hormat,<br/>
Mohon dikirim obat-obatan untuk keperluan Apotek kami sebagai berikut:
<br/><br/>
<table width="100%">
    <tr><th width="5%">No.</th><th width="70%" align="left">Nama Barang</th><th width="25%">Jumlah</th></tr>
<?php foreach ($attr_array as $key => $data) { ?>
    <tr><td align="center"><?= ++$key ?></td><td><?= $data->nama_barang ?></td><td align="center"><?= $data->jumlah ?></td></tr>
<?php } ?>
</table>
<br/>
<br/>
<table width="100%" align="right">
    <tr><td align="right"><?= $label->kota ?>, <?= indo_tgl(date("Y-m-d")) ?></td></tr>
    <tr><td align="right">&nbsp;</td></tr>
    <tr><td align="right">&nbsp;</td></tr>
    <tr><td align="right"><?= $apa->nama ?></td></tr>
    <tr><td align="right"><?= $apa->no_sipa ?></td></tr>
</table>
</body>