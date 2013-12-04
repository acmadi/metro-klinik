<?php
$subNav = array(
	"Barang Aktif ; barang.php ; #509601;",
        "Barang Tidak Aktif ; barang-inactive.php ; #509601;",
        "Pelengkap ; pelengkap.php ; #509601;",
        "Item Kit ; item-kit.php ; #509601;"
);

set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
?>
<script>
load_data_itemkit();
$.cookie('session', 'false');
$(document).keydown(function(e) {
    if (e.keyCode === 120) {
        if ($.cookie('session') === 'false') {
            form_add();
        }
    }
});
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
    load_data_itemkit();
});
function hitung_estimasi() {
    
    var jml_baris = $('.tr_rows').length;
    var estimasi = 0;
    for (i = 1; i <= jml_baris; i++) {
        var subtotal = parseInt(currencyToNumber($('#subtotal'+i).html()));
        estimasi = estimasi + subtotal;
    }
    var margin_pr = $('#margin_pr').val()/100;
    var margin_rp = parseInt(currencyToNumber($('#margin_rp').val()));
    if (margin_pr !== 0) {
        termargin = estimasi+(estimasi*(margin_pr));
    } else {
        termargin = estimasi+margin_rp;
    }
    
    var diskon_pr = $('#diskon_pr').val()/100;
    var diskon_rp = parseInt(currencyToNumber($('#diskon_rp').val()));
    if (diskon_pr !== 0) {
        terdiskon = termargin-(termargin*(diskon_pr));
    } else {
        terdiskon = termargin-diskon_rp;
    }
    //alert(margin_pr+' - '+margin_rp+' - '+diskon_pr+' '+diskon_rp);
    $('#estimasi').html(numberToCurrency(parseInt(terdiskon)));
    $('#harga_jual').val(parseInt(terdiskon));
}

function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
    var jml_baris = $('.tr_rows').length;
    var index = 0;
    for(i = 1; i <= jml_baris; i++) {
        $('.tr_rows:eq('+index+')').children('td:eq(0)').html(i);
        $('.tr_rows:eq('+index+')').children('td:eq(1)').children('.id_barang').attr('id','id_barang'+i);
        $('.tr_rows:eq('+index+')').children('td:eq(2)').children('.kemasan').attr('id','kemasan'+i);
        $('.tr_rows:eq('+index+')').children('td:eq(3)').children('.jumlah').attr('id','jumlah'+i);
        $('.tr_rows:eq('+index+')').children('td:eq(4)').attr('id','harga'+i);
        $('.tr_rows:eq('+index+')').children('td:eq(5)').attr('id','subtotal'+i);
        index++;
    }
    hitung_estimasi();
}

function add_new_rows(id_brg, nama_brg, jumlah, id_kemasan, nama_kemasan) {
    if (id_kemasan === null) {
        alert('Kemasan tidak boleh kosong !');
        return false;
    }
    var jml     = $('.tr_rows').length+1;
    var kemasan = nama_kemasan;
    if (nama_kemasan === undefined) {
        kemasan = $('#kemasan option:selected').text();
    }
    var str = '<tr class="tr_rows">'+
                '<td align=center>'+jml+'</td>'+
                '<td>&nbsp;'+nama_brg+' <input type=hidden name=id_barang[] value="'+id_brg+'" class=id_barang id=id_barang'+jml+' /></td>'+
                '<td align=center>'+kemasan+'<input type=hidden name=kemasan[] id=kemasan'+jml+' class=kemasan value="'+id_kemasan+'" /></td>'+
                '<td><input type=text name=jumlah[] id=jumlah'+jml+' value="'+jumlah+'" class=jumlah size=10 style="text-align: center;" /></td>'+
                '<td align=right id=harga'+jml+'></td>'+
                '<td align=right id=subtotal'+jml+'></td>'+
                '<td align=center><img onclick=removeMe(this); title="Klik untuk hapus" src="img/icons/delete.png" class=add_kemasan align=left /></td>'+
              '</tr>';
    $('#item-kit-list tbody').append(str);
    $.ajax({
        url: 'models/autocomplete.php?method=get_detail_harga_barang_pemesanan&id='+id_brg+'&id_kemasan='+id_kemasan,
        dataType: 'json',
        cache: false,
        success: function(data) {
            var subtotal = data.esti*jumlah;
            //alert(subtotal+' '+data.esti+' '+jumlah);
            $('#harga'+jml).html(numberToCurrency(parseInt(data.esti)));
            $('#subtotal'+jml).html(numberToCurrency(parseInt(subtotal)));
            hitung_estimasi();
        }
    });
    $('#jumlah'+jml).keyup(function() {
        var harga = parseFloat(currencyToNumber($('#harga'+jml).html()));
        var jumlah= $(this).val();
        var subtotal = harga*jumlah;
        $('#subtotal'+jml).html(numberToCurrency(subtotal));
        hitung_estimasi();
    });
}

