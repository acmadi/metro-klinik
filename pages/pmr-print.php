<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<?php
include_once '../models/transaksi.php';
include_once '../models/masterdata.php';
include_once '../inc/functions.php';
$param = array('print' => TRUE,'id' => $_GET['id_pasien']);
$pelanggan = load_data_customer($param);
foreach ($pelanggan['data'] as $rows);
header_surat();
?>
<body onload="cetak();">
<table width="100%">
<tr valign="top">
    <td width="50%" colspan="3">
    <table width="100%" class="attribute">
        <tr><td width="40%">No. RM:</td><td><?= $rows->id ?></td></tr>
        <tr><td width="40%">Nama Pasien:</td><td><?= $rows->nama ?></td></tr>
        <tr><td>Alamat Pasien:</td><td><?= $rows->alamat ?></td></tr>
        <tr><td>No. Telepon:</td><td><?= $rows->telp ?></td></tr>
    </table>
    </td>
    <td width="50%" colspan="9">
    <table width="100%" class="attribute">
        <tr><td width="50%">Jenis Kelamin:</td><td><?= $rows->kelamin ?></td></tr>
        <tr><td>Umur:</td><td><?= hitungUmur($rows->tanggal_lahir) ?></td></tr>
        <tr><td>Gol. Darah:</td><td></td></tr>
    </table>
    </td>
</tr>
</table>
<table cellspacing="0" width="100%" class="list-data-print">
<tr>
    <th>Tanggal</th>
    <th>Status Resep</th>
    <th>Packing Barang</th>
    <th>Kekuatan Obat</th>
    <th>Dosis</th>
    <th>Sediaan</th>
    <th>Cara Pemakaian</th>
    <th>Jumlah</th>
    <th>Dokter</th>
    <th>SIP</th>
    <th>Alamat</th>
    <th>Keterangan</th>
</tr>
<?php
$param = array(
    'id' => '',
    'awal' => '',
    'pasien' => $_GET['id_pasien']
);
$list_data = load_data_resep($param);
foreach ($list_data['data'] as $key => $data) { ?>
<tr valign="top" bgcolor="<?= ($key%2==0)?'#ffffe0':'#ffffff' ?>">
    <td><?= datetimefmysql($data->waktu) ?></td>
    <td align="center"><?= (count($list_data) > 1)?'Lama':'Baru' ?></td>
    <td><?= $data->nama_barang ?></td>
    <td align="center"><?= $data->kekuatan ?></td>
    <td align="center"><?= $data->dosis_racik ?></td>
    <td><?= $data->sediaan ?></td>
    <td align="center"><?= $data->pakai_aturan ?></td>
    <td align="center"><?= $data->resep_r_jumlah ?></td>
    <td><?= $data->dokter ?></td>
    <td><?= $data->sip_no ?></td>
    <td><?= $data->alamat_dokter ?></td>
    <td>...</td>
</tr>
<?php } ?>
</table>
</body>