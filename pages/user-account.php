<?php
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
?>

<script type="text/javascript">
$(function() {
    load_data_user_account();
    $('#search').keyup(function() {
        var value = $(this).val();
        load_data_user_account('',value,'');
    });
});
function form_add() {
var str = '<div id=form_add>'+
            '<form action="" method=post id="save_barang">'+
            '<?= form_hidden('id_user_account', NULL, 'id=id_user_account') ?>'+
            '<table width=100% class=data-input>'+
                '<tr><td width=40%>Karyawan:</td><td><?= form_input('karyawan', NULL, 'id=karyawan size=40') ?><?= form_hidden('id_karyawan', NULL, 'id=id_karyawan') ?></td></tr>'+
                '<tr><td width=40%>Username:</td><td><?= form_input('username', NULL, 'id=username size=40') ?></td></tr>'+
                '<tr><td width=40%>Password:</td><td><?= form_password('password', NULL, 'id=password size=40') ?></td></tr>'+
                '<tr><td width=40%>Level:</td><td><select name="level" id=level><option value="Staff">Staff</option><option value="Admin">Admin</option><option value="Dokter">Dokter</option></select></td></tr>'+
            '</table>'+
            '</form>'+
            '</div>';
    $('body').append(str);
    $('#form_add').dialog({
        title: 'Tambah user account',
        autoOpen: true,
        width: 480,
        height: 220,
        modal: false,
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
        }
    });
    var lebar = $('#user_account').width();
    $('#user_account').dblclick(function() {
        $('<div title="Data user_account" id="user_account-data"></div>').dialog({
            autoOpen: true,
            modal: true,
            width: 500,
            height: 370,
            buttons: {
                
            }
        });
    });
    $('#karyawan').autocomplete("models/autocomplete.php?method=karyawan",
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
            var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0,
        max: 100
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_karyawan').val(data.id);
    });
    $('#save_barang').submit(function() {
        if ($('#nama').val() === '') {
            alert('Nama barang tidak boleh kosong !');
            $('#nama').focus(); return false;
        }
        var cek_id = $('#id_user_account').val();
        $.ajax({
            url: 'models/update-masterdata.php?method=save_user_account',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            cache: false,
            success: function(data) {
                if (data.status === true) {
                    if (cek_id === '') {
                        alert_tambah('#nama');
                        $('input').val('');
                        load_data_user_account('1','',data.id);
                    } else {
                        alert_edit();
                        $('#form_add').dialog().remove();
                        load_data_user_account($('.noblock').html());
                    }
                    
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
    load_data_user_account();
    $('#search').val('');
});
$.plugin($afterSubPageShow,{ // <-- event is here
    showAlert:function(){ // <-- random function name is here (choose whatever you want)
    /* The code that will be executed */
    }
});
function load_data_user_account(page, search, id) {
    pg = page; src = search; id_barg = id;
    if (page === undefined) { var pg = ''; }
    if (search === undefined) { var src = ''; }
    if (id === undefined) { var id_barg = ''; }
    $.ajax({
        url: 'pages/user-account-list.php',
        cache: false,
        data: 'page='+pg+'&search='+src+'&id_user_account='+id_barg,
        success: function(data) {
            $('#result-useraccount').html(data);
        }
    });
}

function paging(page, tab, search) {
    load_data_user_account(page, search);
}

function edit_user_account(str) {
    var arr = str.split('#');
    form_add();
    $('#form_add').dialog({ title: 'Edit user_account' });
    $('#id_user_account').val(arr[0]);
    $('#id_karyawan').val(arr[1]);
    $('#karyawan').val(arr[2]);
    $('#username').val(arr[3]);
    $('#password').val(arr[4]);
    $('#level').val(arr[5]);
}

function delete_user_account(id, page) {
    $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
        title: 'Konfirmasi Penghapusan',
        autoOpen: true,
        modal: true,
        buttons: {
            "OK": function() {
                
                $.ajax({
                    url: 'models/update-masterdata.php?method=delete_user_account&id='+id,
                    cache: false,
                    success: function() {
                        load_data_user_account(page);
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
<h1 class="margin-t-0">User Account</h1>
<hr>
<button id="button">Tambah (F9)</button>
<button id="reset">Reset</button>
<div id="result-useraccount">
    
</div>