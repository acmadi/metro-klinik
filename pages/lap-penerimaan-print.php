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
    LAPORAN PENERIMAAN BARANG <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
    <tr class="italic">
        <th width="5%">No.</th>
        <th width="10%">Tanggal</th>
        <th width="10%">No. Faktur</th>
        <th width="20%">Nama Supplier</th>
        <th width="5%">PPN</th>
        <th width="5%">Materai</th>
        <th width="5%">Jatuh<br/> Tempo</th>
        <th width="5%">Diskon (%)</th>
        <th width="10%">Total RP.</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'id_supplier' => $_GET['supplier'],
        'faktur' => $_GET['faktur']
    );
    $penerimaan = penerimaan_load_data($param);
    $list_data = $penerimaan['data'];
    $total = 0;
    foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= (++$key) ?></td>
            <td align="center"><?= datefmysql($data->tanggal) ?></td>
            <td align="center"><?= $data->faktur ?></td>
            <td><?= $data->supplier ?></td>
            <td align="center"><?= $data->ppn ?></td>
            <td align="center"><?= rupiah($data->materai) ?></td>
            <td align="center"><?= datefmysql($data->jatuh_tempo) ?></td>
            <td align="center"><?= $data->diskon_persen ?></td>
            <td align="right"><?= rupiah($data->total) ?></td>
        </tr>
    <?php 
    $total = $total + $data->total;
    }
    ?>
    <tfoot>
        <tr>
            <td align="right" colspan="8">TOTAL</td>
            <td align="right"><?= rupiah($total) ?></td>
        </tr>
    </tfoot>
</tbody>
</table>
</body>