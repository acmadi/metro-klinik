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
        <th width="5%">ED</th>
        <th width="5%">No. Batch</th>
        <th width="5%">Harga RP.</th>
    </tr>
</thead>
<tbody>
    <?php
    $limit = 10;
    $page  = $_GET['page'];
    if ($_GET['page'] === '') {
        $page = 1;
        $offset = 0;
    } else {
        $offset = ($page-1)*$limit;
    }
    
    $param = array(
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'faktur' => $_GET['faktur'],
        'id_supplier' => $_GET['id_supplier']
    );
    $penerimaan = penerimaan_load_data($param);
    $list_data = $penerimaan['data'];
    //$total_data= $penerimaan['total'];
    $id = "";
    $no = 1;
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
            <td align="center"><?= datefmysql($data->expired) ?></td>
            <td align="center"><?= $data->nobatch ?></td>
            <td align="right"><?= rupiah($data->harga) ?></td>
        </tr>
    <?php 
    if ($id !== $data->id) {
        $no++;
    }
    $id = $data->id;
    }
    ?>
</tbody>
</table>