<?php
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
$akun = load_data_akun();
$bank = load_data_bank();
$cara_bayar = array('Cash','Voucher','Credit Card','Debet Card');
?>

<script type="text/javascript">
$(function() {
    load_data_billing();
    $('#search').keyup(function() {
        var value = $(this).val();
        load_data_billing('',value,'');
    });
});
function hitung_kembalian() {
    //var tTagihan    = parseInt(currencyToNumber($('#total_tagihan').val()));
    var tPembayaran = parseInt(currencyToNumber($('#pembayaran').val()));
    var nBayar      = parseInt(currencyToNumber($('#serahuang').val()));
    
    var kembalian   = nBayar-tPembayaran;
    if (kembalian < 0) {
        $('#label').html('Kekurangan:');
        $('#kembalian').html(numberToCurrency(kembalian));
    } else {
        $('#label').html('Kembalian:');
        $('#kembalian').html(numberToCurrency(kembalian));
    }
}

function hitung_diskon() {
    var total_tagihan = parseInt(currencyToNumber($('#total_tagihan').val()));
    var diskon        = $('#diskon').val()/100;
    var terdiskon     = total_tagihan-(total_tagihan*diskon);
    $('#pembayaran,#serahuang').val(numberToCurrency(Math.ceil(terdiskon)));
}

function form_add() {
var str = '<div id=form_add>'+
            '<form action="" method=post id="save_barang">'+
            '<?= form_hidden('id_pendaftaran', NULL, 'id=id_pendaftaran') ?>'+
            '<table width=100% class=data-input style="font-size: 20px;">'+
                '<tr><td>No. RM:</td><td><?= form_input('id_pasien', NULL, 'id=id_pasien size=40') ?></td></tr>'+
                '<tr><td>Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?></td></tr>'+
                '<tr><td>Total Tagihan:</td><td><?= form_input('total_tagihan', NULL, 'id=total_tagihan onblur="FormNum(this);" readonly onfocus="javascript:this.value=currencyToNumber(this.value);" size=40') ?></td></tr>'+
                '<tr><td>Diskon (%):</td><td><?= form_input('diskon', NULL, 'id=diskon onblur="hitung_diskon();" maxlength=5 size="12.5"') ?></td></tr>'+
                '<tr><td>Cara Bayar:</td><td><select name=cara_bayar id=cara_bayar><?php foreach($cara_bayar as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                '<tr><td>Bank:</td><td><select name=bank id=bank><option value="">Pilih ...</option><?php foreach($bank['data'] as $data) { ?><option value="<?= $data->id ?>"><?= $data->nama ?></option><?php } ?></select></td></tr>'+
                '<tr><td>No. Kartu:</td><td><?= form_input('nokartu', NULL, 'id=nokartu size=40') ?></td></tr>'+
                '<tr><td>Pembayaran:</td><td><?= form_input('pembayaran', NULL, 'id=pembayaran onblur="FormNum(this);" onfocus="javascript:this.value=currencyToNumber(this.value);" size=40') ?></td></tr>'+
                '<tr><td>Nominal Bayar:</td><td><?= form_input('serahuang', NULL, 'id=serahuang onblur="FormNum(this);" onfocus="javascript:this.value=currencyToNumber(this.value);" size=40') ?></td></tr>'+
                '<tr><td id=label>Kembalian:</td><td id=kembalian></td></tr>'+
//                '<tr><td width=40%>Kode Akun:</td><td><select name=akun id=akun><option value="">Pilih ...</option><?php foreach ($akun as $data) { echo '<option value="'.$data->kode.'">'.$data->kode.' '.$data->kelompok.'</option>'; } ?></select></td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('#serahuang').keyup(function() {
        hitung_kembalian();
    });
    $('#form_add').dialog({
        title: 'Pembayaran billing',
        autoOpen: true,
        width: 570,
        height: 480,
        modal: true,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Bayar": function() {
                $('#save_barang').submit();
            }
        }, close: function() {
            $(this).dialog().remove();
        }
    });
    var lebar = $('#pasien').width();
    $('#pasien, #id_pasien').autocomplete("models/autocomplete.php?method=pasien_pendaftar",
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
        $('#pasien').val(data.nama);
        $('#id_pasien').val(data.id);
        $('#id_pendaftaran').val(data.id_pendaftaran);
        $.ajax({
            url: 'models/autocomplete.php?method=get_total_tagihan&id_pendaftaran='+data.id_pendaftaran,
            cache: false,
            dataType: 'json',
            success: function(msg) {
                $('#total_tagihan, #pembayaran, #serahuang').val(numberToCurrency(Math.ceil(msg.total)));
                $('#diskon').val('0').select().focus();
                $('#kembalian').html('0');
            }
        });
    });
    
    $('#save_barang').submit(function() {
        if ($('#nama').val() === '') {
            alert('Nama billing tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        if ($('#charge').val() === '') {
            alert('Charge tidak boleh kosong !');
            $('#kemasan').focus(); return false;
        }
        var cek_id = $('#id_billing').val();
        $.ajax({
            url: 'models/update-transaksi.php?method=save_billing',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    cetak_nota(data.id);
                    alert_refresh('Pembayaran berhasil di masukkan !');
                }
            }
        });
        return false;
    });
}
$mainNav.set("home");
$('#button').button({
    icons: {
        primary: 'ui-icon-newwin'
    }
});
$('#button').click(function() {
    form_add();
});
$('#reset').button({
    icons: {
        primary: 'ui-icon-refresh'
    }
}).click(function() {
    load_data_billing();
});
$.plugin($afterSubPageShow,{ // <-- event is here
    showAlert:function(){ // <-- random function name is here (choose whatever you want)
    
    /* The code that will be executed */
    
    
    }
});
function load_data_billing(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/billing-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_billing='+id_barg,
        success: function(data) {
            $('#result-billing').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_billing(page, search);
}

function edit_billing(str) {
    var arr = str.split('#');
    form_add();
    $('#form_add').dialog({ title: 'Edit billing' });
    $('#id_billing').val(arr[0]);
    $('#nama').val(arr[1]);
    $('#jasa_dokter').val(arr[2]);
    $('#jasa_perawat').val(arr[3]);
    $('#jasa_sarana').val(arr[4]);
    $('#nominal').val(arr[5]);
}
function delete_billing(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_billing&id='+id,
                    cache: false,
                    success: function() {
                        load_data_billing(page);
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

function cetak_nota(id_billing) {
    var wWidth = $(window).width();
    var dWidth = wWidth * 0.3;
    var wHeight= $(window).height();
    var dHeight= wHeight * 1;
    var x = screen.width/2 - dWidth/2;
    var y = screen.height/2 - dHeight/2;
    window.open('pages/nota-billing.php?id_billing='+id_billing,'Billing Cetak','width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
}

</script>
<h1 class="margin-t-0">Data billing</h1>
<hr>
<button id="button">Tambah Data</button>
<button id="reset">Reset</button>
<?= form_input('search', NULL, 'id=search placeholder="Search pasien ..." class=search') ?>
<div id="result-billing">
    
</div>