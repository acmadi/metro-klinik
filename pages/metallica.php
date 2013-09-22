<?php
$subNav = array(
	"Penjualan Bebas ; penjualan-nr.php ; #509601;",
        "Penjualan Resep ; penjualan.php ; #509601;",
        "Entri Resep ; metallica.php ; #509601;"
);
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
$biaya_apoteker = tarif_load_data();
?>
<script type="text/javascript">
$(function() {
    $(document).tooltip();
    $.cookie('session', 'false');
    $(document).keydown(function(e) {
        if (e.keyCode === 120) { 
            if ($.cookie('session') === 'false') {
                form_add();
            }
        } 
    });
    load_data_resep();
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
        load_data_resep();
    });
    $('#addnewrows').click(function() {
        var row = $('.masterresep').length;
        addnoresep(row);
    });
});

function subTotal() {
    
    var jumlah = $('.tr_row').length-1;
    var total_jasa = 0;
    for(i = 0; i<= jumlah; i++) {
        var valjasa  = $('#ja'+i).val();
        var n=valjasa.split("-");
        var jasa = parseInt(n[1]);
        var total_jasa = total_jasa + jasa;
    }
    $('#totalbiaya').html(numberToCurrency(total_jasa));
}

function eliminate(el) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $('#alert').dialog().remove();
                var parent = el.parentNode.parentNode.parentNode.parentNode;
                parent.parentNode.removeChild(parent);
                var jumlah = $('.tr_row').length-1;
                for (i = 0; i <= jumlah; i++) {

                    $('.tr_row:eq('+i+')').children('.masterresep:eq(0)').children('.nr').attr('value',(i+1));
                    $('.tr_row:eq('+i+')').children('.masterresep:eq(0)').children('.nr').attr('id','nr'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.jr').attr('id','jr'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.jt').attr('id','jt'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ap').attr('id','ap'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.it').attr('id','it'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ja').attr('id','ja'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.ad').attr('id','ad'+i);
                    $('.tr_row:eq('+i+')').children('.psdg-right:eq(0)').children('.de').attr('id','de'+i);
                }
            },
            "Cancel": function() {
                $(this).dialog().remove();
            }
        }
    });
    
}

function eliminatechild(el,x,y) {
    ok=confirm('Anda yakin akan menghapus data ini ?');
    if (ok) {

        var parent = el.parentNode.parentNode.parentNode.parentNode.parentNode;
        parent.parentNode.removeChild(parent);
        var jumlah = $('.tr_rows').length-1;
    } else {
        return false;
    }
}

function addnoresep() {
    var jasa_apt    = $('#ja').val().split('-');
    var i           = $('.tr_rows').length+1;
    var barang      = $('#pb').val();
    var id_barang   = $('#id_pb').val();
    var no_r        = $('#nr').val();
    var permintaan  = $('#jr').val();
    var tebus       = $('#jt').val();
    var aturan_pakai= $('#ap').val();
    var iterasi     = $('#it').val();
    var jasa        = jasa_apt[1];
    var kekuatan    = $('#kekuatan').html();
    var dosis_racik = $('#dr').val();
    var jml_pakai   = $('#jmlpakai').val();
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+i+'</td>'+
                '<td><input type=text name=no_r[] id=no_r'+i+' value="'+no_r+'" style="text-align: center;" /></td>'+
                '<td>'+barang+' <input type=hidden name=id_barang[] id=id_barang'+i+' value="'+id_barang+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jp[] id=jp'+i+' value="'+permintaan+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jt[] id=jt'+i+' value="'+tebus+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=ap[] id=ap'+i+' value="'+aturan_pakai+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=it[] id=it'+i+' value="'+iterasi+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=dr[] id=dr'+i+' value="'+dosis_racik+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jpi[] id=jpi'+i+' value="'+jml_pakai+'" style="text-align: center;" /></td>'+
                '<td><input type=hidden name=id_tarif[] id=id_tarif'+i+' value="'+jasa_apt[0]+'" /> <input type=text name=jasa[] id=jasa'+i+' onkeyup=FormNum(this) value="'+numberToCurrency(jasa)+'" style="text-align: right;" /></td>'+
                '<td><input type=text name=hrg_barang[] id=hrg_barang'+i+' value="" style="text-align: right;" /></td>'+
                '<td class=aksi><img onclick=removeMe(this); title="Klik untuk hapus" src="img/icons/delete.png" class=add_kemasan align=left /></td>'+
              '</tr>';
        $('#resep-list tbody').append(str);
        $.ajax({
            url: 'models/autocomplete.php?method=get_detail_harga_barang_resep&id='+id_barang+'&jumlah='+jml_pakai,
            dataType: 'json',
            cache: false,
            success: function(data) {
                //hitung_detail_total(jml, jum, data.diskon_rupiah, data.diskon_persen, data.harga_jual);
                $('#hrg_barang'+i).val(numberToCurrency(parseInt(data.harga_jual*jml_pakai)));
                total_perkiraan_resep();
            }
        });
}

