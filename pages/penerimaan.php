<?php
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/transaksi.php");
include_once("models/masterdata.php");
include_once("pages/message.php");

?>
<script type="text/javascript">
load_data_penerimaan();
    $.cookie('session', 'false');
    $(document).keydown(function(e) {
        if (e.keyCode === 120) {
            if ($.cookie('session') === 'false') {
                $('#button').click();
            }
        }
    });
function removeMe(el) {
    var parent = el.parentNode.parentNode;
    parent.parentNode.removeChild(parent);
}

function check_perubahan_hna(i) {
    var barang      = $('#barang'+i).val();
    var ppn         = $('#ppn').val()/100;
    var isi         = $('#isi'+i).val();
    var isi_satuan  = $('#isi_satuan'+i).val();
    var hna         = $('#existing_hna'+i).val(); // existing HNA
    var hrg_beli    = parseInt(currencyToNumber($('#harga'+i).val()));
    var new_var     = hrg_beli/(isi*isi_satuan); // pengali
    var new_hna     = (ppn*new_var)+new_var;
    if (hna > new_hna) {
        $('<div>HNA untuk barang '+barang+' mengalami perubahan dari Rp. '+numberToCurrency(hna)+' menjadi Rp. '+numberToCurrency(new_hna)+'</br> Apakah anda akan melakukan perubahan?</div>').dialog({
            title: 'Konfirmasi Perubahan HNA',
            autoOpen: true,
            modal: true,
            width: 400,
            buttons: {
                "Ya": function() {
                    $('#hna'+i).val(new_hna);
                    $(this).dialog().remove();
                    $('#diskon_pr'+i).focus().select();
                },
                "Tidak": function() {
                    $('#hna'+i).val(hna);
                    $(this).dialog().remove();
                    $('#diskon_pr'+i).focus().select();
                }
            }, close: function() {
                $('#hna'+i).val(hna);
                $(this).dialog().remove();
                $('#diskon_pr'+i).focus().select();
            }
        });
    }
    if (hna < new_hna) {
        $('#hna'+i).val(new_hna);
    }
}
function load_list_data(id_barang, nama_barang, id_satuan_beli, jumlah, hna, isi, isi_satuan) {
    var no   = $('.tr_rows').length+1;
    var list = '<tr class=tr_rows>'+
                    '<td align=center>'+no+'</td>'+
                    '<td><input type=text name=barang value="'+nama_barang+'" id=barang'+no+' size=50 /> <input type=hidden name=id_barang[] id=id_barang'+no+' value="'+id_barang+'" /></td>'+
                    '<td><select name=satuan[] id=satuan'+no+'></select></td>'+
                    '<td><input type=text name=jumlah[] id=jumlah'+no+' value="'+jumlah+'" size=10 /></td>'+
                    '<td><input type=text name=nobatch[] id=nobatch'+no+' size=10 /></td>'+
                    '<td><input type=text name=ed[] id=ed'+no+' size=10 /></td>'+
                    '<td><input type=text name=harga[] id=harga'+no+' onfocus=javascript:this.value=currencyToNumber(this.value); size=10 /></td>'+
                    '<td><input type=text name=diskon_pr[] id=diskon_pr'+no+' value="0" onblur="hitung_sub_total('+no+');" size=10 maxlength=5 /></td>'+
                    '<td><input type=text name=diskon_rp[] id=diskon_rp'+no+' value="0" onblur=FormNum(this); size=10 onfocus=javascript:this.value=currencyToNumber(this.value); /></td>'+
                    '<td><input type=text name=subtotal[] id=subtotal'+no+' size=10 />'+
                    '<input type=hidden name=existing_hna[] id=existing_hna'+no+' value="'+hna+'" />'+
                    '<input type=hidden name=hna[] id=hna'+no+' value="'+hna+'" />'+
                    '<input type=hidden name=isi[] id=isi'+no+' value="'+isi+'" />'+
                    '<input type=hidden name=isi_satuan[] id=isi_satuan'+no+' value="'+isi_satuan+'" /></td>'+
                    '<td align=center class=aksi><img src="img/icons/delete.png" align=left title="Klik untuk hapus" onclick="removeMe(this);" /></td>'+
               '</tr>';
    $('#penerimaan-list tbody').append(list);
    $('#harga'+no).blur(function() {
        FormNum(this);
        check_perubahan_hna(no);
    });
    $('#ed'+no).datepicker({
        changeMonth: true,
        changeYear: true,
        minDate: 0
    });
    $('#harga'+no+', #diskon_rp'+no+', #subtotal'+no+', #diskon_pr'+no+', #jumlah'+no+', #disc_pr, #disc_rp, #materai').keyup(function() {
        hitung_sub_total(no);
    });
    $('#satuan'+no).change(function() {
        var id        = $(this).val(); // id_satuan
        var id_barang = $('#id_barang'+no).val();
        var jum       = $('#jumlah'+no).val();
        $.ajax({
            url: 'models/autocomplete.php?method=get_detail_harga_barang_penerimaan&id_kemasan='+id+'&id_barang='+id_barang+'&jumlah='+jum,
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('#isi'+no).val(data.isi);
                $('#isi_satuan'+no).val(data.isi_sat);
            }
        });
    });
    $('#harga'+no+', #diskon_rp'+no+', #subtotal'+no).css('text-align','right');
    $('#diskon_pr'+no+', #jumlah'+no).css('text-align','center');
    $('#diskon_pr'+no).keyup(function() {
        var jumlah  = $('#jumlah'+no).val();
        var harga   = parseInt(currencyToNumber($('#harga'+no).val()));
        var subtotal= jumlah*harga;
        var disc_pr = ($('#diskon_pr'+no).val()/100);
        $('#diskon_rp'+no).val(numberToCurrency(parseInt(subtotal*disc_pr)));
    });
    $.getJSON('models/autocomplete.php?method=get_data_kemasan&id='+id_barang, function(data){
        $.each(data, function (index, value) {
            if (value.kemasan !== value.satuan_kecil) {
                label = value.kemasan+' isi: '+value.isi+' '+value.satuan_kecil;
            } else {
                label = value.kemasan;
            }
            $('#satuan'+no).append('<option value="'+value.id+'">'+label+'</option>');
        });
        $('#satuan'+no).val(id_satuan_beli);
    });
    $('#barang,#id_barang,#jumlah,#hna,#isi,#isi_satuan').val('');
    $('#kemasan').html(''); $('#faktur').focus().select();
}

