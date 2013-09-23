<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="10%">Waktu</th>
    <th width="25%">Pasien</th>
    <th width="20%">Layanan</th>
    <th width="10%">Nominal (Rp.)</th>
    <th width="3%">#</th>
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
        'id' => $_GET['id_billing'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $list_data = load_data_billing($param);
    $master_barang = $list_data['data'];
    $total_data = $list_data['total'];
    foreach ($master_barang as $key => $data) { 
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= (++$key+$offset) ?></td>
        <td align="center"><?= datetimefmysql($data->waktu, TRUE) ?></td>
        <td><?= $data->pasien ?></td>
        <td>
<!--            <ul>
            <?php foreach ($tindakan as $rows) { ?>
               <li><?= $rows->nama ?></li>
            <?php } ?>
            </ul>-->
        </td>
        <td align="right"><?= rupiah($data->bayar) ?></td>
        <td class='aksi' align='center'>
            <a class='printing' onclick="cetak_nota('<?= $data->id_pelanggan ?>','<?= $data->tanggal ?>');" title="Klik untuk cetak ulang nota">&nbsp;</a>
            <a class='deletion' onclick="delete_billing('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>