<?php
session_start();
$subNav = array(
	"Penjualan Bebas ; penjualan-nr.php ; #509601;",
        "Entri Resep ; resep.php ; #509601;"
);
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
$biaya_apoteker = tarif_load_data();

$array = array(
    '1' => array('1','1'),
    '2' => array('1/4','0.25'),
    '3' => array('1/3','0.33'),
    '4' => array('1/2','0.5'),
    '5' => array('1,5','1.5'),
    '6' => array('2','2'),
    '7' => array('3','3'),
    '8' => array('4','4'),
    '9' => array('5','5'));
?>
<script type="text/javascript">

    //$(document).tooltip();
    $('#nor').click(function() {
        
    });
    $.cookie('session', 'false');
    $(document).keydown(function(e) {
        if (e.keyCode === 120) { 
            //alert($.cookie('session'));
            if ($.cookie('session') === 'false') {
                $('#button').click();
            }
        }
    });
    load_data_resep();
    $('#button').button({
        icons: {
            primary: 'ui-icon-newwin'
        }
    }).click(function() {
        form_receipt();
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
    //var aturan_pakai= $('#a').val()+'DD'+$('#p').val();
    var a           = $('#a').val();
    var p           = $('#p').val();
    var iterasi     = $('#it').val();
    var jasa        = jasa_apt[1];
    //var kekuatan    = $('#kekuatan').html();
    var dosis_racik = $('#dr').val();
    var jml_pakai   = $('#jmlpakai').val();
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+i+'</td>'+
                '<td><input type=text name=no_r[] id=no_r'+i+' value="'+no_r+'" style="text-align: center;" /></td>'+
                '<td>'+barang+' <input type=hidden name=id_barang[] id=id_barang'+i+' value="'+id_barang+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jp[] id=jp'+i+' value="'+permintaan+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jt[] id=jt'+i+' value="'+tebus+'" style="text-align: center;" /></td>'+
                '<td align=center id=sisa'+i+'></td>'+
                '<td><input type=text name=a[] id=a'+i+' value="'+a+'" style="text-align: right; width: 40%" /> X <input type=text name=p[] id=p'+i+' value="'+p+'" style="text-align: left; width: 40%" /></td>'+
                '<td><input type=text name=it[] id=it'+i+' value="'+iterasi+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=dr[] id=dr'+i+' value="'+dosis_racik+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jpi[] id=jpi'+i+' value="'+jml_pakai+'" style="text-align: center;" /></td>'+
                '<td><input type=hidden name=id_tarif[] id=id_tarif'+i+' value="'+jasa_apt[0]+'" /> <input type=text name=jasa[] id=jasa'+i+' onkeyup=FormNum(this) value="'+numberToCurrency(jasa)+'" style="text-align: right;" /></td>'+
                '<td><input type=text name=hrg_barang[] id=hrg_barang'+i+' value="" style="text-align: right;" />'+
                '<input type=hidden name=hrg_pokok[] id=hrg_pokok'+i+' /></td>'+
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
                $('#hrg_pokok'+i).val(numberToCurrency(parseInt(data.harga_jual)));
                total_perkiraan_resep();
            }
        });
        $.ajax({
        url: 'models/autocomplete.php?method=get_stok_sisa&id='+id_barang,
        dataType: 'json',
        cache: false,
        success: function(data) {
            if (data.sisa === null) {
                sisa = '0';
            } else {
                sisa = data.sisa;
            }
            $('#sisa'+i).html(sisa);
        }
    });
}

