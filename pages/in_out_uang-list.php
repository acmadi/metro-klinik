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
        <th width="5%">No.</th>
        <th width="10%">Waktu</th>
        <th width="15%">User Entri</th>
        <th width="50%">Keterangan Transaksi</th>
        <th width="10%">Masuk</th>
        <th width="10%">Keluar</th>
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
        'limit' => $limit,
        'start' => $offset,
        'transaksi' => ''
    );
    $arus_kas = pp_kas_load_data($param);
    $list_data = $arus_kas['data'];
    $total_data= $arus_kas['total'];
    $total_masuk=0;
    $total_keluar = 0;
    foreach ($list_data as $key => $data) {
        ?>
        <tr class="<?= ($key%2==0)?'even':'odd' ?>">
            <td align="center"><?= (++$key+$offset) ?></td>
            <td align="center"><?= datetimefmysql($data->waktu,'time') ?></td>
            <td><?= $data->karyawan ?></td>
            <td><?= $data->keterangan ?></td>
            <td align="right"><?= rupiah($data->masuk) ?></td>
            <td align="right"><?= rupiah($data->keluar) ?></td>
            <td class="aksi" align="center">
                <a class='deletion' onclick="delete_in_out_uang('<?= $data->id ?>', '<?= $page ?>');" title="Klik untuk hapus Bank">&nbsp;</a>
            </td>
        </tr>
    <?php 
    $total_masuk = $total_masuk+$data->masuk;
    $total_keluar= $total_keluar+$data->keluar;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="4" align="right"><b>TOTAL</b></td>
        <td align="right"><b><?= rupiah($total_masuk) ?></b></td>
        <td align="right"><b><?= rupiah($total_keluar) ?></b></td>
        <td align="right"></td>
    </tr>
</tfoot>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>