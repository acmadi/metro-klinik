<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
$detail="<table width=300px class=list-data>";
    $array = penjualan_load_data_barang($_GET['id']);
    $total = 0;
    foreach ($array as $i => $rows) {
        $detail.="<tr><td>".$rows->nama." ".$rows->kekuatan." ".$rows->satuan."</td><td>".$rows->qty."</td><td align=right>".rupiah($rows->harga_jual)."</td><td align=right>".rupiah($rows->subtotal)."</td></tr>";
    $total = $total + $rows->subtotal;   
    }
$detail.="<tr><td colspan=3>Total</td><td align=right>".  rupiah($total)."</td></tr>";    
$detail.="</table>";

echo $detail;
die;
?>
