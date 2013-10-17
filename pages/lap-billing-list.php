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
        <th width="10%">Cara Bayar</th>
        <th width="10%">Nama Bank</th>
        <th width="10%">No. Kartu</th>
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
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= (++$key) ?></td>
            <td align="center"><?= datefmysql($data->tanggal) ?></td>
            <td><?= $data->pelanggan ?></td>
            <td align="right"><?= rupiah($data->total) ?></td>
            <td align="right"><?= rupiah($data->terbayar) ?></td>
            <td align="center"><?= $data->cara_bayar ?></td>
            <td><?= $data->nama_bank ?></td>
            <td><?= $data->no_kartu ?></td>
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