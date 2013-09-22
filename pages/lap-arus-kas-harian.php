<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
$(function() {
    $( document ).tooltip();
});
</script>
<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="2%">No.</th>
        <th width="10%">Waktu</th>
        <th width="10%">Transaksi</th>
        <th width="10%">Awal</th>
        <th width="10%">Masuk</th>
        <th width="10%">Keluar</th>
        <th width="10%">Sisa</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'transaksi' => $_GET['jenis_transaksi']
    );
    $arus_kas = arus_kas_harian_load_data($param);
    $list_data = $arus_kas['data'];
    //$total_data= $penerimaan['total'];
    $total_masuk=0;
    $total_keluar = 0;
    foreach ($list_data as $key => $data) { 
        $awal = mysql_fetch_object(mysql_query("select sum(masuk)-sum(keluar) as awal from arus_kas where waktu < '".$data->waktu."'"));
        $sisa = mysql_fetch_object(mysql_query("select sum(masuk)-sum(keluar) as sisa from arus_kas where waktu <= '".$data->waktu."'"));
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= (++$key) ?></td>
            <td align="center"><?= datetimefmysql($data->waktu,'time') ?></td>
            <td><?= $data->transaksi ?></td>
            <td align="right"><?= rupiah($awal->awal) ?></td>
            <td align="right"><?= rupiah($data->masuk) ?></td>
            <td align="right"><?= rupiah($data->keluar) ?></td>
            <td align="right"><?= rupiah($sisa->sisa) ?></td>
            
        </tr>
    <?php 
    $total_masuk = $total_masuk+$data->masuk;
    $total_keluar= $total_keluar+$data->keluar;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="4" align="right"><b>TOTAL</b></td>
        <td align="right"><b><?= rupiah($total_masuk) ?></b></td>
        <td align="right"><b><?= rupiah($total_keluar) ?></b></td>
        <td align="right"><b><?= rupiah($total_masuk-$total_keluar) ?></b></td>
    </tr>
</tfoot>
</table>