function hitung_sub_total(i) {
    var jumlah      = $('#jumlah'+i).val();
    var harga       = parseInt(currencyToNumber($('#harga'+i).val()));
    var diskon      = parseInt(currencyToNumber($('#diskon_rp'+i).val())); // diskon rupiah yg diambil
    var subtotal    = (harga*jumlah) - diskon;
    $('#subtotal'+i).val(numberToCurrency(subtotal));
    var jml_baris   = $('.tr_rows').length;
    var total       = 0;
    
    for (j = 1; j <= jml_baris; j++) {
        var subttl      = parseInt(currencyToNumber($('#subtotal'+j).val()));
        total = total + subttl;
    }
    
    var ppn         = $('#ppn').val()/100;
    var materai     = parseInt(currencyToNumber($('#materai').val()));
    
    var ppn_total   = total+(total*ppn); // total PPN faktur setelah ditambah dengan total barang
    
    var disc_percent= $('#disc_pr').val()/100; // persentase diskon per faktur
    if (disc_percent !== 0) {
        var dp_total    = ppn_total*disc_percent;
        $('#disc_rp').val(numberToCurrency(parseInt(Math.ceil(dp_total))));
        var diskon_ttl  = parseInt(currencyToNumber($('#disc_rp').val()));
    }
    else {
        var diskon_ttl  = parseInt(currencyToNumber($('#disc_rp').val()));
    }
    var disc_ppn_ttl= ppn_total-diskon_ttl;
    var general_ttl = disc_ppn_ttl+materai;
    
    $('#total').val(numberToCurrency(parseInt(general_ttl)));
}

