<?php
set_include_path("../");
include_once("inc/essentials.php");
include_once("inc/functions.php");
include_once("models/masterdata.php");
include_once("pages/message.php");
$cara_bayar = array('Uang','Cek','Giro','Transfer');
$bank       = load_data_bank();
?>
<script type="text/javascript">
    $(function() {
        load_data_inkaso();
        $('#search').keyup(function() {
            var value = $(this).val();
            load_data_inkaso('',value,'');
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
            load_data_inkaso();
        });
    });
    
    function form_add() {
        var str = '<div id="form_inkaso">'+
                    '<form id="save_inkaso">'+
                        '<table width="100%" class=data-input>'+
                            '<tr><td>Tanggal:</td><td><?= form_input('tanggal', date("d/m/Y"), 'size=10 id=tanggal') ?></td></tr>'+
                            '<tr><td>Kode Ref:</td><td><?= form_input('noref', NULL, 'size=10 id=noref readonly') ?></td></tr>'+
                            '<tr><td>No. Faktur:</td><td><?= form_input('nofaktur', NULL, 'id=nofaktur size=40') ?><?= form_hidden('id_penerimaan', NULL, 'id=id_penerimaan') ?></td></tr>'+
                            '<tr><td>Sisa Hutang Rp.:</td><td><?= form_input('sisa', NULL, 'id=sisa disabled size=10') ?></td></tr>'+
                            '<tr><td>Cara Pembayaran:</td><td><select name=cara_bayar id=cara_bayar><?php foreach ($cara_bayar as $data) { ?><option value="<?= $data ?>"><?= $data ?></option><?php } ?></select></td></tr>'+
                            '<tr><td>Bank:</td><td><select name=bank id=bank><option value="">Pilih ...</option><?php foreach($bank['data'] as $data) { ?><option value="<?= $data->id ?>"><?= $data->nama ?></option><?php } ?></select></td></tr>'+
                            '<tr><td>No. Transaksi:</td><td><?= form_input('notransaksi', NULL, 'id=notransaksi size=40') ?></td></tr>'+
                            '<tr><td>Keterangan:</td><td><?= form_input('keterangan', NULL, 'id=keterangan size=40') ?></td></tr>'+
                            '<tr><td>Nominal Rp.:</td><td><?= form_input('nominal', NULL, 'size=10 id=nominal onkeyup="FormNum(this)"') ?></td></tr>'+
                        '</table>'+
                    '</form>'+
                  '</div>';
            $('body').append(str);
            var lebar = $('#supplier').width();
            $('#nofaktur').autocomplete("models/autocomplete.php?method=get_nofaktur",
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
                    var str = '<div class=result>'+data.faktur+'<br/> '+data.supplier+'</div>';
                    return str;
                },
                width: lebar, // panjang tampilan pencarian autocomplete yang akan muncul di bawah textbox pencarian
                dataType: 'json' // tipe data yang diterima oleh library ini disetup sebagai JSON
            }).result(
            function(event,data,formated){
                $(this).val(data.faktur);
                $('#id_penerimaan').val(data.id);
                $.ajax({
                    url: 'models/autocomplete.php?method=get_sisa_hutang_supplier',
                    data: 'id_supplier='+data.faktur,
                    dataType: 'json',
                    success: function(data) {
                        $('#sisa').val(numberToCurrency(data.sisa));
                    }
                });
            });
            $('#form_inkaso').dialog({
                title: 'Form Inkaso',
                autoOpen: true,
                modal: true,
                width: 500,
                height: 360,
                hide: 'clip',
                show: 'blind',
                buttons: {
                    "Simpan (F8)": function() {
                        $('#save_inkaso').submit();
                    },
                    "Cancel": function() {
                          $(this).dialog().remove();
                    }
                },
                close: function() {
                      $(this).dialog().remove();
                },
                open: function() {
                    $.ajax({
                        url: 'models/autocomplete.php?method=create_ref_inkaso',
                        dataType: 'json',
                        success: function(data) {
                            $('#noref').val(data.in);
                        }
                    });
                    $('#nofaktur').focus();
                }
            });
            $('#save_inkaso').submit(function() {
                if ($('#id_supplier').val() === '') {
                    alert_empty('Supplier','#supplier'); return false;
                }
                if ($('#cara_bayar').val() === '') {
                    alert_empty('Cara bayar','#cara_bayar'); return false;
                }
                if ($('#nominal').val() === '') {
                    alert_empty('Nominal','#nominal'); return false;
                }
                $.ajax({
                    url: 'models/update-transaksi.php?method=save_inkaso',
                    type: 'POST',
                    data: $('#save_inkaso').serialize(),
                    dataType: 'json',
                    cache: false,
                    success: function(msg) {
                        if (msg.status === true) {
                            alert_refresh('Data berhasil disimpan !');
                            //location.reload();
                        }
                    }
                });
                return false;
            });
            $(document).keydown(function(e) {
                if (e.keyCode === 119) {
                    $('#save_inkaso').submit();
                }
            });
            $('#tanggal, #tanggalcair').datepicker({
                changeMonth: true,
                changeYear: true
            });
            
    }
    function load_data_inkaso(page, search, id) {
        pg = page; src = search; id_inkaso = id;
        if (page === undefined) { var pg = ''; }
        if (search === undefined) { var src = ''; }
        if (id === undefined) { var id_inkaso = ''; }
        $.ajax({
            url: 'pages/inkaso-list.php',
            cache: false,
            data: 'page='+pg+'&search='+src+'&id_inkaso='+id_inkaso,
            success: function(data) {
                $('#result-inkaso').html(data);
            }
        });
    }
    function delete_inkaso(id, page) {
        $('<div id=alert>Anda yakin akan menghapus data ini?</div>').dialog({
            title: 'Konfirmasi Penghapusan',
            autoOpen: true,
            modal: true,
            buttons: {
                "OK": function() {
                    $.ajax({
                        url: 'models/update-transaksi.php?method=delete_inkaso&id='+id,
                        cache: false,
                        success: function() {
                            load_data_inkaso(page);
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
<h1>Inkaso / Pembayaran Penerimaan</h1>
<hr>
<button id="button">Tambah Inkaso (F9)</button>
<button id="reset">Reset</button>
<?= form_input('search', NULL, 'id=search placeholder="Search ..." class=search') ?>
<div id="result-inkaso">
    
</div>