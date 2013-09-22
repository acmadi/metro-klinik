<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
include_once '../pages/message.php';
?>
<script type="text/javascript">
function paging(page, tab, search) {
    load_data_resep(page, search);
}
function load_data_resep(page, search, id) {
    pg = page; src = search; id_barg = id;
    var awal = $('#awal').val();
    var akhir= $('#akhir').val();
    var dokter = $('#id_dokter').val();
    var pasien = $('#id_pasien').val();
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/lap-resep-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&awal='+awal+'&akhir='+akhir+'&id_dokter='+dokter+'&id_pasien='+pasien,
        success: function(data) {
            $('#result-info').html(data);
        }
    });
}
$(function() {
    //$('#pmr').hide();
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#pmr').button().click(function() {
        var id_pasien = $('#id_pasien').val();
        if (id_pasien === '') {
            alert_dinamic('Nama pasien harus dipilih !','#pasien'); return false;
        }
        var wWidth = $(window).width();
        var dWidth = wWidth * 0.8;
        var wHeight= $(window).height();
        var dHeight= wHeight * 1;
        var x = screen.width/2 - dWidth/2;
        var y = screen.height/2 - dHeight/2;
        window.open('pages/pmr-print.php?id_pasien='+id_pasien, 'Stok', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
    });
    $('#search').button().click(function() {
        load_data_resep();
    });
    $('#reset').button().click(function() {
        $('#awal,#akhir').val('<?= date("d/m/Y") ?>');
        $('#dokter,#id_dokter,#pasien,#id_pasien').val('');
        $('#result-info').html('');
    });
    $('#cetak').button().click(function() {
        var awal = $('#awal').val();
        var akhir= $('#akhir').val();
        var id   = $('#id_barang').val();
        window.open('pages/arus-stok-print.php?awal='+awal+'&akhir='+akhir+'&id='+id, 'Stok', 'width=800px, height=350px, resizable=1, scrollable=1');
    });
    var lebar = $('#supplier').width();
    $('#dokter').autocomplete("models/autocomplete.php?method=dokter",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<br/> '+data.no_str+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_dokter').val(data.id);
        $('#pasien').focus().select();
    });
    $('#pasien').autocomplete("models/autocomplete.php?method=pasien",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.id+' '+data.nama+'<br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.id+' '+data.nama);
        $('#id_pasien').val(data.id);
        $('#keterangan').focus().select();
    });
});
</script>
<h1 class="margin-t-0">Laporan Resep</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td></tr>
    <tr><td>Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>
    <tr><td>Nama Dokter:</td><td><?= form_input('dokter', NULL, 'id=dokter size=40') ?><?= form_hidden('id_dokter', NULL, 'id=id_dokter') ?></td></tr>
    <tr><td></td><td>
        <?= form_button('Tampilkan', 'id=search') ?> 
        <?= form_button('Reset', 'id=reset') ?> 
        <?= form_button('Cetak Laporan', 'id=cetak') ?>
        <?= form_button('Cetak PMR', 'id=pmr') ?>
    </td></tr>
</table>
</div>
<div id="result-info">
    
</div>