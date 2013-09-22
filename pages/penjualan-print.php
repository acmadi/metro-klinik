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
    SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
}
</script>
<body onload="cetak()">
<h1>
    LAPORAN PENJUALAN <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="5%">No. Resep</th>
        <th width="15%">Pasien</th>
        <th width="15%">Dokter</th>
        <th width="5%">Diskon <br/>Rp.</th>
        <th width="5%">Diskon <br/>%</th>
        <th width="5%">PPN %</th>
        <th width="5%">Tuslah <br/>RP.</th>
        <th width="5%">Embalage RP.</th>
        <th width="5%">Total</th>
        <th width="5%">Terbayar</th>
    </tr>
</thead>
<tbody>
    <?php
    
    
    $param = array(
        'id' => '',
        'limit' => '',
        'start' => '',
        'laporan' => '',
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'pasien' => $_GET['pasien'],
        'dokter' => $_GET['dokter']
    );
    $penjualan = penjualan_load_data($param);
    $list_data = $penjualan['data'];
    $id = "";
    $no = 1;
    $alert = "";
    $total_nota = 0;
    $total_terbayar = 0;
    foreach ($list_data as $key => $data) { 
        //$str = $data->id.'#'.$data->id_resep.'#'.$data->customer.'#'.$data->id_customer;
        if ($data->total > $data->terbayar) {
            $alert="warning";
        }
        ?>
        <tr id="<?= $data->id ?>" class="detail <?= ($id !== $data->id)?'odd':NULL ?> <?= $alert ?>">
            <td align="center"><?= ++$key ?></td>
            <td align="center"><?= ($id !== $data->id)?datetimefmysql($data->waktu):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->id_resep:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->customer:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->dokter:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->diskon_rupiah):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->diskon_persen:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->ppn:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->tuslah):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->embalage):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->total):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->terbayar):NULL ?></td>
            
        </tr>
    <?php 
    if ($id !== $data->id) {
        $no++;
    }
    $id = $data->id;
    $total_nota = $total_nota+$data->total;
    $total_terbayar = $total_terbayar+$data->terbayar;
    }
    ?>
        <tr>
            <td colspan="9" align="right">TOTAL</td><td align="right"><b><?= rupiah($total_nota) ?></b></td><td align="right"><b><?= rupiah($total_terbayar) ?></b></td>
        </tr>
</tbody>
</table>
</body>