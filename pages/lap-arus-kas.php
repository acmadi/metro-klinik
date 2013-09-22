<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
include_once '../pages/message.php';
$jenis_transaksi = get_jenis_transaksi();
$jenis_laporan   = array('Harian','Bulanan','Tahunan');

?>
<script type="text/javascript">
    function get_parameter(jenis) {
        $('#result-info').html('');
        if (jenis === 'Bulanan') {
            var str = '<option value="01">Januari</option>'+
                    '<option value="02">Februari</option>'+
                    '<option value="03">Maret</option>'+
                    '<option value="04">April</option>'+
                    '<option value="05">Mei</option>'+
                    '<option value="06">Juni</option>'+
                    '<option value="07">Juli</option>'+
                    '<option value="08">Agustus</option>'+
                    '<option value="09">September</option>'+
                    '<option value="10">Oktober</option>'+
                    '<option value="11">November</option>'+
                    '<option value="12">Desember</option>';
            $('#jenis_attr').html('<td width="10%">Bulan, Tahun:</td><td><select name="bulan" id="bulan">'+str+'</select><select name="tahun" id="tahun" style="min-width: 75px;"></select></td>');
            for (i = 2013; i <= <?= date("Y") ?>; i++) {
                //alert(i);
                var thn = '<option value="'+i+'">'+i+'</option>';
                $('#tahun').append(thn);
            }
        }
        if (jenis === 'Tahunan') {
            $('#jenis_attr').html('<td width="10%">Tahun:</td><td><select name="tahun" id="tahun" style="min-width: 75px;"></td>');
            for (i = 2013; i <= <?= date("Y") ?>; i++) {
                var thn = '<option value="'+i+'">2013</option>';
                $('#tahun').append(thn);
            }
        }
        if (jenis === 'Harian') {
            $('#jenis_attr').html('<td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td>');
            $('#awal, #akhir').datepicker({
                changeYear: true,
                changeMonth: true
            });
        }
    }
    function get_result_arus_kas() {
        var jenis_lap   = $('#jenis').val();
        var jenis_transaksi = $('#transaksi').val();
        if (jenis_lap === 'Harian') {
            var awal  = $('#awal').val();
            var akhir = $('#akhir').val();
            $.ajax({
                url: 'pages/lap-arus-kas-harian.php?awal='+awal+'&akhir='+akhir+'&jenis_transaksi='+jenis_transaksi,
                cache: false,
                success: function(data) {
                    $('#result-info').html(data);
                }
            });
        }
        if (jenis_lap === 'Bulanan') {
            var bulan   = $('#bulan').val();
            var tahun   = $('#tahun').val();
            $.ajax({
                url: 'pages/lap-arus-kas-bulanan.php?bulan='+tahun+'-'+bulan+'&jenis_transaksi='+jenis_transaksi,
                cache: false,
                success: function(data) {
                    $('#result-info').html(data);
                }
            });
        }
        if (jenis_lap === 'Tahunan') {
            var tahun   = $('#tahun').val();
            $.ajax({
                url: 'pages/lap-arus-kas-tahunan.php?tahun='+tahun+'&jenis_transaksi='+jenis_transaksi,
                cache: false,
                success: function(data) {
                    $('#result-info').html(data);
                }
            });
        }
    }
    $(function() {
        $('input[type=button]').button();
        $('#awal,#akhir').datepicker({
            changeMonth: true,
            changeYear: true
        });
        $('#jenis').change(function() {
            var jenis = $(this).val();
            get_parameter(jenis);
        });
        $('#search').click(function() {
            if ($('#jenis').val() === '') {
                alert_empty('Jenis laporan','#jenis'); return false;
            }
            get_result_arus_kas();
        });
        $('#reset').click(function() {
            $('input[type=text], select').val('');
            $('#jenis_attr').html('');
        });
    });
</script>

<h1 class="margin-t-0">Lap. Arus Kas</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Jenis Laporan:</td><td><select name="jenis" id="jenis"><option value="">Pilih ...</option><?php foreach ($jenis_laporan as $data) { ?><option value="<?= $data ?>"><?= $data ?></option> <?php } ?></select></td></tr>
    <tr id="jenis_attr"></tr>
    <tr><td>Nama Transaksi:</td><td><select name="transaksi" id="transaksi"><option value="">Pilih ...</option><?php foreach ($jenis_transaksi as $data) { ?><option value="<?= $data ?>"><?= $data ?></option> <?php } ?></select></td></tr>
    <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?> <?= form_button('Cetak', 'id=cetak') ?></td></tr>
</table>
</div>
<div id="result-info">
    
</div>