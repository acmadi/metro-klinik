<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript">
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body onload="cetak();">
<?= header_surat(); ?>
<h1>
    LAPORAN RUGI / LABA <br /> TANGGAL <?= $_GET['awal'] ?> s . d <?= $_GET['akhir'] ?>
</h1>
<?php
$pendapatan_penjualan = pendapatan_penjualan_load_data($_GET['awal'],$_GET['akhir']);
$pendapatan_jasa      = pendapatan_jasa_load_data($_GET['awal'], $_GET['akhir']);
$pendapatan_lain      = pendapatan_lain_lain_load_data($_GET['awal'], $_GET['akhir']);
$kas                  = total_penerimaan_kas_load_data($_GET['awal'], $_GET['akhir']); // total penerimaan kas

$total_pendapatan     = $kas->penerimaan_total+$pendapatan_jasa->jasa; // total pendapatan = penjualan barang + jasa
$percentage_pendapatan_barang = 0;
$percentage_jasa_apt  = 0;
$percentage_pend_lain = 0;
$total_percent_pendapatan     = 0;

if ($total_pendapatan !== 0) {
    
    // percentage pendapatan barang
    $percentage_pendapatan_barang = ($pendapatan_penjualan->penjualan_barang/$total_pendapatan)*100;
    $percentage_jasa_apt          = ($pendapatan_jasa->jasa/$total_pendapatan)*100;
    $percentage_pend_lain         = ($pendapatan_lain->penerimaan_lain/$total_pendapatan)*100;

    $total_percent_pendapatan     = $percentage_pendapatan_barang+$percentage_jasa_apt+$percentage_pend_lain;
}
// penerimaan lain-lain
$penerimaan           = penerimaan_kas_load_data($_GET['awal'],$_GET['akhir']);


// BEBAN APOTEK

$hpp                  = hna_load_data($_GET['awal'], $_GET['akhir']);
$pengeluaran          = pengeluaran_kas_load_data($_GET['awal'], $_GET['akhir']);
$total_pengeluaran_kas= total_pengeluaran_kas_load_data($_GET['awal'], $_GET['akhir']);

$total_pengeluaran    = 0;
if ($total_pengeluaran_kas !== 0) {
    $total_pengeluaran    = $hpp->total_hna+$total_pengeluaran_kas->pengeluaran_total;
}
?>
<table class="list-data-print" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th width="45%">Diskripsi</th>
            <th width="15%">Rincian</th>
            <th width="15%">Jumlah</th>
            <th width="15%">Total Rp.</th>
            <th width="10%">( % )</th>
        </tr>
    </thead>
    <tbody>
        <!-- Pendapatan Begin -->
        <tr class="even">
            <td><b>&nbsp;PENDAPATAN APOTEK</b></td>
            <td colspan="4"></td>
        </tr>
        <tr class="even">
            <td>&nbsp;&nbsp;PENDAPATAN PENJUALAN</td>
            <td colspan="4"></td>
        </tr>
                <tr class="even">
                    <td style="padding-left: 20px;">Penjualan Barang Dagangan</td>
                    <td align="right"><?= rupiah($pendapatan_penjualan->penjualan_barang) ?></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?= $percentage_pendapatan_barang ?></td>
                </tr>
                <tr class="even">
                    <td style="padding-left: 20px;">Jasa Apoteker</td>
                    <td align="right"><?= rupiah($pendapatan_jasa->jasa) ?></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?= $percentage_jasa_apt ?></td>
                </tr>
        <tr class="even">
            <td>&nbsp;&nbsp;PENDAPATAN LAIN-LAIN</td>
            <td colspan="4"></td>
        </tr>
                <?php foreach ($penerimaan as $rows) { ?>
                <tr class="even">
                    <td style="padding-left: 20px;"><?= $rows->penerimaan_pengeluaran_nama ?></td>
                    <td align="right"><?= rupiah($rows->penerimaan) ?></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?= (($rows->penerimaan/$total_pendapatan)*100) ?></td>
                </tr>
                <?php } ?>
        <tr class="odd" style="font-weight: bold;">
            <td>&nbsp;TOTAL PENDAPATAN</td>
            <td align="right"></td>
            <td></td>
            <td align="right"><?= rupiah($total_pendapatan) ?></td>
            <td align="right"><?= $total_percent_pendapatan ?></td>
        </tr>
    <!-- Pendapatan End -->

    <!-- Pengeluaran Start -->

    <tr class="even">
        <td><b>&nbsp;BEBAN APOTEK</b></td>
        <td colspan="4"></td>
    </tr>
    <tr class="even">
        <td>&nbsp;&nbsp;BEBAN POKOK PENJUALAN</td>
        <td align="right"></td>
        <td></td>
        <td></td>
        <td align="right"></td>
    </tr>
            <tr class="even">
                <td style="padding-left: 20px;">Harga Pokok Penjualan</td>
                <td align="right"><?= rupiah($hpp->total_hna) ?></td>
                <td></td>
                <td></td>
                <td align="right">
                    <?php 
                        $hpps = 0;
                        if ($total_pendapatan > 0) {
                            $hpps = (($hpp->total_hna/$total_pendapatan)*100); echo $hpps;  
                        }
                    ?>
                </td>
            </tr>
    <tr class="even">
        <td>&nbsp;&nbsp;BEBAN USAHA</td>
        <td align="right"></td>
        <td></td>
        <td></td>
        <td align="right"></td>
    </tr>
            <?php 
            $ttl_beban_usaha = 0;
            if (count($pengeluaran) > 0) {
                foreach ($pengeluaran as $rowx) { ?>
                    <tr class="even">
                    <td style="padding-left: 20px;"><?= $rowx->penerimaan_pengeluaran_nama ?></td>
                    <td align="right"><?= rupiah($rowx->pengeluaran) ?></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?= ($total_pendapatan === 0)?'0':(($rowx->pengeluaran/$total_pendapatan)*100) ?></td>
                </tr>
                <?php 
                $ttl_beban_usaha=$ttl_beban_usaha+($total_pendapatan === 0)?'0':(($rowx->pengeluaran/$total_pendapatan)*100);
                }
            } ?>
    <tr class="odd" style="font-weight: bold;">
        <td>&nbsp;TOTAL BEBAN</td>
        <td align="right"></td>
        <td></td>
        <td align="right"><?= rupiah($total_pengeluaran) ?></td>
        <td align="right"><?= ($hpps+$ttl_beban_usaha) ?></td>
    </tr>
    <?php
    $result_nominal = $total_pendapatan-$total_pengeluaran;
    if ($result_nominal < 0) {
        $hasil = "(".rupiah(abs($result_nominal)).")";
    } else {
        $hasil = rupiah($result_nominal);
    }

    $result_percent = (round($percentage_pendapatan_barang+$percentage_jasa_apt+$percentage_pend_lain,2)-($hpps+$ttl_beban_usaha));
    if ($result_percent < 0) {
        $hasil_percent = "(".abs($result_percent).")";
    } else {
        $hasil_percent = $result_percent;
    }
    ?>
    <tr class="odd" style="font-weight: bold;">
        <td>&nbsp;LABA (RUGI)</td>
        <td align="right"></td>
        <td></td>
        <td align="right"><?= $hasil ?></td>
        <td align="right"><?= $hasil_percent ?></td>
    </tr>
    </tbody>
</table>
</body>