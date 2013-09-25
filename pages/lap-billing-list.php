<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">

</script>
<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="2%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="30%">Pelanggan</th>
        <th width="10%">Total Tagihan RP.</th>
        <th width="10%">Terbayar RP.</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'pasien' => $_GET['pasien'],
        'status' => $_GET['status']
    );
    $hutang = billing_load_data($param);
    $list_data = $hutang['data'];
    $total = 0;
    $terbayar = 0;
    foreach ($list_data as $key => $data) { 
        $barang = billing_get_total_barang($data->tanggal, $data->id_pelanggan);
        $jasa   = billing_get_total_jasa($data->tanggal, $data->id_pelanggan);
        $total_barang = 0; $total_jasa = 0;
        if (isset($barang->total_barang)) {
            $total_barang = $barang->total_barang;
        }
        if (isset($jasa->total_jasa)) {
            $total_jasa   = $jasa->total_jasa;
        }
        $total_tagih  = $total_barang+$total_jasa;
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= (++$key) ?></td>
            <td align="center"><?= datefmysql($data->tanggal) ?></td>
            <td><?= $data->pelanggan ?></td>
            <td align="right"><?= rupiah($total_tagih) ?></td>
            <td align="right"><?= rupiah($data->terbayar) ?></td>
            
        </tr>
    <?php 
    $total = $total;
    $terbayar = $terbayar;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="3" align="right">TOTAL</td>
        <td align="right"><?= rupiah($total) ?></td>
        <td align="right"><?= rupiah($terbayar) ?></td>
    </tr>
</tfoot>
</table>
<script type="text/javascript">
$('#result-hutang').html('Rp.'+numberToCurrency('<?= ($total-$terbayar) ?>')+' ,00')
</script>