<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">

</script>
<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="40%">Nama Barang</th>
    <th width="30%">Nama Distributor</th>
    <th width="10%">Stok Min.</th>
    <th width="10%">Sisa</th>
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
        'id' => $_GET['id_defecta'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $list_data = load_data_defecta($param);
    $master_defecta = $list_data['data'];
    $total_data = $list_data['total'];
    foreach ($master_defecta as $key => $data) { 
        $rows = get_distributor_by_barang($data->id_barang);
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= (++$key+$offset) ?></td>
        <td><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?></td>
        <td><?= isset($rows->nama)?$rows->nama:NULL ?></td>
        <td align="center"><?= $data->stok_minimal ?></td>
        <td align="center"><?= $data->sisa ?></td>
        <td class='aksi' align='center'>
            <a class='planning' onclick="add_to_planning('<?= $data->id_barang ?>','<?= $page ?>','<?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?>');" title="Klik untuk entri ke rencana pemesanan">&nbsp;</a>
        </td>
        <!--<td class='aksi' align='center'>
            <a class='edition' onclick="edit_stokopname('<?= $str ?>');" title="Klik untuk edit defecta">&nbsp;</a>
            <a class='deletion' onclick="delete_stokopname('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>-->
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>