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
    <th width="10%">Tagihan (Rp.)</th>
    <th width="5%">Diskon %</th>
    <th width="10%">Total (Rp.)</th>
    <th width="10%">Tunai (Rp.)</th>
    <th width="5%">Cara Bayar</th>
    <th width="10%">Nama Bank</th>
    <th width="10%">No. Kartu</th>
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
        <td align="right"><?= rupiah($data->total) ?></td>
        <td align="center"><?= $data->diskon ?></td>
        <td align="center"><?= rupiah($data->total-($data->total*($data->diskon/100))) ?></td>
        <td align="right"><?= rupiah($data->uang_serah) ?></td>
        <td align="center"><?= $data->cara_bayar ?></td>
        <td><?= $data->nama_bank ?></td>
        <td><?= $data->no_kartu ?></td>
        <td class='aksi' align='center'>
            <a class='printing' onclick="cetak_nota('<?= $data->id ?>');" title="Klik untuk cetak ulang nota">&nbsp;</a>
            <a class='deletion' onclick="delete_billing('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>