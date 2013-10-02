<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
header_surat();
?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body onload="cetak();">
<h1>
    LAPORAN PEMESANAN <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
    <tr class="italic">
        <th width="5%">No.</th>
        <th width="10%">No. SP</th>
        <th width="10%">Tanggal</th>
        <th width="20%">Nama Supplier</th>
        <th width="15%">Karyawan</th>
        <th width="25%">Nama Barang</th>
        <th width="5%">Jumlah</th>
        <th width="5%">Kemasan</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'id' => '',
        'id_supplier' => $_GET['id_supplier']
    );
    $pemesanan = pemesanan_load_data($param);
    $list_data = $pemesanan['data'];
    $total_data= $pemesanan['total'];
    $nomor_sp = "";
    $no = 1;
    foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= ($nomor_sp !== $data->id)?($no):NULL ?></td>
            <td><?= ($nomor_sp !== $data->id)?$data->id:NULL ?></td>
            <td align="center"><?= ($nomor_sp !== $data->id)?datetimefmysql($data->tanggal):NULL ?></td>
            <td><?= ($nomor_sp !== $data->id)?$data->supplier:NULL ?></td>
            <td><?= ($nomor_sp !== $data->id)?$data->karyawan:NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td align="center"><?= $data->kemasan ?></td>
        </tr>
    <?php 
    if ($nomor_sp !== $data->id) {
        $no++;
    }
    $nomor_sp = $data->id;
    }
    ?>
</tbody>
</table>
</body>