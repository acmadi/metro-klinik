<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>

<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="7%">No. Ref</th>
        <th width="7%">No. Faktur</th>
        <th width="5%">Tanggal</th>
        <th width="20%">Supplier</th>
        <th width="7%">Cara Bayar</th>
        <th width="10%">Nama Bank</th>
        <th width="10%">No. Transaksi</th>
        <th width="15%">Keterangan</th>
        <th width="8%">Jumlah<br/>Pembayaran</th>
        <th width="2%">#</th>
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
        'id' => $_GET['id_inkaso'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $inkaso = inkaso_load_data($param);
    $list_data = $inkaso['data'];
    $total_data= $inkaso['total'];
    foreach ($list_data as $key => $data) {
    ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= ++$key ?></td>
        <td align="center"><?= $data->no_ref ?></td>
        <td align="center"><?= $data->faktur ?></td>
        <td align="center"><?= datefmysql($data->tanggal) ?></td>
        <td><?= $data->supplier ?></td>
        <td align="center"><?= $data->cara_bayar ?></td>
        <td><?= $data->bank ?></td>
        <td align="center"><?= $data->no_transaksi ?></td>
        <td><?= $data->keterangan ?></td>
        <td align="right"><?= rupiah($data->nominal) ?></td>
        <td align="center">
            <a class='deletion' onclick="delete_inkaso('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>
    </tr>
    
    <?php } ?>
</tbody>
</table>