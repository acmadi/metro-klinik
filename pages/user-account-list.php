<?php
include_once '../models/masterdata.php';
include_once '../inc/functions.php';
?>
<table cellspacing="0" width="50%" class="list-data">
<thead>
<tr class="italic">
    <th width="5%">No.</th>
    <th width="30%">Username</th>
    <th width="40%">Nama Karyawanr</th>
    <th width="5%">Level</th>
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
        'id' => $_GET['id_user_account'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $data_list = load_data_user_account($param);
    $list_data = $data_list['data'];
    $total_data= $data_list['total'];
    foreach ($list_data as $key => $data) { 
        $str = $data->id.'#'.$data->id_karyawan.'#'.$data->nama.'#'.$data->username.'#'.$data->hint.'#'.$data->level;
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= (++$key+$offset) ?></td>
        <td><?= $data->username ?></td>
        <td><?= $data->nama ?></td>
        <td><?= $data->level ?></td>
        <td class='aksi' align='center'>
            <a class='edition' onclick="edit_user_account('<?= $str ?>');" title="Klik untuk edit supplier">&nbsp;</a>
            <a class='deletion' onclick="delete_user_account('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus supplier">&nbsp;</a>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>