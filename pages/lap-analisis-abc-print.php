<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
//header_surat();
?>
<body onload="cetak()">
<?php header_surat(); ?>
<h1>
    LAPORAN ANALISIS ABC <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<table cellspacing="0" width="100%" class="list-data-print">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="40%">Nama Obat</th>
        <th width="5%">Jumlah</th>
        <th width="5%">Harga RP.</th>
        <th width="5%">Total Harga RP.</th>
        <th width="5%">Percentage</th>
        <th width="5%">% Kumulatif</th>
        <th width="5%">Gol. Obat</th>
    </tr>
</thead>
<tbody>
    <?php
    $param = array(
        'id' => '',
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'perundangan' => ''
    );
    $penjualan = analisis_abc__load_data($param);
    $list_data = $penjualan['data'];
    $total     = $penjualan['total'];
    $ttl_barang= 0;
    $ttl_percen= 0;
    $kum       = 0;
    $ttl_harga = 0;
    foreach ($list_data as $key => $data) {
        $kum = $kum+(($data->jumlah/$total)*100);
        if ($kum <= 80) {
            $golongan = "A";
        } else if ($kum > 80 and $kum <= 95) {
            $golongan = "B";
        } else {
            $golongan = "C";
        }
        $total_harga = ($data->harga*$data->jumlah);
        ?>
        <tr class="<?= ($key%2==0)?'odd':'even' ?>">
            <td align="center"><?= ++$key ?></td> 
            <td><?= $data->nama_barang ?></td> 
            <td align="center"><?= $data->jumlah ?></td>
            <td align="right"><?= rupiah($data->harga) ?></td>
            <td align="right"><?= rupiah($total_harga) ?></td>
            <td align="center"><?= (($data->jumlah/$total)*100) ?> %</td> 
            <td align="center"><?= $kum ?></td>
            <td align="center"><?= $golongan ?></td>
        </tr>
    <?php 
    $ttl_barang = $ttl_barang+$data->jumlah;
    $ttl_percen = $ttl_percen+(($data->jumlah/$total)*100);
    $ttl_harga  = $ttl_harga+$total_harga;
    }
    ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="2" align="right">TOTAL</td>
        <td align="center"><?= $ttl_barang ?></td>
        <td></td>
        <td align="right"><?= rupiah($ttl_harga) ?></td>
        <td align="center"><?= $ttl_percen ?></td>
        <td colspan="2"></td>
    </tr>
</tfoot>
</table>
</body>