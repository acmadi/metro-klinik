<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
$(function() {
    /*$('.detail').on('mouseover',function() {
        $.ajax({
            url: 'pages/tooltip-detail-transaksi.php',
            data: 'id='+$(this).attr('id'),
            cache: false,
            success: function(msg) {
                $('.list-data tbody tr.detail').attr('title',msg);
                $( document ).tooltip();
            }
        });
    });*/
});
</script>
<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="5%">Tanggal</th>
        <th width="15%">Customer</th>
        <th width="5%">Diskon <br/>Rp.</th>
        <th width="5%">Diskon <br/>%</th>
        <th width="5%">PPN %</th>
        <th width="5%">Tuslah <br/>RP.</th>
        <th width="5%">Embalage RP.</th>
        <th width="5%">Total</th>
        <th width="5%">Terbayar</th>
        <th width="20%">Nama Barang</th>
        <th width="5%">Kemasan</th>
        <th width="5%">Jumlah</th>
        <th width="5%">Harga</th>
        <th width="10%">Subtotal</th>
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
        'id' => $_GET['id_penjualan'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $penjualan = penjualan_load_data($param);
    $list_data = $penjualan['data'];
    $total_data= $penjualan['total'];
    $id = "";
    $no = 1;
    
    foreach ($list_data as $key => $data) { 
        $str = $data->id.'#'.$data->id_resep.'#'.$data->customer.'#'.$data->id_customer;
        if ($data->total > $data->terbayar) {
            $alert="warning";
        } else {
            $alert = "";
        }
        ?>
        <tr id="<?= $data->id ?>" class="detail <?= ($id !== $data->id)?'odd':NULL ?> <?= $alert ?>">
            <td align="center"><?= ($id !== $data->id)?($no+$offset):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datetimefmysql($data->waktu):NULL ?></td>
            <td><?= ($id !== $data->id)?$data->customer:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->diskon_rupiah):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->diskon_persen:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->ppn:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->tuslah):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->embalage):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->total):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->terbayar):NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td align="center"><?= $data->kemasan ?></td>
            <td align="center"><?= $data->qty ?></td>
            <td align="right"><?= rupiah($data->harga_jual) ?></td>
            <td align="right"><?= rupiah($data->subtotal) ?></td>
            <td class='aksi' align='center'>
                <!--<a class='edition' onclick="edit_penjualan('<?= $str ?>');" title="Klik untuk edit">&nbsp;</a>-->
                <?php
                if ($id !== $data->id) { ?>
                    <a class='deletion' onclick="delete_penjualan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
                <?php } ?>
            </td>
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
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>