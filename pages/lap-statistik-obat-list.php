<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="40%">Nama Barang</th>
        <th width="5%">Jumlah</th>
        <th width="5%">Percentage</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'id' => '',
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'perundangan' => $_GET['perundangan']
    );
    $penjualan = statistik_penjualan_load_data($param);
    $list_data = $penjualan['data'];
    $total     = $penjualan['total'];
    $ttl_barang= 0;
    $ttl_percen= 0;
    foreach ($list_data as $key => $data) { 
        
        ?>
        <tr class="<?= ($key%2==0)?'odd':'even' ?>">
            <td align="center"><?= ++$key ?></td> 
            <td><?= $data->nama_barang ?></td> 
            <td align="center"><?= $data->jumlah ?></td>
            <td align="center"><?= (($data->jumlah/$total)*100) ?> %</td> 
        </tr>
    <?php 
    $ttl_barang = $ttl_barang+$data->jumlah;
    $ttl_percen = $ttl_percen+(($data->jumlah/$total)*100);
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="2" align="right">TOTAL</td>
        <td align="center"><?= $ttl_barang ?></td>
        <td align="center"><?= $ttl_percen ?></td>
    </tr>
</tfoot>
</table>