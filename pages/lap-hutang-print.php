<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {
    window.print();
    if (confirm('Apakah menu print ini akan ditutup?')) {
        window.close();
    }
    //SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
}
</script>
<body onload="cetak()">
<h1>
    LAPORAN HUTANG SUPPLIER <br /> <?php if ($_GET['awal'] !== '') {  ?>TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?> <?php } ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
    <tr class="italic">
        <th width="5%">No.</th>
        <th width="10%">Tanggal</th>
        <th width="10%">No. Faktur</th>
        <th width="10%">Jatuh Tempo</th>
        <th width="20%">Nama Supplier</th>
        <th width="10%">Total RP.</th>
        <th width="10%">Terbayar RP.</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'awal_faktur' => date2mysql($_GET['awal_faktur']),
        'akhir_faktur' => date2mysql($_GET['akhir_faktur']),
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'status' => $_GET['status'],
        'id_supplier' => $_GET['id_supplier']
    );
    $hutang = hutang_load_data($param);
    $list_data = $hutang['data'];
    $total = 0;
    $terbayar = 0;
    foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= (++$key) ?></td>
            <td align="center"><?= datefmysql($data->tanggal) ?></td>
            <td align="center"><?= $data->faktur ?></td>
            <td align="center"><?= datefmysql($data->jatuh_tempo) ?></td>
            <td><?= $data->supplier ?></td>
            <td align="right"><?= rupiah($data->total) ?></td>
            <td align="right"><?= rupiah($data->terbayar) ?></td>
            
        </tr>
    <?php 
    $total = $total + $data->total;
    $terbayar = $terbayar + $data->terbayar;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="5" align="right">TOTAL</td>
        <td align="right"><?= rupiah($total) ?></td>
        <td align="right"><?= rupiah($terbayar) ?></td>
    </tr>
    <tr>
        <td colspan="5" align="right">HUTANG</td>
        <td align="right" colspan="2"><?= rupiah($total-$terbayar) ?></td>
    </tr>
</tfoot>
</table> 
</body>