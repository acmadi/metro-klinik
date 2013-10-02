<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
function paging(page) {
    get_result_pemesanan(page);
}
function get_result_pemesanan(page) {
    var awal = $('#awal').val();
    var akhir= $('#akhir').val();
    var id   = $('#id_supplier').val();
    var pg   = (page === undefined)?'':page;
    $.ajax({
        url: 'pages/lap-sp-list.php?awal='+awal+'&akhir='+akhir+'&id_supplier='+id+'&page='+pg,
        cache: false,
        success: function(data) {
            $('#result-info').html(data);
        }
    });
}
$(function() {
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#search').button().click(function() {
        get_result_pemesanan();
    });
    $('#reset').button().click(function() {
        $('#awal,#akhir').val('<?= date("d/m/Y") ?>');
        $('#customer,#id_customer').val('');
        $('#result-info').html('');
    });
    $('#cetak').button().click(function() {
        var awal = $('#awal').val();
        var akhir= $('#akhir').val();
        var supplier   = $('#id_supplier').val();
        var wWidth = $(window).width();
        var dWidth = wWidth * 0.8;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('pages/lap-sp-print.php?awal='+awal+'&akhir='+akhir+'&id_supplier='+supplier, 'Stok', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
    var lebar = $('#supplier').width();
    $('#supplier').autocomplete("models/autocomplete.php?method=supplier",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('#id_supplier').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_supplier').val(data.id);
    });
});
</script>
<h1 class="margin-t-0">Laporan Pemesanan</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td></tr>
    <tr><td>Nama Supplier:</td><td><?= form_input('supplier', NULL, 'id=supplier size=40') ?><?= form_hidden('id_supplier', NULL, 'id=id_supplier') ?></td></tr>
    <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?> <?= form_button('Cetak', 'id=cetak') ?></td></tr>
</table>
</div>
<div id="result-info">
    
</div>