function form_add() {
    var str = '<div id="item_kit"><form id="form_item_kit">'+
                '<input type=hidden name="id_item_kit" id="id_item_kit" />'+
                '<table width=100% class=data-input><tr valign=top><td width=50%>'+
                    '<table width=100%>'+
                        '<tr><td width=30%>Nama Item:</td><td><?= form_input('nama_item', NULL, 'id=nama_item size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                        '<tr><td>Margin:</td><td><?= form_input('margin_pr', '0', 'id=margin_pr size=10') ?> %, Rp. <?= form_input('margin_rp', '0', 'id=margin_rp onblur="FormNum(this)" size=10') ?></td></tr>'+
                        '<tr><td>Diskon:</td><td><?= form_input('diskon_pr', '0', 'id=diskon_pr size=10') ?> %, Rp. <?= form_input('diskon_rp', '0', 'id=diskon_rp onblur="FormNum(this)" size=10') ?></td></tr>'+
                    '</table>'+
                    '</td><td width=50%>'+
                    '<table width=100%>'+
                        '<tr><td width=20%>Nama Barang:</td><td width=50%><?= form_input('barang', NULL, 'id=barang size=40') ?><?= form_hidden('id_barang', NULL, 'id=id_barang') ?></td></tr>'+
                        '<tr><td>Kemasan:</td><td><select name=id_kemasan id=kemasan style="min-width: 86px;"><option value="">Pilih ...</option></select></td></tr>'+
                        '<tr><td>Jumlah:</td><td><?= form_input('jumlah', NULL, 'id=jumlah size=10') ?></td></tr>'+
                        '<tr><td>Harga:</td><td style="font-size: 40px;"><?= form_hidden('harga_jual', NULL, 'id=harga_jual') ?><span>Rp</span> <span id=estimasi>0</span>, 00</td></tr>'+
                    '</table>'+
                    '</td></tr>'+
                '</table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="item-kit-list"><thead>'+
                    '<tr>'+
                        '<th width=5%>No.</th>'+
                        '<th width=44%>Nama Barang</th>'+
                        '<th width=20%>Kemasan</th>'+
                        '<th width=10%>Jumlah</th>'+
                        '<th width=10%>Harga</th>'+
                        '<th width=10%>Subtotal</th>'+
                        '<th width=1%>#</th></tr></thead>'+
                    '<tbody></tbody>'+
                '</table>'+
              '</form></div>';
    $('body').append(str);
    $('#jumlah').keydown(function(e) {
        if (e.keyCode === 13) {
            add_new_rows($('#id_barang').val(), $('#barang').val(), $('#jumlah').val(), $('#kemasan').val());
            $('#id_barang').val('');
            $('#jumlah').val('');
            $('#kemasan').html('').append('<option value="">Pilih ...</option>');
            $('#barang').val('').focus();
            
        }
    });
    $(document).keydown(function(e) {
        if (e.keyCode === 119) {
            if ($.cookie('session') === 'true') {
                $('#form_item_kit').submit();
            }
        }
    });
    $('#margin_pr').blur(function() {
        if ($('#margin_pr').val() !== '' && $('#margin_pr').val() !== '0') {
            $('#margin_rp').val('0');
        }
        hitung_estimasi();
    });
    $('#margin_rp').blur(function() {
        if ($('#margin_rp').val() !== '' && $('#margin_rp').val() !== '0') {
            $('#margin_pr').val('0');
        }
        hitung_estimasi();
    });
    $('#diskon_pr').blur(function() {
        if ($('#diskon_pr').val() !== '' && $('#diskon_pr').val() !== '0') {
            $('#diskon_rp').val('0');
        }
        hitung_estimasi();
    });
    $('#diskon_rp').blur(function() {
        if ($('#diskon_rp').val() !== '' && $('#diskon_rp').val() !== '0') {
            $('#diskon_pr').val('0');
        }
        hitung_estimasi();
    });
    $('#barang').keydown(function(e) {
        if (e.keyCode === 13) {
            $('#kemasan').focus();
            //$('#jumlah').val('1').focus().select();
        }
    });
    $('#kemasan').keydown(function(e) {
        if (e.keyCode === 13) {
            //$('#kemasan').focus();
            $('#jumlah').val('1').focus().select();
        }
    });
    var lebar = $('#barang').width();
    $('#barang').autocomplete("models/autocomplete.php?method=barang",
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
        $('#id_barang').val(data.id);
        $('#kemasan').html('');
        $.getJSON('models/autocomplete.php?method=get_kemasan_barang&id='+data.id, function(data){
            if (data === null) {
                alert('Kemasan tidak barang tidak tersedia !');
            } else {
                $.each(data, function (index, value) {
                    $('#kemasan').append("<option value='"+value.id_kemasan+"'>"+value.nama+"</option>");
                });
            }
        });
    });
    var wWidth = $(window).width();
    var dWidth = wWidth * 1;
    
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    $('#item_kit').dialog({
        title: 'Tambah Item Kit',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan (F8)": function() {
                $('#form_item_kit').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $.cookie('session', 'true');
        }
    });
    $('#form_item_kit').submit(function() {
        if ($('#nama_item').val() === '') {
            alert_empty('item', '#nama_item'); return false;
        }
        if ($('.tr_rows').length === 0) {
            alert_dinamic('Belum ada barang yang terpilih !','#barang'); return false;
        }
        $.ajax({
            url: 'models/update-masterdata.php?method=save_item_kit',
            data: $(this).serialize(),
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    alert_refresh('Data item kit berhasil di simpan');
                    $('#nama_item').val('');
                    $('#margin_pr, #margin_rp, #diskon_pr, #diskon_rp').val('0');
                    $('#estimasi').html('0');
                    $('#item-kit-list tbody').html('');
                }
            }
        });
        return false;
    });
}
function load_data_itemkit(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/item-kit-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_itemkit='+id_barg,
        success: function(data) {
            $('#result-itemkit').html(data);
        }
    });
}

function edit_itemkit(id, data) {
    form_add();
    var arr = data.split('#');
    $('#id_item_kit').val(id);
    $('#nama_item').val(arr[0]);
    $('#margin_pr').val(arr[1]);
    $('#margin_rp').val(arr[2]);
    $('#diskon_pr').val(arr[3]);
    $('#diskon_rp').val(arr[4]);
    $('#harga_jual').val(arr[5]);
    $('#estimasi').html(numberToCurrency(arr[5]));
    $.getJSON('models/autocomplete.php?method=get_data_item_kit&id='+id, function(data){
        $.each(data, function (index, value) {
            add_new_rows(value.id_barang, value.nama_barang, value.jumlah, value.id_kemasan, value.nama_kemasan);
        });
    });
}

function paging(page, tab, search) {
    load_data_itemkit(page, search);
}
function delete_itemkit(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: 'models/update-masterdata.php?method=delete_itemkit&id='+id,
                    cache: false,
                    success: function() {
                        load_data_penjualan(page);
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
<h1 class="margin-t-0">Item Kit (Packaging)</h1>
<hr>
<button id="button">Tambah Data (F9)</button>
<button id="reset">Reset</button>
<div id="result-itemkit">
    
</div>