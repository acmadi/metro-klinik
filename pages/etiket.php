<?php
include_once '../models/transaksi.php';
include_once '../models/cetak-transaksi.php';
include_once '../inc/functions.php';
?>
<link rel="stylesheet" href="../themes/theme_default/theme-print.css" />
<script type="text/javascript" src="../plugins/metro-jquery/jquery-1.8.3.js"></script>
<script type="text/javascript" src="../plugins/metro-jquery/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript">
$(function() {
    $('button').click(function() {
        $('textarea').css('border','none');
        $('button').hide();
        cetak();
        setTimeout(function(){ $('button').show();},2000);
    });
});
window.onunload = refreshParent;
function refreshParent() {
    //window.opener.location.reload();
}
function cetak() {  		
    window.print();
    setTimeout(function(){ window.close();},300);
}
</script>
<body>
<?php
$load = etiket_load_data($_GET['id_resep'],$_GET['no_r']);
foreach ($load as $rows);
?>

<div style="padding: 3px;">
    No. R/: <?= $rows->r_no ?><br/>
    <?php
        foreach ($load as $data) { ?>
                <?= $data->nama_barang ?><br/>
    <?php }
    ?>
                Aturan Pakai:<br/><textarea name="aturan_pakai" style="font-family: Arial; font-size: 11px; width: 100%" id="aturan_pakai"></textarea>
    <span id="CETAK"><button>CETAK</button></span>
</div>
    
</body>