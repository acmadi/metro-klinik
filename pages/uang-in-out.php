<?php
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
?>

<script type="text/javascript">
$(function() {
    load_data_in_out_uang();
    $('#search').keyup(function() {
        var value = $(this).val();
        load_data_in_out_uang('',value,'');
    });
});

function form_add() {
var str = '<div id=form_add>'+
            '<form action="" method=post id="save_barang">'+
            '<?= form_hidden('id_in_out_uang', NULL, 'id=id_in_out_uang') ?>'+
            '<table width=100% class=data-input>'+
                '<tr><td width=30%>Waktu:</td><td width=70%><?= form_input('waktu', date("d/m/Y"), 'id=waktu size=15 onBlur="javascript:this.value=this.value.toUpperCase();"') ?></td></tr>'+
                '<tr><td>Jenis:</td><td><?= form_radio('jenis', 'masuk', 'masuk', 'Masuk', TRUE) ?> <?= form_radio('jenis', 'keluar', 'keluar', 'Keluar', FALSE) ?></td></tr>'+
                '<tr><td>Nominal Rp.</td><td><?= form_input('nominal', NULL, 'size=40 id=nominal onkeyup=FormNum(this);') ?></td></tr>'+
                '<tr><td>Keterangan:</td><td><?= form_input('keterangan', NULL, 'id=keterangan size=40 onBlur="javascript:this.value=this.value.toUpperCase();"') ?><input type=hidden name="id_pabrik" /></td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('input[type=text]').blur(function() {
        this.value=this.value.toUpperCase();
    });
    $('#form_add').dialog({
        title: 'Tambah in_out_uang',
        autoOpen: true,
        width: 480,
        height: 270,
        modal: true,
        hide: 'clip',
        show: 'blind',
        buttons: {
            "Simpan": function() {
                $('#save_barang').submit();
            }, "Cancel": function() {
                $(this).dialog().remove();
            }
        }, close: function() {
            $(this).dialog().remove();
        },
        open: function() {
            $('#nominal').focus();
        }   
    });
    $('#waktu').datepicker({
        maxDate: 0,
        changeYear: true,
        changeMonth: true
    });
    
    $('#save_barang').submit(function() {
        if ($('#nominal').val() === '') {
            alert_empty('Nominal','#nominal'); return false;
        }
        $.ajax({
            url: 'models/update-transaksi.php?method=save_in_out_uang',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    alert_refresh('Data berhasil di tambahkan');
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
}).click(function() {
    form_add();
});
$('#reset').button({
    icons: {
        primary: 'ui-icon-refresh'
    }
}).click(function() {
    load_data_in_out_uang();
    $('#search').val('');
});
$.plugin($afterSubPageShow,{ // <-- event is here
    showAlert:function(){ // <-- random function name is here (choose whatever you want)
    
    /* The code that will be executed */
    
    
    }
});
function load_data_in_out_uang(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/in_out_uang-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_in_out_uang='+id_barg,
        success: function(data) {
            $('#result-in_out_uang').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_in_out_uang(page, search);
}

function edit_in_out_uang(str) {
    
    var arr = str.split('#');
    form_add();
    $('#form_add').dialog({ title: 'Edit in_out_uang' });
    $('#id_in_out_uang').val(arr[0]);
    $('#nama').val(arr[1]);
    if (arr[2] === 'P') { $('#prm').attr('checked','checked'); }
    if (arr[2] === 'L') { $('#l').attr('checked','checked'); }
    $('#tmp_lahir').val(arr[3]);
    $('#tanggal').val(arr[4]);
    $('#alamat').val(arr[5]);
    $('#kabupaten').val(arr[6]);
    $('#provinsi').val(arr[7]);
    $('#telp').val(arr[8]);
    $('#email').val(arr[9]);
    $('#jabatan').val(arr[10]);
    $('#sipa').val(arr[11]);
    
}

function delete_in_out_uang(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url: 'models/update-transaksi.php?method=delete_in_out_uang&id='+id,
                    cache: false,
                    success: function() {
                        load_data_in_out_uang(page);
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
$.plugin($afterSubPageShow,{ // <-- event is here
    showAlert:function(){ // <-- random function name is here (choose whatever you want)
        $('#search').focus();
    }
});
</script>
<h1 class="margin-t-0">Data Keluar Masuk Uang</h1>
<hr>
<button id="button">Tambah Data</button>
<button id="reset">Reset</button>
<?= form_input('search', NULL, 'id=search placeholder="Search ..." class=search') ?>
<div id="result-in_out_uang">
    
</div>