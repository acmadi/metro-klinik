<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
function paging(page) {
    get_result_penerimaan(page);
}
function get_result_penerimaan(page) {
    var awal = $('#awal').val();
    var akhir= $('#akhir').val();
    var id   = $('#id_supplier').val();
    var faktur=$('#faktur').val();
    var pg   = (page === undefined)?'':page;
    $.ajax({
        url: 'pages/lap-penerimaan-list.php?awal='+awal+'&akhir='+akhir+'&id_supplier='+id+'&faktur='+faktur+'&page='+pg,
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
        get_result_penerimaan();
    });
    $('#reset').button().click(function() {
        $('input[type=text],input[type=hidden]').val('');
        $('#awal,#akhir').val('<?= date("d/m/Y") ?>');
        $('#result-info').html('');
    });
    $('#cetak').button().click(function() {
        var awal        = $('#awal').val();
        var akhir       = $('#akhir').val();
        var supplier    = $('#id_supplier').val();
        var faktur      = $('#faktur').val();
        var wWidth = $(window).width();
        var dWidth = wWidth * 1;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('pages/lap-penerimaan-print.php?awal='+awal+'&akhir='+akhir+'&supplier='+supplier+'&faktur='+faktur, 'Penerimaan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
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
    $('#faktur').autocomplete("models/autocomplete.php?method=get_faktur",
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
            var str = '<div class=result>'+data.faktur+' - '+data.supplier+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.faktur);
        $('#id_supplier').val(data.id_supplier);
        $('#supplier').val(data.supplier);
    });
});
</script>
<h1 class="margin-t-0">Laporan penerimaan</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td></tr>
    <tr><td>Nama Supplier:</td><td><?= form_input('supplier', NULL, 'id=supplier size=40') ?><?= form_hidden('id_supplier', NULL, 'id=id_supplier') ?></td></tr>
    <tr><td>Nomor Faktur:</td><td><?= form_input('faktur', NULL, 'id=faktur size=40') ?></td></tr>
    <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?> <?= form_button('Cetak', 'id=cetak') ?></td></tr>
</table>
</div>
<div id="result-info">
    
</div>