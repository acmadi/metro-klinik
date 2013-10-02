<?php
session_start();
set_include_path("../");
include_once 'inc/essentials.php';
include_once '../inc/functions.php';
include_once '../pages/message.php';
?>
<script type="text/javascript">

$(function() {
    displayTime();
    load_data_pendaftaran();
    $('#search').click(function() {
        load_data_pendaftaran();
    });
    $('#search, #reset').button();
    $('#reset').click(function() {
        $('input[type=text], input[type=hidden]').val('');
        load_data_pendaftaran();
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
        $(this).val(data.id+' - '+data.nama);
        $('#id_pasien').val(data.id);
        $('#spesialisasi').focus().select();
    });
    $('#spesialisasi').autocomplete("models/autocomplete.php?method=spesialisasi",
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
            var str = '<div class=result>'+data.nama+'</div>';
            return str;
        },
        width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
        dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
        cacheLength: 0
    }).result(
    function(event,data,formated){
        $(this).val(data.nama);
        $('#id_spesialisasi').val(data.id);
        $.ajax({
            url: 'models/autocomplete.php?method=get_no_antri&id_spesialisasi='+data.id,
            dataType: 'json',
            cache: false,
            success: function(data) {
                $('#noantri').html(data);
                $('#simpan').focus();
            }
        });
    });
});

function displayTime() {
    //var elt = document.getElementById("waktu");  // Find element with id="clock"
    var now = new Date();                        // Get current time
    var dt  = now.toDateString()+' '+now.toLocaleTimeString();
    $('#waktu').val(dt);    // Make elt display it
    
    setTimeout(displayTime, 1000);               // Run again in 1 second
}

function paging(page, tab, search) {
    load_data_pendaftaran(page, search);
}

function load_data_pendaftaran() {
    var id_pasien   = $('#id_pasien').val();
    var pelayanan   = $('#id_spesialisasi').val();
    $.ajax({
        url: 'pages/lap-control-list.php',
        cache: false,
        data: 'id_pasien='+id_pasien+'&id_pelayanan='+pelayanan,
        success: function(data) {
            $('#result').html(data);
        }
    });
}
</script>
<h1 class="margin-t-0">Control Pasien</h1>
<div class="input-parameter">
    <table width="100%">
        <tr><td width="15%">No. RM / Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>
        <tr><td>Nama Pelayanan:</td><td><?= form_input('spesialisasi', NULL, 'id=spesialisasi size=40') ?><?= form_hidden('id_spesialisasi', NULL, 'id=id_spesialisasi') ?></td></tr>
        <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?></td></tr>
    </table>
</div>
<div id="result">
    
</div>