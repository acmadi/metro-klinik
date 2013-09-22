<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
$(function() {
    //$( document ).tooltip();
});
</script>
<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="5%">No.</th>
        <th width="10%">No. SP</th>
        <th width="5%">Tanggal</th>
        <th width="20%">Nama Supplier</th>
        <th width="15%">Karyawan</th>
        <th width="20%">Nama Barang</th>
        <th width="5%">Kemasan</th>
        <th width="5%">Jumlah</th>
        <th width="5%">#</th>
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
        'id' => $_GET['id_pemesanan'],
        'limit' => $limit,
        'start' => $offset,
        'id_supplier' => '',
        'search' => $_GET['search']
    );
    $pemesanan = pemesanan_load_data($param);
    $list_data = $pemesanan['data'];
    $total_data= $pemesanan['total'];
    $no = 1;
    $sp = "";
    foreach ($list_data as $key => $data) { ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= ($sp !== $data->id)?($no+$offset):NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->id:NULL ?></td>
            <td align="center"><?= ($sp !== $data->id)?datetimefmysql($data->tanggal):NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->supplier:NULL ?></td>
            <td><?= ($sp !== $data->id)?$data->karyawan:NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->kemasan ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td class='aksi' align='center'>
                <!--<a class='edition' onclick="edit_pemesanan('<?= $str ?>');" title="Klik untuk edit pemesanan">&nbsp;</a>-->
                <?php
                if ($sp !== $data->id) { ?>
                    <a class='deletion' onclick="delete_pemesanan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus pemesanan">&nbsp;</a>
                <?php } ?>
            </td>
        </tr>
    <?php 
    if ($sp !== $data->id) {
        $no++;
    }
    $sp = $data->id;
    }
    ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>