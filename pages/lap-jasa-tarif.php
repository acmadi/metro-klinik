<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
include_once '../pages/message.php';
?>
<script type="text/javascript">
    function get_result_arus_kas() {
        var nakes       = $('#id_nakes').val();
        var awal  = $('#awal').val();
        var akhir = $('#akhir').val();
        $.ajax({
            url: 'pages/lap-jasa-tarif-list.php?awal='+awal+'&akhir='+akhir+'&nakes='+nakes,
            cache: false,
            success: function(data) {
                $('#result-info').html(data);
            }
        });
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
            $('#awal, #akhir').val('<?= date("d/m/Y") ?>');
            $('#result-info').html('');
        });
        $('#cetak').click(function() {
            var wWidth = $(window).width();
            var dWidth = wWidth * 1;
            var wHeight= $(window).height();
            var dHeight= wHeight * 1;
            var x = screen.width/2 - dWidth/2;
            var y = screen.height/2 - dHeight/2;
                var awal  = $('#awal').val();
                var akhir = $('#akhir').val();
                var nakes = $('#id_nakes').val();
                window.open('pages/lap-jasa-tarif-print.php?awal='+awal+'&akhir='+akhir+'&nakes='+nakes, 'Jasa Nakes', 'width='+dWidth+', height='+dHeight+', left='+x+',top='+y);
            
        });
        var lebar = $('#nakes').width();
        $('#nakes').autocomplete("models/autocomplete.php?method=dokter",
        {
            parse: function(data){
                var parsed = [];
                for (var i=0; i < data.length; i++) {
                    parsed[i] = {
                        data: data[i],
                        value: data[i].nama // nama field yang dicari
                    };
                }
                $('#id_perawat').val('');
                return parsed;
            },
            formatItem: function(data,i,max){
                var str = '<div class=result>'+data.nama+'<br/> '+data.no_str+'</div>';
                return str;
            },
            width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
            dataType: 'json', // tipe data yang diterima oleh library ini disetup sebagai JSON
            cacheLength: 0,
            max: 100
        }).result(
        function(event,data,formated){
            $(this).val(data.nama);
            $('#id_nakes').val(data.id);
        });
    });
</script>

<h1 class="margin-t-0">Lap. Jasa Layanan</h1>
<div class="input-parameter">
<table width="100%">
    <tr><td width="10%">Range Tanggal:</td><td><?= form_input('awal', date("d/m/Y"), 'id=awal size=10') ?> s . d <?= form_input('akhir', date("d/m/Y"), 'id=akhir size=10') ?></td></tr>
    <tr><td>Nama Nakes:</td><td><?= form_input('nakes', NULL, 'id=nakes size=40') ?> <?= form_hidden('id_nakes', NULL, 'id=id_nakes') ?></td></tr>
    <tr><td></td><td><?= form_button('Tampilkan', 'id=search') ?> <?= form_button('Reset', 'id=reset') ?> <?= form_button('Cetak', 'id=cetak') ?></td></tr>
</table>
</div>
<div id="result-info">
    
</div>