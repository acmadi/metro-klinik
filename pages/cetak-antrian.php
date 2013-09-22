<?php
include_once '../models/transaksi.php';
include_once '../models/cetak-transaksi.php';
include_once '../inc/functions.php';

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
<?php
$attr = cetak_no_antri($_GET['id_daftar']);
?>
<br/>
<table width="100%">
    <tr><td width="30%">No. RM:</td><td><?= $attr->id_pelanggan ?></td></tr>
    <tr><td>Nama Pasien:</td><td><?= $attr->nama ?></td></tr>
    <tr><td>Layanan:</td><td><?= $attr->layanan ?></td></tr>
    <tr><td>No. Antrian:</td><td style="font-size: 40px;"><?= $attr->no_antri ?></td></tr>
</table>
</body>