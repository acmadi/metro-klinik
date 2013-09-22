<?php
$subNav = array(
	"Data Pendaftaran ; pendaftaran.php ; #509601;",
        "Data Pemeriksaan ; pemeriksaan.php ; #509601;"
);
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/transaksi.php");
include_once("pages/message.php");
?>
<script type="text/javascript">
$.cookie('session','false');
$(function() {
    load_data_pemeriksaan();
    $('#button').button({
        icons: {
            primary: 'ui-icon-newwin'
        }
    }).click(function() {
        form_add();
    });
    $('#reset').button({
        icons: {
            primary: 'ui-icon-refresh'
        }
    }).click(function() {
        load_data_pemeriksaan();
    });
    $(document).on('keydown', function(e) {
        if (e.keyCode === 120) {
            if ($.cookie('session') === 'false') {
                form_add();
            }
        }
    });
    
    $('#search').click(function() {
        load_data_penjualan();
    });
});

function add_diagnosis(id, nama) {
    var str = '<tr>'+
                '<td><input type=hidden name=id_diagnosis[] value="'+id+'" /> '+nama+'</td>'+
              '</tr>';
    $('.diagnosis').append(str);
    $('#diagnosis,#id_diagnosis').val('');
    $('#diagnosis').focus();
}

function add_tindakan(id, nama, nominal) {
    var str = '<tr>'+
                '<td><input type=hidden name=id_tindakan[] value="'+id+'" /> '+nama+' <input type=hidden name=nominal[] value="'+nominal+'" /></td>'+
              '</tr>';
    $('.tindakan').append(str);
    $('#tindakan,#id_tindakan').val('');
    $('#tindakan').focus();
}

function form_add() {
    var str = '<div id=form_pemeriksaan>'+
                '<form id=save_pemeriksaan action="models/update-transaksi.php?method=save_pemeriksaan" enctype=multipart/form-data>'+
                    '<span id=output></span><?= form_hidden('id_pendaftaran', NULL, 'id=id_pendaftaran') ?>'+
                    '<table width=100% class=data-input><tr valign=top><td width=33%>'+
                    '<table width=100%>'+
                        '<tr><td>No. Pemeriksaan</td><td><?= form_input('nopemeriksaan', NULL, 'id=nopemeriksaan readonly size=10') ?></td></tr>'+
                        '<tr><td>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'id=tanggal size=10') ?></td></tr>'+
                        '<tr><td>Nomor PMR:</td><td><?= form_input('norm', NULL, 'id=norm size=40') ?></td></tr>'+
                        '<tr><td>Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>'+
                        '<tr><td>Dokter:</td><td><?= form_input('dokter', NULL, 'id=dokter size=40') ?><?= form_hidden('id_dokter', NULL, 'id=id_dokter') ?></td></tr>'+
                        '<tr><td>Foto Pasien:</td><td><?= form_upload('mFile') ?></td></tr>'+
                    '</table></td><td width=33%>'+
                    '<table width=100%>'+
                        '<tr><td valign=top>Anamnesis:</td><td><?= form_textarea('anamnesis', NULL, 'id=anamnesis cols=37 style="height: 30px"') ?></td></tr>'+
                        '<tr><td>Diagnosis:</td><td><?= form_input('diagnosis', NULL, 'id=diagnosis size=40') ?><?= form_hidden('id_diagnosis', NULL, 'id=id_diagnosis') ?></td></tr>'+
                        '<tr><td>Tindakan:</td><td><?= form_input('tindakan', NULL, 'id=tindakan size=40') ?><?= form_hidden('id_tindakan', NULL, 'id=id_tindakan') ?></td></tr>'+
                    '</table>'+
                    '</td><td id=foto></td></tr></table>'+
                    '<table width=100% cellspacing="0" class="list-data-input" id="penjualan-list">\n\
                        <thead><tr>'+
                            '<th width=50%>DIAGNOSIS</th>'+
                            '<th width=50%>TINDAKAN</th>'+
                        '</tr></thead>'+
                        '<tbody><tr><td valign=top><table width=100% class=diagnosis></table></td><td valign=top><table width=100% class=tindakan></td></tr></tbody>'+
                    '</table>'+
                '</form>'+
              '</div>';
    $('body').append(str);
    var lebar = $('#pasien').width();
    $('#pasien,#norm').autocomplete("models/autocomplete.php?method=pasien",
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
            var str = '<div class=result>'+data.id+'<br/> '+data.nama+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $('#norm').val(data.id);
        $('#pasien').val(data.nama);
        $('#id_pasien').val(data.id);
        $.ajax({
            url: 'models/autocomplete.php?method=get_photo_pemeriksaan&id_pelanggan='+data.id,
            dataType: 'json',
            cache: false,
            success: function(msg) {
                if (msg.foto !== null) {
                    $('#foto').html('<img src="img/pemeriksaan/'+msg.foto+'" height="120px"/>');
                } else {
                    $('#foto').html('');
                }
                $('#id_pendaftaran').val(msg.id);
            }
        });
        $('#dokter').focus();
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
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0,
        max: 100
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_dokter').val(data.id);
        //alert(data.id);
    });
    $('#diagnosis').autocomplete("models/autocomplete.php?method=diagnosis",
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
            var str = '<div class=result>'+data.topik+'<br/> '+data.sub_kode+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0,
        max: 100
    }).result(
    function(event,data,formated){
        $(this).val(data.topik);
        $('#id_diagnosis').val(data.id);
        add_diagnosis(data.id, data.topik);
    });
    $('#tindakan').autocomplete("models/autocomplete.php?method=tindakan",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].nama // nama field yang dicari
                };
            }
            $('#id_tindakan').val('');
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.nama+'<br/> '+data.nominal+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_tindakan').val(data.id);
        add_tindakan(data.id, data.nama, data.nominal);
    });
    $('#save_pemeriksaan').on('submit', function(e) {
        e.preventDefault();
        if ($('#id_pasien').val() === '') {
            alert_empty('Nomor pasien','#norm'); return false;
        }
        if ($('#id_dokter').val() === '') {
            alert_empty('Dokter','#dokter'); return false;
        }   
        $(this).ajaxSubmit({
            target: '#output',
            dataType: 'json',
            success:  function(data) {
                if (data.status === true) {
                    $('#form_pemeriksaan').dialog().remove();
                    alert_tambah('#norm');
                }
            }
        });
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_pemeriksaan').dialog({
        title: 'Pemeriksaan',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#save_pemeriksaan').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $.ajax({
                url: 'models/autocomplete.php?method=get_no_pemeriksaan',
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#nopemeriksaan').val(data);
                }
            });
            $.cookie('session', 'true');
        }
    });
    $('#tanggal').datepicker();
}

function load_data_pemeriksaan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/pemeriksaan-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_pemeriksaan='+id_barg,
        success: function(data) {
            $('#result-pemeriksaan').html(data);
        }
    });
}
function delete_pemeriksaan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_pemeriksaan&id='+id,
                    cache: false,
                    success: function() {
                        load_data_pemeriksaan(page);
                        $('#alert').dialog().remove();
                    }
                });
            },
            "Cancel": function() {
                $(this).dialog().remove();
            }
        }
    });
}
</script>
<h1>Pemeriksaan</h1>
<hr>
<!--<button id="button">Pemeriksaan (F9)</button>-->
<button id="reset">Reset</button>
<div id="result-pemeriksaan">
    
</div>