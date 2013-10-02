<?php
$subNav = array(
	"Lap. Penjualan Resep ; lap-penjualan.php ; #509601;",
        "Lap. Penjualan Bebas; lap-penjualan-nr.php ; #509601;",
);

set_include_path("../");
include_once("inc/essentials.php");
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
$(function() {
    $('input[type=button]').button();
    $('#awal,#akhir').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#reset').click(function() {
        $('input[type=text]').val('');
        $('#awal,#akhir').val('<?= date("d/m/Y") ?>');
        $('#result-info').html('');
    });
    var lebar = $('#pasien').width();
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
            $('#id_pasien').val('');
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
        $('#id_pasien').val(data.id);
        $('#keterangan').focus().select();
    });
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
            $('#id_dokter').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<br/> '+data.no_str+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_dokter').val(data.id);
    });
    $('#search').click(function() {
        load_data_penjualan();
    });
});

function cetak() {
    var awal    = $('#awal').val();
    var akhir   = $('#akhir').val();
    var pasien  = $('#id_pasien').val();
    var dokter  = $('#id_dokter').val();
    var status  = $('input:checked').val();
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.9;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('pages/penjualan-print.php?awal='+awal+'&akhir='+akhir+'&pasien='+pasien+'&dokter='+dokter+'&status='+status, 'cetak penjualan', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}
function load_data_penjualan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    var awal    = $('#awal').val();
    var akhir   = $('#akhir').val();
    var pasien  = $('#id_pasien').val();
    var dokter  = $('#id_dokter').val();
    var status  = $('input:checked').val();
    if (status === 'group') {
        $.ajax({
            url: 'pages/lap-penjualan-list.php',
            cache: false,
            data: 'page='+pg+'&search='+src+'&id_penjualan='+id_barg+'&hal=laporan&awal='+awal+'&akhir='+akhir+'&pasien='+pasien+'&dokter='+dokter,
            success: function(data) {
                $('#result-info').html(data);
            }
        });
    } else {
        $.ajax({
            url: 'pages/lap-penjualan-detail.php',
            cache: false,
            data: 'page='+pg+'&search='+src+'&id_penjualan='+id_barg+'&awal='+awal+'&akhir='+akhir+'&pasien='+pasien+'&dokter='+dokter,
            success: function(data) {
                $('#result-info').html(data);
            }
        });
    }
}
</script>
<h1 class="margin-t-0">Laporan Penjualan Resep</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td></tr>
    <tr><td>Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>  
    <tr><td>Dokter:</td><td><?= form_input('dokter', NULL, 'id=dokter size=40') ?><?= form_hidden('id_dokter', NULL, 'id=id_dokter') ?></td></tr>
    <tr><td></td><td><?= form_radio('ket', 'group', 'group', 'Group', TRUE) ?> <?= form_radio('ket', 'detail', 'detail', 'Detail', FALSE) ?></td></tr>
    <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?> <?= form_button('Cetak', 'id=cetak onclick=cetak();') ?></td></tr>
</table>
</div>
<div id="result-info">
    
</div>