<?php
include_once '../models/transaksi.php';
include_once '../models/masterdata.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
    $(function() {
        $('#search').button().click(function() {
            get_result_lap_billing();
        });
        $('#reset').button().click(function() {
            $('input[type=text],input[type=hidden]').val('');
            $('#awal,#akhir').val('<?= date("d/m/Y") ?>');
            $('input[type=radio]').removeAttr('checked');
            $('#result-info').html('');
        });
        $('#awal,#akhir').datepicker({
            changeYear: true,
            changeMonth: true
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
        });
    });
    
    function get_result_lap_billing() {
        var awal    = $('#awal').val();
        var akhir   = $('#akhir').val();
        var pasien  = $('#id_pasien').val();
        var status  = $('input:checked').val();
        var sts     = status;
        if (status === undefined) {
            sts = '';
        }
        $.ajax({
            url: 'pages/lap-billing-list.php?awal='+awal+'&akhir='+akhir+'&pasien='+pasien+'&status='+sts,
            cache: false,
            success: function(data) {
                $('#result-info').html(data);
            }
        });
    }
</script>
<h1 class="margin-t-0">Laporan Billing</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td></tr>
    <tr><td>Nama Pasien:</td><td><?= form_input('pasien', NULL, 'id=pasien size=40') ?><?= form_hidden('id_pasien', NULL, 'id=id_pasien') ?></td></tr>
    <!--<tr><td>Status:</td><td><?= form_radio('status', 'Lunas', 'lunas', 'Lunas', FALSE) ?> <?= form_radio('status', 'Belum', 'belum', 'Belum Lunas', FALSE) ?></td></tr>-->
    <tr><td></td><td><?= form_button('Cari', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?></td></tr>
</table>
</div>
<div id="result-info">
    
</div>