function addnoresep_ik() {
    var jasa_apt    = $('#ja').val().split('-');
    var i           = $('.tr_rows').length+1;
    var barang      = $('#pb').val();
    var id_barang   = $('#id_pb').val();
    var no_r        = $('#nr').val();
    var permintaan  = $('#jr').val();
    var tebus       = $('#jt').val();
    //var aturan_pakai= $('#a').val()+'DD'+$('#p').val();
    var a           = $('#a').val();
    var p           = $('#p').val();
    var iterasi     = $('#it').val();
    var jasa        = jasa_apt[1];
    //var kekuatan    = $('#kekuatan').html();
    var dosis_racik = $('#dr').val();
    var jml_pakai   = $('#jmlpakai').val();
    var harga_jual  = numberToCurrency($('#hjual').val());
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+i+'</td>'+
                '<td><input type=text name=no_rik[] id=no_r'+i+' value="'+no_r+'" style="text-align: center;" /></td>'+
                '<td>'+barang+' <input type=hidden name=id_ikit[] value="'+id_barang+'" class=id_ikit id=id_ikit'+i+' /></td>'+
                '<td><input type=text name=jpik[] id=jp'+i+' value="'+permintaan+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jtik[] id=jt'+i+' value="'+tebus+'" style="text-align: center;" /></td>'+
                '<td align=center id=sisa'+i+'>-</td>'+
                '<td><input type=text name=aik[] id=a'+i+' value="'+a+'" style="text-align: right; width: 40%" /> X <input type=text name=pik[] id=p'+i+' value="'+p+'" style="text-align: left; width: 40%" /></td>'+
                '<td><input type=text name=itik[] id=it'+i+' value="'+iterasi+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=drik[] id=dr'+i+' value="'+dosis_racik+'" style="text-align: center;" /></td>'+
                '<td><input type=text name=jpiik[] id=jpi'+i+' value="'+jml_pakai+'" style="text-align: center;" /></td>'+
                '<td><input type=hidden name=id_tarifik[] id=id_tarif'+i+' value="'+jasa_apt[0]+'" /> <input type=text name=jasaik[] id=jasa'+i+' onkeyup=FormNum(this) value="'+numberToCurrency(jasa)+'" style="text-align: right;" /></td>'+
                '<td><input type=text name=hrg_barangik[] id=hrg_barang'+i+' value="'+harga_jual+'" style="text-align: right;" /></td>'+
                '<td class=aksi><img onclick=removeMe(this); title="Klik untuk hapus" src="img/icons/delete.png" class=add_kemasan align=left /></td>'+
              '</tr>';
        $('#resep-list tbody').append(str);
        total_perkiraan_resep();
}

function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}

function total_perkiraan_resep() {
    var jumlah  = $('.tr_rows').length;
    var total   = 0;
    //var jasa    = 0;
    for (i = 1; i <= jumlah; i++) {
        var subtotal = parseInt(currencyToNumber($('#hrg_barang'+i).val()));
        var jasa     = parseInt(currencyToNumber($('#jasa'+i).val()));
        total   = total + subtotal + jasa;
    }
    $('#total').html(numberToCurrency(parseInt(total)));
    $('#total_penjualan').val(total);
}

function hitung_jml_pakai() {
    var dosis_racik = ($('#dr').val())*1;
    var jumlah_tbs  = parseInt($('#jt').val());
    var kekuatan    = ($('#kekuatan').html())*1;
    //alert(dosis_racik+' '+jumlah_tbs+' '+kekuatan);
    
    var jumlah_pakai= (dosis_racik*jumlah_tbs)/kekuatan;
    var jml_pakai = isNaN(jumlah_pakai)?'':jumlah_pakai;
    $('#jmlpakai').val(jml_pakai);
}

