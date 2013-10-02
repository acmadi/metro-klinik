<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';

?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body onload="cetak();">
<?php header_surat(); ?>
<h1>
    LAPORAN RESEP <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="5%">No.<br/>Resep</th>
    <th width="5%">Tanggal</th>
    <th width="3%">ID</th>
    <th width="15%">Pasien</th>
    <th width="15%">Dokter</th>
    <th width="3%">No.R /</th>
    <th width="10%">Apoteker</th>
    <th width="5%">Jasa</th>
    <th width="20%">Nama Barang</th>
    <th width="3%">Dosis <br/> Racik</th>
    <th width="4%">Jumlah<br/> Pakai</th>
    <th width="5%">Harga<br/>Barang</th>
</tr>
</thead>
<tbody>
    <?php 
    $param = array(
        'id' => '',
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'pasien' => $_GET['id_pasien'],
        'dokter' => $_GET['id_dokter']
    );
    $list_data = load_data_resep($param);
    $master_resep = $list_data['data'];
    $id_resep = "";
    $jasa = "";
    $no = 1;
    foreach ($master_resep as $key => $data) { 
        ?>
    <tr class="<?= ($data->id_resep !== $id_resep)?'odd':'even' ?>">
        <td align="center"><?= ($data->id_resep !== $id_resep)?($no):NULL ?></td>
        <td align="center"><?= ($data->id_resep !== $id_resep)?$data->id_resep:NULL ?></td>
        <td align="center"><?= ($data->id_resep !== $id_resep)?datetimefmysql($data->waktu):NULL ?></td>
        <td align="center"><?= ($data->id_resep !== $id_resep)?$data->id_pasien:NULL ?></td>
        <td><?= ($data->id_resep !== $id_resep)?$data->pasien:NULL ?></td>
        <td><?= ($data->id_resep !== $id_resep)?$data->dokter:NULL ?></td>
        <td align="center"><?= (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa))?$data->r_no:NULL ?></td>
        <td><?= (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa))?$data->apoteker:NULL ?></td>
        <td align="right"><?= (($data->id_resep !== $id_resep) or ($data->r_no !== $jasa))?rupiah($data->nominal):NULL ?></td>
        <td><?= $data->nama_barang ?></td>
        <td align="center"><?= $data->dosis_racik ?></td>
        <td align="center"><?= $data->jumlah_pakai ?></td>
        <td align="right"><?= rupiah($data->jual_harga) ?></td>
    </tr>
    <?php 
    $jasa = $data->r_no;
    if ($data->id_resep !== $id_resep) {
        $no++;
    }
    $id_resep = $data->id_resep;
    } ?>
</tbody>
</table>
</body>