<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
function paging(page) {
    get_result_hutang(page);
}
function get_result_hutang(page) {
    var awal_faktur = $('#awal_faktur').val();
    var akhir_faktur= $('#akhir_faktur').val();
    var awal  = $('#awal').val();
    var akhir = $('#akhir').val();
    var id    = $('#id_supplier').val();
    var status= $('input:checked').val();
    $.ajax({
        url: 'pages/lap-hutang-list.php?awal='+awal+'&akhir='+akhir+'&id_supplier='+id+'&awal_faktur='+awal_faktur+'&akhir_faktur='+akhir_faktur+'&status='+status,
        cache: false,
        success: function(data) {
            $('#result-info').html(data);
        }
    });
}
$(function() {
    $('#awal,#akhir,#awal_faktur,#akhir_faktur').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#search').button().click(function() {
        get_result_hutang();
    });
    $('#reset').button().click(function() {
        $('input[type=text]').val('');
        $('input[type=radio]').removeAttr('checked');
        $('#result-info').html('');
    });
    $('#cetak').button().click(function() {
        var awal_faktur = $('#awal_faktur').val();
        var akhir_faktur= $('#akhir_faktur').val();
        var awal        = $('#awal').val();
        var akhir       = $('#akhir').val();
        var supplier    = $('#id_supplier').val();
        var status      = $('input:checked').val();
        var wWidth = $(window).width();
        var dWidth = wWidth * 1;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('pages/lap-hutang-print.php?awal='+awal+'&akhir='+akhir+'&id_supplier='+supplier+'&awal_faktur='+awal_faktur+'&akhir_faktur='+akhir_faktur+'&status='+status, 'hutang', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
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
<h1 class="margin-t-0">Laporan hutang</h1>
<div class="input-parameter">
    <table width="100%"><tr valign="top"><td width="50%">
        <table width="100%">
            <tr><td width="25%">Range Tanggal Faktur:</td><td><?= form_input('awal_faktur', NULL, 'id=awal_faktur size=10') ?> s . d <?= form_input('akhir_faktur', NULL, 'id=akhir_faktur size=10') ?></td></tr>
            <tr><td>Range Jatuh Tempo:</td><td><?= form_input('awal', NULL, 'id=awal size=10') ?> s . d <?= form_input('akhir', NULL, 'id=akhir size=10') ?></td></tr>
            <tr><td>Nama Supplier:</td><td><?= form_input('supplier', NULL, 'id=supplier size=40') ?><?= form_hidden('id_supplier', NULL, 'id=id_supplier') ?></td></tr>
            <tr><td>Keterangan:</td><td><?= form_radio('ket', 'Lunas', 'lunas', 'Lunas', FALSE) ?> <?= form_radio('ket', 'Hutang', 'hutang', 'Hutang', FALSE) ?></td></tr>
            <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?> <?= form_button('Cetak', 'id=cetak') ?></td></tr>
        </table>
            </td><td width="50%">
                Hutang: <div id="result-hutang" style="font-size: 40px;"></div>
        </td></tr>
    </table>
</div>
<div id="result-info">
    
</div>