function cetak_copy_resep(id_resep) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('pages/copy-resep.php?id='+id_resep,'Resep Cetak','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function print_etiket(id_resep, no_r) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('pages/etiket.php?id_resep='+id_resep+'&no_r='+no_r,'Etiket','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

function form_receipt() {
    var str = '<div id=form_resep>'+
                '<form id=resep_save>'+
                '<input type=hidden name=id_resep id=id_resep />'+
                '<input type=hidden name=id_pendaftaran id=id_pendaftaran />'+
                '<table width=100% class=data-input><tr valign=top><td width=33% style="border-right: 1px solid #ccc;">'+
                    '<table width=100%>'+
                        '<tr><td width=25%>Nomor Resep:</td><td><?= form_input('noresep', NULL, 'id=noresep size=10') ?></td></tr>'+
                        '<tr><td>Tanggal:</td><td><?= form_input('waktu', date("d/m/Y"), 'id=waktu size=10 disabled') ?></td></tr>'+
                        '<tr><td>Dokter:</td><td><?= form_input('dokter', NULL, 'id=dokter style="width: 90%"') ?><?= form_hidden('id_dokter', NULL , 'id=id_dokter') ?></td></tr>'+
                        '<tr><td>Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien style="width: 90%"') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>'+
                        '<tr><td>Keterangan:</td><td><?= form_input('keterangan', NULL, ' style="width: 90%" id=keterangan') ?></td></tr>'+
                    '</table></td><td width=33% style="padding-left: 10px; border-right: 1px solid #ccc;">'+
                    '<table width=100%>'+
                        '<tr><td width=25%>No. R/:</td><td><input type=text name=nr id=nr value="1" class=nr size=20 onkeyup=Angka(this) maxlength=2 /></td></tr>'+
                        '<tr><td>Permintaan:</td><td><input type=text name=jr id=jr class=jr size=20 onkeyup=Angka(this) /></td></tr>'+
                        '<tr><td>Jumlah Tebus:</td><td><input type=text name=jt id=jt class=jt onblur="hitung_jml_pakai();" onkeyup=Angka(this) size=20 /></td></tr>'+
                        '<tr><td>Aturan Pakai:</td><td><select name=a id=a style="min-width: 65px;">'+
                        '<?php for ($i = 1; $i<=10;$i++) { echo '<option value="'.$i.'">'.$i.'</option>'; } ?></select> X <select name=p id=p style="min-width: 65px;">'+
                        '<?php foreach($array as $key => $i) { echo '<option value="'.$i[1].'">'.$i[0].'</option>'; } ?></select>'+
                        '</td></tr>'+
                        '<tr><td>Iterasi:</td><td><input type=text name=it id=it class=it size=20 value="0" onkeyup=Angka(this) /></td></tr>'+
                        '<tr><td>Jasa Apoteker:</td><td><select style="width: 100px;" onchange="subTotal()" name=ja id=ja><option value="0-0">Pilih biaya ..</option><?php foreach ($biaya_apoteker as $value) { echo '<option value="'.$value->id.'-'.$value->nominal.'"> Rp. '.$value->nominal.' '.$value->nama.'</option>'; } ?></select> F4 = No. R/ selanjutnya</td></tr>'+
                    '</table>'+
                    '</td><td width=33% style="padding-left: 10px;">'+
                    '<table align=right width=100%>'+
                        '<tr><td width=25%>Nama Produk:</td><td>  <input type=text name=pb id=pb class=pb style="width: 90%" />'+
                            '<input type=hidden name=id_pb id=id_pb class=id_pb /></td></tr>'+
                        '<tr><td>Kekuatan:</td><td><span class=label id=kekuatan>-</span></td></tr>'+
                        '<tr><td>Dosis Racik:</td><td> <input type=text name=dr id=dr class=dr size=10 onblur="hitung_jml_pakai();" /></td></tr>'+
                        '<tr><td>Jumlah Pakai:</td><td><?= form_input('jmlpakai', NULL, 'id=jmlpakai size=10') ?><?= form_hidden(NULL, NULL, 'id=status') ?><?= form_hidden(NULL, NULL, 'id=hjual') ?></td></tr>'+
                        '<tr><td>TOTAL:</td><td style="font-size: 30px;"><span>Rp </span><span id=total></span>,00 <?= form_hidden('total_penjualan', NULL, 'id=total_penjualan') ?></td></tr>'+
                    '</table>'+
                    '</td></tr></table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="resep-list"><thead>'+
                    '<tr><th width=5%>No.</th>'+
                        '<th width=5%>No. R</th>'+
                        '<th width=25%>Nama Barang</th>'+
                        '<th width=10%>Jumlah<br/>Permintaan</th>'+
                        '<th width=10%>Jumlah<br/>Tebus</th>'+
                        '<th width=5%>Sisa<br/>Stok</th>'+
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
    $('#form_resep').keydown(function(e) {
        if (e.keyCode === 119) {
            $('#resep_save').submit();
        }
        if (e.keyCode === 115) {
            if ($.cookie('session') === 'true') {
                var prev = $.cookie('nomor_r');
                var next = parseInt(prev)+1;
                $('#nr').val(next);
                $('#jr,#jt,#ap,#ja').val('');
                $('#it').val('0');
                $('#jr').focus();
                $.cookie('nomor_r', next);
            }
        }
    });
    var lebar = $('#dokter').width();
    $('#jmlpakai').keydown(function(e) {
        if (e.keyCode === 13) {
            if ($('#status').val() === 'TRUE') {
                addnoresep_ik();
                $('#pb,#id_pb,#dr,#jmlpakai,#ja,#status,#hjual').val('');
                $('#kekuatan').html('-');
                $('#pb').focus();
            } else {
                addnoresep();
                $('#pb,#id_pb,#dr,#jmlpakai,#ja').val('');
                $('#kekuatan').html('-');
                $('#pb').focus();
            }
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
        if (e.keyCode === 13) { $('#a').focus().select(); }
    });
    $('#a').keydown(function(e) {
        if (e.keyCode === 13) { $('#p').focus().select(); }
    });
    $('#p').keydown(function(e) {
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
        if (data.status !== 'TRUE') {
            hitung_jml_pakai();
        } else {
            $('#status').val(data.status);
            $('#hjual').val(data.harga_jual);
        }
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
    $('#pasien').autocomplete("models/autocomplete.php?method=pasien_pendaftar",
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
        $('#id_pendaftaran').val(data.id_pendaftaran);
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
                        cetak_copy_resep(data.id);
                        alert_refresh('Data berhasil disimpan');
                        $('input:text,select').val('');
                        $('#resep-list tbody, #total').html('');
                        //load_data_resep();
                        //location.reload();
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
            "Simpan (F8)": function() {
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
            $.ajax({
                url: 'models/autocomplete.php?method=get_no_resep',
                cache: false,
                dataType: 'json',
                success: function(data) {
                    $('#noresep').val(data);
                }
            });
            $('#dokter').focus();
            $.cookie('session', 'true');
            $.cookie('nomor_r', $('#nr').val());
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
    form_receipt();
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
<button id="nor" style="display: none;"></button>
<div id="result-resep">
    
</div>