<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
$list_data = pemesanan_plant_load_data();

foreach ($list_data['data'] as $key => $data) {
?>
<tr class="tr_rows">
    <td align=center><?= ++$key ?></td>
    <td>&nbsp;<?= $data->nama_barang ?> <input type=hidden name=id_barang[] value="<?= $data->id_barang ?>" class=id_barang id=id_barang<?= $key ?> /></td>
    <td align=center><input type=hidden name=harga_jual[] id="harga_jual<?= $key ?>" /> <input type=hidden name=isi_satuan[] id="isi_satuan<?= $key ?>" /> <select name=kemasan[] id="kemasan<?= $key ?>"></select></td>
    <td><input type=text name=jumlah[] id=jumlah<?= $key ?> value="<?= $data->jumlah ?>" size=10 style="text-align: center;" /></td>
    <td align=right id=subtotal<?= $key ?>></td>
    <td align=center><input type=hidden id=perundangan<?= $key ?> /><img onclick=removeMe(this); title="Klik untuk hapus" src="img/icons/delete.png" class=add_kemasan align=left /></td>
</tr>
<script>
$.getJSON('models/autocomplete.php?method=get_kemasan_barang&id='+<?= $data->id_barang ?>, function(data){
    $('#kemasan'+<?= $key ?>).html('');
    $.each(data, function (index, value) {
        $('#kemasan'+<?= $key ?>).append("<option value='"+value.id+"'>"+value.nama+"</option>");
        if (value.default_kemasan === '1') { $('#kemasan'+<?= $key ?>).val(value.id); }
    });
});
$.ajax({
    url: 'models/autocomplete.php?method=get_detail_harga_barang_resep&id='+<?= $data->id_barang ?>+'&jumlah='+<?= $data->jumlah ?>,
    dataType: 'json',
    cache: false,
    success: function(data) {
        hitung_detail_total('<?= $key ?>', '<?= $data->jumlah ?>', data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual_nr), data.isi_satuan);
        $('#isi_satuan'+<?= $key ?>).val(data.isi_satuan);
    }
});
$('#kemasan'+<?= $key ?>).change(function() {
    var id  = $(this).val();
    var jum = $('#jumlah'+<?= $key ?>).val();
    $.ajax({
        url: 'models/autocomplete.php?method=get_detail_harga_barang&id='+id+'&jumlah='+jum,
        dataType: 'json',
        cache: false,
        success: function(data) {
            $('#isi_satuan'+<?= $key ?>).val(data.isi_satuan);
            hitung_detail_total(<?= $key ?>, jum, data.diskon_rupiah, data.diskon_persen, Math.ceil(data.harga_jual), data.isi_satuan);
            hitung_estimasi();
        }
    });
});

$('#jumlah'+<?= $key ?>).blur(function() {
    var jumlah      = $('#jumlah'+<?= $key ?>).val();
    var hrg_jual    = parseInt(currencyToNumber($('#harga_jual'+<?= $key ?>).val()));
    var isi_satuan  = parseInt($('#isi_satuan'+<?= $key ?>).val());
    
    var subtotal    = (hrg_jual*jumlah*isi_satuan);
    $('#subtotal'+<?= $key ?>).html(numberToCurrency(parseInt(subtotal)));
    hitung_estimasi();
});

</script>
<?php } ?>