function form_add() {
    var str = '<div id="penerimaan"><form id="save_penerimaan">'+
                '<input type=hidden name=id_penerimaan id=id_penerimaan />'+
                '<table width=100% class=data-input><tr valign=top><td width=50%>'+
                    '<table width=100%>'+
                        '<tr><td>No. SP:</td><td><input type=text name=no_sp id=no_sp size=10 /></td></tr>'+
                        '<tr><td>Faktur:</td><td><input type=text name=faktur id=faktur size=10 /></td></tr>'+
                        '<tr><td>Tanggal:</td><td><input type=text value="<?= date("d/m/Y") ?>" name=tanggal id=tanggal size=10 /></td></tr>'+
                        '<tr><td>Supplier:</td><td><input type=text name=supplier id=supplier size=40 /><input type=hidden name=id_supplier id=id_supplier /></td></tr>'+
                        '<tr><td>Jatuh Tempo:</td><td><input type=text name=tempo id=tempo size=10 /></td></tr>'+
                        '<tr><td>PPN:</td><td><input type=text name=ppn id=ppn size=10 value="0" /> %</td></tr>'+
                    '</table>'+
                    '</td><td width=50%>'+
                    '<table width=100%>'+
                        '<tr><td>Diskon:</td><td><input type=text name=disc_pr id=disc_pr value="0" size=10 /> %, Rp. <input type=text name=disc_rp id=disc_rp onblur=FormNum(this); onfocus=javascript:this.value=currencyToNumber(this.value); size=10 value="0" /></td></tr>'+
                        '<tr><td>Materai (Rp.):</td><td><input type=text name=materai onblur=FormNum(this); id=materai size=10 value="0" /></td></tr>'+
                        '<tr><td>Total (Rp.):</td><td><input type=text name=total id=total size=10 /></td></tr>'+
                        '<tr><td width=20%>Nama Barang:</td><td width=50%><?= form_input('barang', NULL, 'id=barang size=40') ?><?= form_hidden('id_barang', NULL, 'id=id_barang') ?><?= form_hidden(NULL, NULL, 'id=hna') ?><?= form_hidden(NULL, NULL, 'id=isi') ?><?= form_hidden(NULL, NULL, 'id=isi_satuan') ?></td></tr>'+
                        '<tr><td>Kemasan:</td><td><select name=id_kemasan id=kemasan style="min-width: 86px;"><option value="">Pilih ...</option></select></td></tr>'+
                        '<tr><td>Jumlah:</td><td><?= form_input('jumlah', NULL, 'id=jumlah size=10') ?></td></tr>'+
                    '</table>'+
                '</td></tr></table>'+
                '<table width=100% cellspacing="0" class="list-data-input" id="penerimaan-list"><thead>'+
                    '<tr>'+
                        '<th width=5%>No.</th>'+
                        '<th width=25%>Nama Barang</th>'+
                        '<th width=10%>Kemasan</th>'+
                        '<th width=5%>Jumlah</th>'+
                        '<th width=10%>No. Batch</th>'+
                        '<th width=10%>ED</th>'+
                        '<th width=10%>Harga @</th>'+
                        '<th width=5%>Diskon (%)</th>'+
                        '<th width=10%>Diskon Rp.</th>'+
                        '<th width=10%>SubTotal Rp.</th>'+
                        '<th width=10%>#&nbsp;#</th>'+
                    '</tr></thead>'+
                    '<tbody></tbody>'+
                '</table>'+
              '</form></div>';
    $('body').append(str);
    $('#tempo,#tanggal').datepicker({
        changeYear: true,
        changeMonth: true
    });
    $('#jumlah').keydown(function(e) {
        if (e.keyCode === 13) {
            var id_barang       = $('#id_barang').val();
            var nama_barang     = $('#barang').val();
            var id_satuan_beli  = $('#kemasan').val();
            var jumlah          = $('#jumlah').val();
            var hna             = $('#hna').val();
            var isi             = $('#isi').val();
            var isi_satuan      = $('#isi_satuan').val();
            load_list_data(id_barang, nama_barang, id_satuan_beli, jumlah, hna, isi, isi_satuan);
        }
    });
    $('#kemasan').change(function() {
        var id        = $(this).val(); // id_satuan
        var id_barang = $('#id_barang').val();
        var jum       = $('#jumlah').val();
        $.ajax({
            url: 'models/autocomplete.php?method=get_detail_harga_barang_penerimaan&id_kemasan='+id+'&id_barang='+id_barang+'&jumlah='+jum,
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('#isi').val(data.isi);
                $('#isi_satuan').val(data.isi_sat);
            }
        });
    });
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
        $('#hna').val(data.hna);
        $('#kemasan').html('').focus();
        $.getJSON('models/autocomplete.php?method=get_kemasan_barang&id='+data.id, function(data){
            if (data === null) {
                alert('Kemasan barang tidak tersedia !');
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
    $('#penerimaan').dialog({
        title: 'Penerimaan Barang',
        autoOpen: true,
        modal: true,
        width: dWidth,
        height: dHeight,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_penerimaan').submit();
            }, 
            "Cancel": function() {    
                $(this).dialog().remove();
                $.cookie('session', 'false');
            }
        }, close: function() {
            $(this).dialog().remove();
            $.cookie('session', 'false');
        }, open: function() {
            $('#no_sp').focus();
            $.cookie('session', 'true');
        }
    });
    var lebar = $('#supplier').width();
    $('#no_sp').autocomplete("models/autocomplete.php?method=get_nomor_sp",
    {
        parse: function(data){
            var parsed = [];
            for (var i=0; i < data.length; i++) {
                parsed[i] = {
                    data: data[i],
                    value: data[i].id // nama field yang dicari
                };
            }
            return parsed;
        },
        formatItem: function(data,i,max){
            var str = '<div class=result>'+data.id+'<br/> '+data.supplier+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.id);
        $('#supplier').val(data.supplier);
        $('#id_supplier').val(data.id_supplier);
        $('#penerimaan-list tbody').html('');
        $.ajax({
            url: 'models/autocomplete.php?method=get_attr_penerimaan',
            cache: false,
            dataType: 'json',
            success: function(msg) {
                $('#faktur').val(msg.faktur);
                $('#tempo').val(msg.tempo);
                $('#ppn,#materai,#disc_rp, #disc_pr').val('0');
            }
        });
        $.getJSON('models/autocomplete.php?method=get_data_pemesanan_penerimaan&id='+data.id, function(data){
            $.each(data, function (index, value) {
                // function here
                load_list_data(value.id_barang, value.nama+' '+value.kekuatan+' '+value.satuan_kekuatan, value.id_kemasan, value.jumlah, value.hna, value.isi, value.isi_satuan);
            });
        });
    });
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
        $('#id_supplier').val(data.id);
    });
    $('#save_penerimaan').submit(function() {
        var jml_baris = $('.tr_rows').length;
        for (i = 1; i <= jml_baris; i++) {
            if ($('#satuan'+i).val() === '') {
                alert_empty('Kemasan','#satuan'+i);
                return false;
            }
            if ($('#jumlah'+i).val() === '') {
                alert_empty('Jumlah', '#jumlah'+i);
                return false;
            }
            if ($('#ed'+i).val() === '') {
                alert_empty('Expired date','#ed'+i);
                return false;
            }
            if ($('#harga'+i).val() === '') {
                alert_empty('Harga','#harga'+i);
                return false;
            }
        }
        
        $.ajax({
            url: 'models/update-transaksi.php?method=save_penerimaan',
            type: 'POST',
            dataType: 'json',
            data: $('#save_penerimaan').serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (data.action === 'add') {
                        alert_refresh('Data berhasil disimpan');
                        $('#penerimaan').dialog().remove();
                        load_data_penerimaan();
                    } else {
                        alert_edit();
                        load_data_penerimaan();
                    }
                }
            }
        });
        return false;
    });
}
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
    load_data_penerimaan();
});
function load_data_penerimaan(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/penerimaan-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_penerimaan='+id_barg,
        success: function(data) {
            $('#result-penerimaan').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_penerimaan(page, search);
}
function delete_penerimaan(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_penerimaan&id='+id,
                    cache: false,
                    success: function() {
                        load_data_penerimaan(page);
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
<h1 class="margin-t-0">Penerimaan Barang</h1>
<hr>
<button id="button">Tambah Penerimaan (F9)</button>
<button id="reset">Reset</button>
<div id="result-penerimaan">
    
</div>