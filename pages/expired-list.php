<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<div style="background: yellow; width: 20px; margin-right: 2px; border: 1px solid #000; display: inline-block;">&nbsp;</div> Expired memasuki 6 bulan
<div style="background: yellowgreen; width: 20px; margin-right: 2px; border: 1px solid #000; display: inline-block;">&nbsp;</div> Expired memasuki 3 bulan
<div style="background: red; width: 20px; margin-right: 2px; border: 1px solid #000; display: inline-block;">&nbsp;</div> Expired
<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="5%">No.</th>
    <th width="50%">Nama Barang</th>
    <th width="10%">ED</th>
    <th width="10%">Sisa</th>
    <!--<th width="4%">#</th>-->
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
        'id' => $_GET['id_expired'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $list_data = load_data_expired_date($param);
    $master_stok_opname = $list_data['data'];
    $total_data = $list_data['total'];
    foreach ($master_stok_opname as $key => $data) { 
        $sekarang = date("Y-m-d");
        $var1     = mktime(0, 0, 0, date("m")+3, date("d"), date("Y"));
        $tiga_bln = date("Y-m-d", $var1);
        $var2     = mktime(0, 0, 0, date("m")+6, date("d"), date("Y"));
        $enam_bln = date("Y-m-d", $var2);
        if (($data->ed > $sekarang) and ($data->ed <= $tiga_bln)) {
            $alert = "threemonth";
        }
        else if (($data->ed > $sekarang) and ($data->ed <= $enam_bln)) {
            $alert = "warning";
        }
        else if ($data->ed <= $sekarang) {
            $alert = "urgent";
        } else {
            $alert = "";
        }
        ?>
    <tr class="<?= $alert ?>">
        <td align="center"><?= (++$key+$offset) ?></td>
        <td><?= $data->nama.' '.$data->kekuatan.' '.$data->satuan_kekuatan ?></td>
        <td align="center"><?= datefmysql($data->ed) ?></td>
        <td align="center"><?= $data->sisa ?></td>
        <!--<td class='aksi' align='center'>
            <a class='edition' onclick="edit_expired('<?= $str ?>');" title="Klik untuk edit stok_opname">&nbsp;</a>
            <a class='deletion' onclick="delete_expired('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
        </td>-->
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>