function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}

function total_perkiraan_resep() {
    var jumlah  = $('.tr_rows').length;
    var total   = 0;
    for (i = 1; i <= jumlah; i++) {
        var subtotal = parseInt(currencyToNumber($('#hrg_barang'+i).val()));
        total   = total + subtotal;
    }
    $('#total').html(numberToCurrency(parseInt(total)));
}

function hitung_jml_pakai() {
    var dosis_racik = parseInt($('#dr').val());
    var jumlah_tbs  = parseInt($('#jt').val());
    var kekuatan    = parseInt($('#kekuatan').html());
    //alert(dosis_racik+' '+jumlah_tbs+' '+kekuatan);
    var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
    var jml_pakai = isNaN(jumlah_pakai)?'':jumlah_pakai;
    $('#jmlpakai').val(jml_pakai);
}

function form_add() {
    var str = '<div id=form_resep>'+
                '<form id=resep_save>'+
                '<input type=hidden name=id_resep id=id_resep />'+
                '<table width=100% class=data-input><tr valign=top><td width=33% style="border-right: 1px solid #ccc;">'+
                    '<table width=100%>'+
                        '<tr><td width=25%>Waktu:</td><td><?= form_input('waktu', date("d/m/Y"), 'id=waktu size=10') ?></td></tr>'+
                        '<tr><td>Dokter:</td><td><?= form_input('dokter', NULL, 'id=dokter style="width: 90%"') ?><?= form_hidden('id_dokter', NULL, 'id=id_dokter') ?></td></tr>'+
                        '<tr><td>Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien style="width: 90%"') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>'+
                        '<tr><td>Keterangan:</td><td><?= form_input('keterangan', NULL, ' style="width: 90%" id=keterangan') ?></td></tr>'+
                    '</table></td><td width=33% style="padding-left: 10px; border-right: 1px solid #ccc;">'+
                    '<table width=100%>'+
                        '<tr><td width=25%>No. R/:</td><td><input type=text name=nr id=nr value="1" class=nr size=20 onkeyup=Angka(this) maxlength=2 /></td></tr>'+
                        '<tr><td>Permintaan:</td><td><input type=text name=jr id=jr class=jr size=20 onkeyup=Angka(this) /></td></tr>'+
                        '<tr><td>Jumlah Tebus:</td><td><input type=text name=jt id=jt class=jt onblur="hitung_jml_pakai();" onkeyup=Angka(this) size=20 /></td></tr>'+
                        '<tr><td>Aturan Pakai:</td><td><input type=text name=ap id=ap class=ap size=20 /></td></tr>'+
                        '<tr><td>Iterasi:</td><td><input type=text name=it id=it class=it size=10 value="0" onkeyup=Angka(this) /></td></tr>'+
                        '<tr><td>Jasa Apoteker:</td><td><select style="max-width: 100px;" onchange="subTotal()" name=ja id=ja><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option value="'.$value->id.'-'.$value->nominal.'"> Rp. '.$value->nominal.' '.$value->nama.'</option>'; } ?></select> F8 = No. R/ selanjutnya</td></tr>'+
                    '</table>'+
                    '</td><td width=33% style="padding-left: 10px;">'+
                    '<table align=right width=100%>'+
                        '<tr><td width=25%>Nama Produk:</td><td>  <input type=text name=pb id=pb class=pb style="width: 90%" />'+
                            '<input type=hidden name=id_pb id=id_pb class=id_pb /></td></tr>'+
                        '<tr><td>Kekuatan:</td><td><span class=label id=kekuatan>-</span></td></tr>'+
                        '<tr><td>Dosis Racik:</td><td> <input type=text name=dr id=dr class=dr size=10 onblur="hitung_jml_pakai();" /></td></tr>'+
                        '<tr><td>Jumlah Pakai:</td><td><?= form_input('jmlpakai', NULL, 'id=jmlpakai size=10') ?></td></tr>'+
                        '<tr><td>TOTAL:</td><td id=total style="font-size: 30px;"></td></tr>'+
                    '</table>'+
                    '</td></tr></table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="resep-list"><thead>'+
                    '<tr><th width=5%>No.</th>'+
                        '<th width=5%>No. R</th>'+
                        '<th width=25%>Nama Barang</th>'+
                        '<th width=10%>Jumlah<br/>Permintaan</th>'+
                        '<th width=10%>Jumlah<br/>Tebus</th>'+
                        '<th width=10%>Aturan Pakai</th>'+
                        '<th width=5%>Iterasi</th>'+
                        '<th width=8%>Dosis Racik</th>'+
                        '<th width=8%>Jumlah Pakai</th>'+
                        '<th width=10%>Jasa Apoteker</th>'+
                        '<th width=10%>Harga Barang</th>'+
                        '<th width=2%>#</th>'+
                    '</tr></thead>'+
                    '<tbody></tbody>'+
                '</table>'+
                '</form>'+
              '</div>';
    $('body').append(str);
    var lebar = $('#dokter').width();
    $('#jmlpakai').keydown(function(e) {
        if (e.keyCode === 13) {
            addnoresep();
            $('#pb,#id_pb,#dr,#jmlpakai').val('');
            $('#kekuatan').html('-');
            $('#pb').focus();
        }
    });
    $(document).keydown(function(e) {
        if (e.keyCode === 119) {
            var next = (isNaN($('#nr').val())?'0':parseInt($('#nr').val()))+1;
            $('#nr').val(next);
            $('#jr,#jt,#ap,#it,#ja').val('');
            $('#jr').focus();
        }
    });
    $('#keterangan').keydown(function(e) {
        if (e.keyCode === 13) { $('#nr').focus().select(); }
    });
    $('#nr').keydown(function(e) {
        if (e.keyCode === 13) { $('#jr').focus().select(); }
    });
    $('#jr').keydown(function(e) {
        if (e.keyCode === 13) { $('#jt').focus().select(); }
    });
    $('#jt').keydown(function(e) {
        if (e.keyCode === 13) { $('#ap').focus().select(); }
    });
    $('#ap').keydown(function(e) {
        if (e.keyCode === 13) { $('#it').focus().select(); }
    });
    $('#it').keydown(function(e) {
        if (e.keyCode === 13) { $('#ja').focus().select(); }
    });
    $('#ja').keydown(function(e) {
        if (e.keyCode === 13) { $('#pb').focus().select(); }
    });
    $('#dr').keydown(function(e) {
        if (e.keyCode === 13) { $('#jmlpakai').focus().select(); }
    });
    $('#pb').autocomplete("models/autocomplete.php?method=barang",
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
            var str = '<div class=result>'+data.nama_barang+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama_barang);
        $('#id_pb').val(data.id);
        $('#kekuatan').html(data.kekuatan);
        $('#dr').val(data.kekuatan);
        hitung_jml_pakai();
        $('#dr').focus().select();
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
            var str = '<div class=result>'+data.nama+'<br/> '+data.alamat+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_pasien').val(data.id);
        $('#keterangan').focus().select();
    });
    $('#resep_save').submit(function() {
        if ($('#id_dokter').val() === '') {
            alert_empty('dokter','#dokter'); return false;
        }
        if ($('#id_pasien').val() === '') {
            alert_empty('pasien','#pasien'); return false;
        }
        if ($('.tr_rows').length === 0) {
            alert_dinamic('Barang belum ada yang dipilih !','#barang'); return false;
        }
        $.ajax({
            type: 'POST',
            url: 'models/update-transaksi.php?method=save_resep',
            data: $(this).serialize(),
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (data.action === 'add') {
                        alert_tambah('#dokter');
                        $('input:text,select').val('');
                        $('#resep-list').html('');
                        load_data_resep();
                    } else {
                        alert_edit();
                        load_data_resep($('.noblock').html());
                    }
                }
            }
        });
        return false;
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#form_resep').dialog({
        title: 'Tambah Resep',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#resep_save').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#dokter').focus();
            $.cookie('session', 'true');
        }
    });
}

function load_data_resep(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/resep-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_resep='+id_barg,
        success: function(data) {
            $('#result-resep').html(data);
        }
    });
}

function edit_resep(data, id) {
    var arr = data.split('#');
    form_add();
    $('#id_resep').val(arr[0]);
    $('#id_dokter').val(arr[1]);
    $('#dokter').val(arr[2]);
    $('#id_pasien').val(arr[3]);
    $('#pasien').val(arr[4]);
    $('#keterangan').val(arr[5]);
    $.ajax({
        url: 'pages/resep-edit.php?id='+id,
        cache: false,
        success: function(msg) {
            $('#resep-list tbody').html(msg);
            total_perkiraan_resep();
        }
    });
}

function delete_resep(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_resep&id='+id,
                    cache: false,
                    success: function() {
                        load_data_resep(page);
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

function paging(page, tab, search) {
    load_data_resep(page, search);
}
</script>
<h1 class="margin-t-0">Resep</h1>
<hr>
<button id="button">Resep Baru (F9)</button>
<button id="reset">Reset</button>
<div id="result-resep">
    
</div>