<?php
include_once '../models/masterdata.php';
include_once '../inc/functions.php';
?>
<table cellspacing="0" width="100%" class="list-data">
    <thead>
        <tr>
            <th width="3%">No.</th>
            <th width="20%">Nama Item</th>
            <th width="5%">Margin %</th>
            <th width="8%">Margin Rp.</th>
            <th width="5%">Diskon %</th>
            <th width="8%">Diskon Rp.</th>
            <th width="8%">Harga Jual Rp.</th>
            <th width="20%">Nama Barang</th>
            <th width="5%">Kemasan</th>
            <th width="5%">Jumlah</th>
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
            'id' => $_GET['id_itemkit'],
            'limit' => $limit,
            'start' => $offset,
            'search' => $_GET['search']
        );
        $master_item_kit = item_kit_load_data($param);
        $list_data = $master_item_kit['data'];
        $total_data= $master_item_kit['total'];
        $id = "";
        $no = 1;
        foreach ($list_data as $key => $data) { 
            $str =  $data->nama.'#'.
                    $data->margin_persen.'#'.
                    $data->margin_rupiah.'#'.
                    $data->diskon_persen.'#'.
                    $data->diskon_rupiah.'#'.
                    $data->harga_jual;
            ?>
        <tr>
            <td align="center"><?= ($id !== $data->id)?$no:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->nama:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->margin_persen:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->margin_rupiah):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->diskon_persen:NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->diskon_rupiah):NULL ?></td>
            <td align="right"><?= ($id !== $data->id)?rupiah($data->harga_jual):NULL ?></td>
            <td><?= $data->nama_barang ?></td>
            <td><?= $data->kemasan ?></td>
            <td align="center"><?= $data->jumlah ?></td>
            <td align="center" class="aksi">
                <?php if ($id !== $data->id) { ?>
                <a class='edition' onclick="edit_itemkit('<?= $data->id ?>', '<?= $str ?>');" title="Klik untuk edit">&nbsp;</a>
                <a class='deletion' onclick="delete_itemkit('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
                <?php } ?>
            </td>
        </tr>
        <?php 
        if ($id !== $data->id) {
            $no++;
        }
        $id = $data->id;
        } ?>
    </tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>