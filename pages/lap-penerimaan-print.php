<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {
    window.print();
    setTimeout(function(){ window.close();},300);
    //SCETAK.innerHTML = '<br /><input onClick=\'cetak()\' type=\'submit\' name=\'Submit\' value=\'Cetak\' class=\'tombol\'>';
}
</script>
<body onload="cetak();">
<?php    header_surat(); ?>
<h1>
    LAPORAN PENERIMAAN BARANG <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="5%">No. Faktur</th>
        <th width="15%">Nama Supplier</th>
        <th width="3%">PPN</th>
        <th width="5%">Materai</th>
        <th width="5%">Tempo</th>
        <th width="3%">Diskon<br/> (%)</th>
        <th width="5%">Diskon<br/> Rp.</th>
        <th width="5%">Total RP.</th>
        <th width="15%">Nama Barang</th>
        <th width="5%">Jumlah</th>
        <th width="5%">Kemasan</th>
        <th width="5%">ED</th>
        <th width="5%">No. Batch</th>
        <th width="5%">Harga RP.</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'faktur' => $_GET['faktur'],
        'id_supplier' => $_GET['supplier']
    );
    $penerimaan = penerimaan_load_data($param);
    $list_data = $penerimaan['data'];
    //$total_data= $penerimaan['total'];
    $id = "";
    $no = 1;
    $total_penerimaan = 0;
    foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= ($id !== $data->id)?($no):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datefmysql($data->tanggal):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->faktur:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->supplier:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->ppn:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?rupiah($data->materai):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datefmysql($data->jatuh_tempo):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->diskon_persen:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->diskon_rupiah):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->total):NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td align="center"><?= $data->kemasan ?></td>
            <td align="center"><?= datefmysql($data->expired) ?></td>
            <td align="center"><?= $data->nobatch ?></td>
            <td align="right"><?= rupiah($data->harga) ?></td>
        </tr>
    <?php 
    if ($id !== $data->id) {
        $no++;
        $total_penerimaan = $total_penerimaan+$data->total;
    }
    $id = $data->id;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="9" align="right">TOTAL</td>
        <td align="center"><b><?= rupiah($total_penerimaan) ?></b></td>
        <td colspan="6"></td>
    </tr>
</tfoot>
</table>
</body>