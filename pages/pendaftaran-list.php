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
    <th width="10%">Waktu</th>
    <th width="10%">No. RM</th>
    <th width="25%">Customer</th>
    <th width="5%">No. Antri</th>
    <th width="10%">Pelayanan</th>
    <th width="10%">Waktu Dilayani</th>
    <th width="25%">Nama Dokter</th>
    <th width="10%">Status</th>
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
        'id' => $_GET['id_pendaftaran'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $list_data = load_data_pendaftaran($param);
    $master_pendaftaran = $list_data['data'];
    $total_data = $list_data['total'];
    foreach ($master_pendaftaran as $key => $data) { 
        ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= (++$key+$offset) ?></td>
        <td align="center"><?= datetimefmysql($data->waktu,'yes') ?></td>
        <td align="center"><?= isset($data->id_pelanggan)?$data->id_pelanggan:NULL ?></td>
        <td><?= isset($data->nama)?$data->nama:NULL ?></td>
        <td align="center"><?= $data->no_antri ?></td>
        <td><?= $data->spesialisasi ?></td>
        <td align="center"><?= datetimefmysql($data->waktu_pelayanan,'yes') ?></td>
        <td><?= $data->dokter ?></td>
        <td class='aksi' align='center'>
            <?php if ($data->id_pemeriksaan === NULL) { ?>
            <span style="cursor: pointer;" onclick="form_pemeriksaan('<?= $data->id ?>','<?= $data->id_pelanggan ?>','<?= $data->nama ?>')">BELUM PERIKSA</span>
            <?php } else { ?>
            <span style="cursor: pointer; color: green; font-weight: bold;">SUDAH PERIKSA</span>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>