<?php
include_once '../config/database.php';
include_once '../inc/functions.php';

function print_pemesanan($id) {
    $sql = mysql_query("select p.*, k.nama as karyawan, dp.jumlah, concat_ws(' ',b.nama, b.kekuatan, st.nama) as nama_barang, 
        st.nama as kemasan, s.nama as supplier, s.alamat as alamat_supplier from pemesanan p
        join supplier s on (p.id_supplier = s.id)
        join detail_pemesanan dp on (dp.id_pemesanan = p.id)
        join kemasan km on (km.id = dp.id_kemasan)
        join barang b on (b.id = km.id_barang)
        join satuan st on (st.id = km.id_kemasan)
        left join users u on (p.id_users = u.id)
        left join karyawan k on (u.id_karyawan = k.id)
        where p.id = '$id'
    ");
    $data = array();
    while ($row = mysql_fetch_object($sql)) {
        $data[] = $row;
    }
    return $data;
}

function etiket_load_data($id_resep, $no_r) {
    $sql   = "select r.*, rr.id as id_rr, rr.id_resep, rr.id_barang, rr.id_tarif, rr.r_no, concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, 
        rr.dosis_racik, rr.jumlah_pakai, rr.jual_harga, d.nama as dokter, k.nama as apoteker, t.nama as tarif, p.nama as pasien, p.tanggal_lahir,
        rr.resep_r_jumlah, 
        rr.tebus_r_jumlah, rr.pakai, rr.aturan, rr.iter, rr.nominal from resep r
        join resep_r rr on (r.id = rr.id_resep)
        join barang b on (b.id = rr.id_barang)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join tarif t on (t.id = rr.id_tarif)
        left join pelanggan p on (p.id = r.id_pasien)
        left join dokter d on (d.id = r.id_dokter)
        left join karyawan k on (k.id = rr.id_karyawan)
        where r.id = '$id_resep' and rr.r_no = '$no_r'
    ";
    //echo "<pre>".$sql."</pre>";
    $result = mysql_query($sql);
    $rows = array();
    while ($data = mysql_fetch_object($result)) {
        $rows[] = $data;
    }
    return $rows;
}
?>
