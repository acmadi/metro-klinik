<?php

include_once '../config/database.php';

function get_jenis_transaksi() {
    return array('Total Penjualan','Penjualan Resep','Penjualan Non Resep','Penjualan Jasa','Inkaso', 'Lain-lain');
}

function header_surat() {
    $sql = "select * from klinik";
    $result = mysql_query($sql);
    $data   = mysql_fetch_object($result);
    echo "<h1 class=kop-surat>".$data->nama."<br/>".$data->alamat."<br/>".$data->telp." ".$data->email."</h1>";
}

function get_bottom_label() {
    $sql = "select * from klinik";
    $result = mysql_query($sql);
    $data   = mysql_fetch_object($result);
    return $data;
}

function get_apa_from_karyawan() {
    $sql = "select * from karyawan where jabatan = 'APA'";
    $result = mysql_query($sql);
    $data   = mysql_fetch_object($result);
    return $data;
}

function pemesanan_load_data($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.="and p.id = '".$param['id']."' ";
    }
    if ($param['id_supplier'] !== '') {
        $q.=" and s.id = '".$param['id_supplier']."'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select p.*, k.nama as karyawan, dp.jumlah, concat_ws(' ',b.nama, b.kekuatan, st.nama) as nama_barang, st.nama as kemasan, s.nama as supplier from pemesanan p
        join supplier s on (p.id_supplier = s.id)
        join detail_pemesanan dp on (dp.id_pemesanan = p.id)
        join kemasan km on (km.id = dp.id_kemasan)
        join barang b on (b.id = km.id_barang)
        join satuan st on (st.id = km.id_kemasan)
        left join users u on (p.id_users = u.id)
        left join karyawan k on (u.id_karyawan = k.id)
        where p.id is not NULL $q order by p.id desc";
    //echo $sql;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function penerimaan_load_data($param) {
    $q = NULL; $limit = NULL;
    if (isset($param['id']) and $param['id'] !== '') {
        $q.="and p.id = '".$param['id']."' ";
    }
    if (isset($param['id_supplier']) and $param['id_supplier'] !== '') {
        $q.=" and s.id = '".$param['id_supplier']."'";
    }
    if (isset($param['faktur']) and $param['faktur'] !== '') {
        $q.=" and p.faktur = '".$param['faktur']."'";
    }
    if (isset($param['awal'])) {
        $q.=" and p.tanggal between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (isset($param['start'])) {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    
    $sql = "select p.*, k.nama as karyawan, s.nama as supplier, concat_ws(' ',b.nama, b.kekuatan, st.nama) as nama_barang, 
        dp.jumlah, dp.expired, dp.nobatch, dp.harga, dp.disc_pr, dp.disc_rp
        from penerimaan p
        left join pemesanan ps on (p.id_pemesanan = ps.id)
        join detail_penerimaan dp on (dp.id_penerimaan = p.id)
        join kemasan km on (dp.id_kemasan = km.id)
        join barang b on (km.id_barang = b.id)
        join satuan st on (b.satuan_kekuatan = st.id)
        join supplier s on (ps.id_supplier = s.id)
        left join users u on (p.id_users = u.id)
        left join karyawan k on (u.id_karyawan = k.id)
        where p.id is not NULL $q";
    //echo $sql.$limit;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function hutang_load_data($param) {
    $q = NULL; $limit = NULL; $having = NULL;
    if ($param['id_supplier'] !== '') {
        $q.=" and s.id = '".$param['id_supplier']."'";
    }
    if (isset($param['awal_faktur']) and $param['awal_faktur'] !== '') {
        $q.=" and p.tanggal between '".$param['awal_faktur']."' and '".$param['akhir_faktur']."'";
    }
    if (isset($param['awal']) and $param['awal'] !== '') {
        $q.=" and p.jatuh_tempo between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if ($param['status'] !== 'undefined') {
        if ($param['status'] === 'Lunas') {
            $having.=" having terbayar = p.total ";
        }
        if ($param['status'] === 'Hutang') {
            $having.=" having terbayar < p.total ";
        }
    }
    if (isset($param['start'])) {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    
    $sql = "select p.*, k.nama as karyawan, s.nama as supplier, IFNULL(sum(i.nominal),'0') as terbayar from penerimaan p
        left join pemesanan ps on (p.id_pemesanan = ps.id)
        left join inkaso i on (p.id = i.id_penerimaan)
        join supplier s on (ps.id_supplier = s.id)
        left join users u on (p.id_users = u.id)
        left join karyawan k on (u.id_karyawan = k.id)
        where p.id is not NULL $q group by p.id $having";
    //echo $sql.$limit;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function retur_penerimaan_load_data($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.="and rp.id = '".$param['id']."' ";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select rp.tanggal, rp.id_supplier, st.nama as kemasan, b.nama as barang, b.kekuatan, 
        stn.nama as satuan, dp.*, s.nama as supplier from retur_penerimaan rp
        join detail_retur_penerimaan dp on (rp.id = dp.id_retur_penerimaan)
        join supplier s on (rp.id_supplier = s.id)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (b.id = k.id_barang)
        join satuan st on (st.id = k.id_kemasan)
        left join satuan stn on (stn.id = b.satuan_kekuatan)
        where rp.id is not NULL $q order by rp.id";
    //echo $sql;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function load_data_stok_opname($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.="and s.id = '".$param['id']."' ";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select s.*, b.kekuatan, b.nama, st.nama as satuan_kekuatan, sum(s.masuk) as masuk, sum(s.keluar) as keluar, (sum(s.masuk)-sum(s.keluar)) as sisa from stok s 
        join barang b on (s.id_barang = b.id)
        left join satuan st on (b.satuan_kekuatan = st.id)
        where s.id is not NULL $q group by s.id_barang";
    //echo $sql;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function load_data_expired_date($param) {
    $sekarang = date("Y-m-d");
    $var1     = mktime(0, 0, 0, date("m")+3, date("d"), date("Y"));
    $tiga_bln = date("Y-m-d", $var1);
    $var2     = mktime(0, 0, 0, date("m")+6, date("d"), date("Y"));
    $enam_bln = date("Y-m-d", $var2);
    
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select s.*, b.kekuatan, b.nama, st.nama as satuan_kekuatan, sum(s.masuk) as masuk, sum(s.keluar) as keluar, 
        (sum(s.masuk)-sum(s.keluar)) as sisa 
        from stok s 
        join barang b on (s.id_barang = b.id)
        left join satuan st on (b.satuan_kekuatan = st.id)
        where (s.ed between '$sekarang' and '$tiga_bln') or (s.ed between '$sekarang' and '$enam_bln') group by s.id_barang, s.ed order by b.nama";
    //echo $sql;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function load_data_arus_stok($param) {
    $q = NULL; $limit = NULL;
    if ($param['id'] !== '') {
        $q.=" and s.id_barang = '".$param['id']."' ";
    }
    if ($param['awal'] !== '' and $param['akhir'] !== '') {
        $q.=" and date(s.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (isset($param['perundangan']) and $param['perundangan'] != '') {
        $q.=" and b.perundangan = '".$param['perundangan']."'";
    }
    if ($param['limit'] !== '') {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    $sql = "select s.*, b.kekuatan, b.nama, st.nama as satuan_kekuatan from stok s 
        join barang b on (s.id_barang = b.id)
        left join satuan st on (b.satuan_kekuatan = st.id)
        where s.id is not NULL $q order by s.waktu asc";
    //echo $sql.$limit;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function load_data_resep($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.="and r.id = '".$param['id']."' ";
    }
    if (isset($param['awal']) and $param['awal'] !== '') {
        $q.=" and date(r.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (!isset($param['awal'])) {
        $q.=" and date(r.waktu) between '".date("Y-m-d")."' and '".date("Y-m-d")."'";
    }
    if (isset($param['dokter']) and $param['dokter'] !== '') {
        $q.=" and r.id_dokter = '".$param['dokter']."'";
    }
    if (isset($param['pasien']) and $param['pasien'] !== '') {
        $q.=" and r.id_pasien = '".$param['pasien']."'";
    }
    $limit = NULL;
    if (isset($param['start'])) {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    $sql   = "select r.*, rr.id as id_rr, rr.id_resep, rr.id_barang, rr.id_tarif, rr.r_no, concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, 
        rr.dosis_racik, rr.jumlah_pakai, rr.jual_harga, d.nama as dokter, k.nama as apoteker, t.nama as tarif, p.nama as pasien, p.tanggal_lahir, b.kekuatan,
        rr.resep_r_jumlah, sd.nama as sediaan, concat_ws(' ',rr.pakai, rr.aturan) as pakai_aturan, d.no_str as sip_no, d.alamat as alamat_dokter,
        rr.tebus_r_jumlah, rr.pakai, rr.aturan, rr.iter, rr.nominal, pj.total from resep r
        join resep_r rr on (r.id = rr.id_resep)
        join penjualan pj on (r.id = pj.id_resep)
        join barang b on (b.id = rr.id_barang)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join tarif t on (t.id = rr.id_tarif)
        left join pelanggan p on (p.id = r.id_pasien)
        left join dokter d on (d.id = r.id_dokter)
        left join sediaan sd on (sd.id = b.id_sediaan)
        left join karyawan k on (k.id = rr.id_karyawan)
        where r.id is not NULL $q order by r.id, rr.r_no
    ";
    //echo "<pre>".$sql."</pre>";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function check_penjualan_availability($id_resep) {
    $sql = "select p.*, sum(db.bayar) as terbayar, (p.total-sum(db.bayar)) as sisa from penjualan p 
        join detail_bayar_penjualan db on (p.id = db.id_penjualan) 
        where p.id_resep = '$id_resep' group by db.id_penjualan";
    $query = mysql_query($sql);
    $rows  = mysql_fetch_object($query);
    return $rows;
}

function penjualan_nr_load_data($param) {
    $q = NULL; $limit = NULL;
    if ($param['id'] !== '') {
        $q.="and p.id = '".$param['id']."' ";
    } else {
        if (isset($param['awal']) and $param['awal'] !== '' and $param['akhir'] !== '') {
            $q.="and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
            $q.=" group by p.id";
        } else {
            $q.="and date(p.waktu) between '".date("Y-m-d")."' and '".date("Y-m-d")."'";
            $limit = " limit ".$param['start'].", ".$param['limit']."";
        }
    }
    $sql = "select p.*, date(p.waktu) as tanggal, pl.nama as customer, a.nama as asuransi,
        (select sum(bayar) from detail_bayar_penjualan where id_penjualan = p.id) as terbayar,
        concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, dp.qty, dp.harga_jual, (dp.harga_jual*dp.qty) as subtotal
        from penjualan p
        join detail_penjualan dp on (p.id = dp.id_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        left join asuransi a on (pl.id_asuransi = a.id) 
        where p.id_resep is NULL $q order by p.waktu desc";
    //echo $sql.$limit;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function penjualan_load_data($param) {
    $q = NULL; $limit = NULL;
    if ($param['id'] !== '') {
        $q.=" and p.id = '".$param['id']."' ";
    } 
    if (isset($param['pasien']) and $param['pasien'] !== '') {
        $q.=" and p.id_pelanggan = '".$param['pasien']."'";
    }
    if (isset($param['dokter']) and $param['dokter'] !== '') {
        $q.=" and r.id_dokter = '".$param['dokter']."'";
    }
    if (isset($param['laporan'])) {
        $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
        $q.=" group by p.id";
    } else {
        $q.=" and date(p.waktu) between '".date("Y-m-d")."' and '".date("Y-m-d")."'";
        //$limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    
    $sql = "select p.*, date(p.waktu) as tanggal, pl.nama as customer, pl.id as id_customer, a.nama as asuransi, d.nama as dokter, 
        (select sum(bayar) from detail_bayar_penjualan where id_penjualan = p.id) as terbayar,
        concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, dp.qty, dp.harga_jual, (dp.harga_jual*dp.qty) as subtotal
        from penjualan p
        join detail_penjualan dp on (p.id = dp.id_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        left join asuransi a on (pl.id_asuransi = a.id) 
        join resep r on (p.id_resep = r.id)
        left join dokter d on (r.id_dokter = d.id)
        where p.id_resep is not NULL $q order by p.waktu desc";
    //echo "<pre>".$sql.$limit."</pre>";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function statistik_penjualan_load_data($param) {
    $q = NULL; $limit = NULL;
    if (isset($param['awal']) and $param['awal'] !== '') {
        $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (isset($param['perundangan']) and $param['perundangan'] !== '') {
        $q.=" and b.perundangan = '".$param['perundangan']."'"; 
    }
    
    $sql = "select concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, b.hna+(b.hna*(b.margin_non_resep/100)) as harga, count(*) as jumlah, 
        date(p.waktu) as tanggal, pl.nama as customer, pl.id as id_customer, a.nama as asuransi
        from penjualan p
        join detail_penjualan dp on (p.id = dp.id_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        left join asuransi a on (pl.id_asuransi = a.id) 
        where b.id is not NULL $q group by b.id order by jumlah desc ";
    
    $sql_jml = "select concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, date(p.waktu) as tanggal, 
        pl.nama as customer, pl.id as id_customer, a.nama as asuransi
        from penjualan p
        join detail_penjualan dp on (p.id = dp.id_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        left join asuransi a on (pl.id_asuransi = a.id)
        where b.id is not NULL $q";
    //echo "<pre>".$sql.$limit."</pre>";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql_jml));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function analisis_abc__load_data($param) {
    $q = NULL; $limit = NULL;
    if (isset($param['awal']) and $param['awal'] !== '') {
        $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (isset($param['perundangan']) and $param['perundangan'] !== '') {
        $q.=" and b.perundangan = '".$param['perundangan']."'"; 
    }
    
    $sql = "select concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, b.hna+(b.hna*(b.margin_non_resep/100)) as harga, 
        count(*)*(b.hna+(b.hna*(b.margin_non_resep/100))) as total_harga, count(*) as jumlah, 
        date(p.waktu) as tanggal, pl.nama as customer, pl.id as id_customer, a.nama as asuransi
        from penjualan p
        join detail_penjualan dp on (p.id = dp.id_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        left join asuransi a on (pl.id_asuransi = a.id) 
        where b.id is not NULL $q group by b.id order by total_harga desc ";
    
    $sql_jml = "select concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang, date(p.waktu) as tanggal, 
        pl.nama as customer, pl.id as id_customer, a.nama as asuransi
        from penjualan p
        join detail_penjualan dp on (p.id = dp.id_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        left join satuan st on (k.id_kemasan = st.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        left join asuransi a on (pl.id_asuransi = a.id)
        where b.id is not NULL $q";
    //echo "<pre>".$sql.$limit."</pre>";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql_jml));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function penjualan_load_data_barang($id) {
    $sql = "select b.*, p.bayar, s.nama as satuan, dp.qty, dp.harga_jual, dp.disc_pr, dp.disc_rp,
        p.waktu, p.total, p.tuslah, p.embalage, p.ppn, p.diskon_persen, p.diskon_rupiah, p.id_resep, pl.nama as pelanggan,
        (dp.qty*dp.harga_jual) as subtotal from detail_penjualan dp
        join penjualan p on (dp.id_penjualan = p.id)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        join kemasan k on (dp.id_kemasan = k.id)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        where dp.id_penjualan = '$id'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function penjualan_load_data_barang_nota($id) {
    $sql = "select id, nama_barang as nama, jumlah as qty, harga_jual, disc_rp, disc_pr from detail_penjualan_nota where id_penjualan = '$id'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function pemeriksaan_load_data($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.=" and p.id = '".$param['id']."'";
    }
    if ($param['search'] !== '' and $param['search'] !== 'undefined') {
        $q.=" and (pl.nama like ('%".$param['search']."%') or d.nama like ('%".$param['search']."%'))";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select p.*, pl.nama as pasien, d.nama as dokter
        from pemeriksaan p
        join pendaftaran pd on (p.id_pendaftaran = pd.id)
        join pelanggan pl on (pd.id_pelanggan = pl.id)
        join dokter d on (p.id_dokter = d.id)
        where p.id is not NULL $q order by p.id_auto desc";
    //echo "<pre>".$sql."</pre>";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function inkaso_load_data($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.=" and i.id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q.=" and i.no_ref like ('%".$param['search']."%') or s.nama like ('%".$param['search']."%') or b.nama like ('%".$param['search']."%')";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select i.*, p.faktur, s.nama supplier, b.nama as bank from inkaso i
        join penerimaan p on (i.id_penerimaan = p.id)
        join supplier s on (p.id_supplier = s.id)
        left join bank b on (i.id_bank = b.id) where i.id is not NULL $q";
    
    //echo $sql.$limit;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}
function load_data_defecta($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.=" and s.id = '".$param['id']."' ";
    }
    if ($param['search'] !== '') {
        $q.=" and b.nama like ('%".$param['search']."%')";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select s.*, b.kekuatan, b.id as id_barang, b.nama, b.stok_minimal, st.nama as satuan_kekuatan, sum(s.masuk) as masuk, 
        sum(s.keluar) as keluar, (sum(s.masuk)-sum(s.keluar)) as sisa 
        from stok s 
        join barang b on (s.id_barang = b.id) 
        left join satuan st on (b.satuan_kekuatan = st.id) 
        where b.id not in (select id_barang from defecta where status = '0') $q
        group by s.id_barang  
        having sisa <= b.stok_minimal order by b.nama";
    
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function get_distributor_by_barang($id_barang) {
    $sql = mysql_query("select s.nama from supplier s
        join penerimaan p on (s.id = p.id_supplier)
        join detail_penerimaan dp on (p.id = dp.id_penerimaan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (k.id_barang = b.id)
        inner join (
            select id_kemasan, max(id) as id_max from detail_penerimaan group by id_kemasan
        ) dm on (dp.id_kemasan = dm.id_kemasan and dp.id = dm.id_max)
        where b.id = '$id_barang'
    ");
    $row = mysql_fetch_object($sql);
    return $row;
}

function pemesanan_plant_load_data($param = NULL) {
    $limit = NULL;
    if (isset($param['list'])) {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    $sql = "select d.*, concat_ws(' ',b.nama, b.kekuatan, s.nama) as nama_barang from defecta d
        join barang b on (d.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id) where status = '0' order by b.nama";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function load_data_pendaftaran($param) {
    $q = NULL; $limit = NULL;
    if (isset($param['search']) and $param['search'] !== '') {
        $q.=" and (pl.id like ('%".$param['search']."%') or pl.nama like ('%".$param['search']."%') or s.nama like ('%".$param['search']."%') or d.nama like ('%".$param['search']."%'))";
    }
    if (isset($param['pasien']) and $param['pasien'] !== '') {
        $q.=" and pl.id = '".$param['pasien']."'";
    }
    if (isset($param['pelayanan']) and $param['pelayanan'] !== '') {
        $q.=" and s.id = '".$param['pelayanan']."'";
    }
    if ($param['limit'] !== '') {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    $sql = "select p.*, pl.nama, s.nama as spesialisasi, d.nama as dokter, pm.id as kode_periksa, pm.id_auto as id_pemeriksaan, pm.tanggal as waktu_pelayanan,
        pm.no_antri, pm.id_spesialisasi, pm.tanggal
        from pendaftaran p
        join pelanggan pl on (p.id_pelanggan = pl.id)
        left join pemeriksaan pm on (p.id = pm.id_pendaftaran)
        join spesialisasi s on (pm.id_spesialisasi = s.id)
        left join dokter d on (pm.id_dokter = d.id)
        where date(p.waktu) = '".date("Y-m-d")."' $q order by pm.id_auto";
    //echo $sql;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function cetak_no_antri($id_pemeriksaan) {
    $sql = "select p.*, pl.nama, s.nama as layanan, pm.no_antri from pendaftaran p
        join pelanggan pl on (p.id_pelanggan = pl.id)
        join pemeriksaan pm on (pm.id_pendaftaran = p.id)
        join spesialisasi s on (pm.id_spesialisasi = s.id)
        where pm.id_auto = '$id_pemeriksaan'";
    $result = mysql_query($sql);
    $rows   = mysql_fetch_object($result);
    return $rows;
}

function arus_kas_harian_load_data($param) {
    $q = NULL;
    if (isset($param['awal'])) {
        $q.=" and date(waktu) between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if ($param['transaksi'] !== '') {
        if ($param['transaksi'] === 'Total Penjualan') {
            $q.=" and transaksi like '%Penjualan%'";
        }
        else {
            $q.=" and transaksi = '".$param['transaksi']."'";
        }
    }
    $sql = "select * from arus_kas where id is not NULL $q";
    //echo $sql;
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function pp_kas_load_data($param) {
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select a.*, k.nama as karyawan from arus_kas a
            join users u on (a.id_users = u.id)
            join karyawan k on (u.id_karyawan = k.id)
            where a.transaksi = 'Lain-lain' order by waktu desc";
    //echo $sql;
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function arus_kas_bulanan_load_data($param) {
    $q = NULL;
    if (isset($param['bulan'])) {
        $q.=" and date(waktu) like ('%".$param['bulan']."%')";
    }
    if ($param['transaksi'] !== '') {
        if ($param['transaksi'] === 'Total Penjualan') {
            $q.=" and transaksi like '%Penjualan%'";
        }
        else {
            $q.=" and transaksi = '".$param['transaksi']."'";
        }
    }
    $sql = "select waktu, transaksi, sum(masuk) as masuk, sum(keluar) as keluar from arus_kas where id is not NULL $q group by transaksi";
    //echo $sql;
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function arus_kas_tahunan_load_data($param) {
    $q = NULL;
    if (isset($param['tahun'])) {
        $q.=" and year(waktu) = '".$param['tahun']."'";
    }
    if ($param['transaksi'] !== '') {
        if ($param['transaksi'] === 'Total Penjualan') {
            $q.=" and transaksi like '%Penjualan%'";
        }
        else {
            $q.=" and transaksi = '".$param['transaksi']."'";
        }
    }
    $sql = "select waktu, transaksi, sum(masuk) as masuk, sum(keluar) as keluar from arus_kas where id is not NULL $q group by transaksi";
    //echo $sql;
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function retur_penjualan_load_data($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.="and rp.id = '".$param['id']."' ";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select rp.id_penjualan, rp.total, rp.waktu, st.nama as kemasan, b.nama as barang, b.kekuatan, 
        stn.nama as satuan, dp.* from retur_penjualan rp
        join detail_retur_penjualan dp on (rp.id = dp.id_retur_penjualan)
        join kemasan k on (k.id = dp.id_kemasan)
        join barang b on (b.id = k.id_barang)
        left join satuan st on (st.id = k.id_kemasan)
        left join satuan stn on (stn.id = b.satuan_kekuatan)
        where rp.id is not NULL $q order by rp.id";
    //echo "<pre>".$sql."</pre>";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function diagnosis_load_by_pendaftaran($id_periksa) {
    $sql = "select p.topik from diagnosis d join penyakit p on (d.id_penyakit = p.id) where d.id_pemeriksaan = '$id_periksa'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function tindakan_load_by_pendaftaran($id_periksa) {
    $sql = "select tr.nama, tr.nominal from tindakan t join tarif tr on (t.id_tarif = tr.id) where id_pemeriksaan = '$id_periksa'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function rek_tindakan_load_by_pendaftaran($id_periksa) {
    $sql = "select tr.nama, tr.nominal from rek_tindakan t join tarif tr on (t.id_tarif = tr.id) where id_pemeriksaan = '$id_periksa'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function load_data_billing($param) {
    $q = NULL;
    if ($param['id'] !== '') {
        $q.="";
    }
    if ($param['search'] !== '' and $param['search'] !== 'undefined') {
        $q.=" and pl.nama like ('%".$param['search']."%')";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select pb.*, pl.nama as pasien, b.nama as nama_bank
        from pembayaran_billing pb
        join pendaftaran pdf on (pb.id_pendaftaran = pdf.id)
        join pelanggan pl on (pl.id = pdf.id_pelanggan)
        left join bank b on (b.id = pb.id_bank)
        where pb.id is not NULL $q";
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
    $result['data'] = $data;
    $result['total']= $total;
    return $result;
}

function nota_billing_load_data($id_billing) {
    $sqw = "select pb.*, pl.id as no_rm, pl.nama as pelanggan from pembayaran_billing pb
        join pendaftaran pdf on (pb.id_pendaftaran = pdf.id)
        join pelanggan pl on (pl.id = pdf.id_pelanggan)
        where pb.id = '$id_billing'";
    //echo $sqw;
    $qwe = array();
    $ewq = mysql_query($sqw);
    while ($asd = mysql_fetch_object($ewq)) {
        $qwe[] = $asd;
    }
    
    $return['atribute'] = $qwe;
    
    $sql = "select dp.nama_barang, dp.jumlah as qty, dp.harga_jual, 
        p.waktu, p.total, p.tuslah, p.embalage, p.ppn, p.diskon_persen, p.diskon_rupiah, p.id_resep, pl.nama as pelanggan,
        (dp.jumlah*dp.harga_jual) as subtotal from detail_penjualan_nota dp
        join penjualan p on (dp.id_penjualan = p.id)
        join resep r on (r.id = p.id_resep)
        join pendaftaran pdf on (pdf.id = r.id_pendaftaran)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        where r.id_pendaftaran = (select id_pendaftaran from pembayaran_billing where id = '$id_billing')";
    //echo "<pre>".$sql."</pre>";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    
    $sqk = "select count(tr.id) as frek, t.*, tr.nama from tindakan t
        join tarif tr on (t.id_tarif = tr.id)
        join pemeriksaan pm on (t.id_pemeriksaan = pm.id_auto)
        join pendaftaran pdf on (pm.id_pendaftaran = pdf.id)
        where pm.id_pendaftaran = (select id_pendaftaran from pembayaran_billing where id = '$id_billing')
        group by t.id_tarif";
    //echo $sqk;
    $result = mysql_query($sqk);
    $rows = array();
    while ($metallica = mysql_fetch_object($result)) {
        $rows[] = $metallica;
    }
    
    $return['list_barang'] = $data;
    $return['list_jasa'] = $rows;
    return $return;
}

function billing_load_data($param) {
    $q = NULL;
    if (isset($param['awal'])) {
        $q.=" and date(p.waktu) between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (isset($param['pasien']) and $param['pasien'] !== '') {
        $q.=" and p.id_pelanggan = '".$param['pasien']."'";
    }
    if (isset($param['status']) and $param['status'] !== '') {
        if ($param['status'] === 'Lunas') {
            //$q.=" and p.is_bayar = '1'";
        }
    }
    $sql = "select pb.*, date(p.waktu) as tanggal, pl.nama as pelanggan, pb.bayar as terbayar, b.nama as nama_bank
        from pendaftaran p
        join pelanggan pl on (p.id_pelanggan = pl.id)
        left join pembayaran_billing pb on (p.id = pb.id_pendaftaran)
        left join bank b on (b.id = pb.id_bank)
        where p.id is not NULL $q";
    //echo "<pre>".$sql."</pre>";
    $result = mysql_query($sql);
    $rows = array();
    while ($metallica = mysql_fetch_object($result)) {
        $rows[] = $metallica;
    }
    
    $return['data'] = $rows;
    return $return;
    
}

function billing_get_total_barang($tanggal, $id_pelanggan) {
    $sql = "select b.*, s.nama as satuan, dp.qty, dp.harga_jual, 
        p.waktu, p.total, p.tuslah, p.embalage, p.ppn, p.diskon_persen, p.diskon_rupiah, p.id_resep, pl.nama as pelanggan,
        sum(dp.qty*dp.harga_jual) as total_barang from detail_penjualan dp
        join penjualan p on (dp.id_penjualan = p.id)
        join resep r on (r.id = p.id_resep)
        join pendaftaran pdf on (pdf.id = r.id_pendaftaran)
        left join pelanggan pl on (p.id_pelanggan = pl.id)
        join kemasan k on (dp.id_kemasan = k.id)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (b.satuan_kekuatan = s.id)
        where p.id_pelanggan = '$id_pelanggan' and date(p.waktu) = '$tanggal'";
    //echo $sql;
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

function billing_get_total_jasa($tanggal, $id_pelanggan) {
    $sqk = "select sum(t.nominal) as total_jasa, t.*, tr.nama from tindakan t
        join tarif tr on (t.id_tarif = tr.id)
        join pendaftaran pdf on (t.id_pendaftaran = pdf.id)
        where pdf.id_pelanggan = '$id_pelanggan' and date(pdf.waktu) = '$tanggal'";
    // group by tr.id
    //echo $sqk;
    $result =  mysql_fetch_object(mysql_query($sqk));
    return $result;
}

function laporan_jasa_pelayanan_load_data($param) {
    $q = NULL;
    if ($param['awal'] !== '' and $param['akhir'] !== '') {
        $q.=" and p.tanggal between '".$param['awal']."' and '".$param['akhir']."'";
    }
    if (isset($param['nakes']) and $param['nakes'] !== '') {
        $q.=" and pp.id_nakes = '".$param['nakes']."'";
    }
    $sql = "select pp.*, d.nama, d.id as no_sip, t.nama as tarif, pl.nama as pasien, pl.id as no_rm from perawat_pemeriksaan pp
            join pemeriksaan p on (pp.id_pemeriksaan = p.id_auto)
            join dokter d on (pp.id_nakes = d.id)
            join tarif t on (pp.id_tarif = t.id)
            join pendaftaran pd on (p.id_pendaftaran = pd.id)
            join pelanggan pl on (pd.id_pelanggan = pl.id)
            where pp.id is not NULL $q order by pp.id_nakes asc";
    //echo "<pre>".$sql."</pre>";
    $result = mysql_query($sql);
    $rows = array();
    while ($metallica = mysql_fetch_object($result)) {
        $rows[] = $metallica;
    }
    return $rows;
}

/*Laba Rugi*/
function pendapatan_penjualan_load_data($awal, $akhir) {
    $sql = "select IFNULL(sum(masuk),'0') as penjualan_barang from arus_kas where transaksi like ('Penjualan%') and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

function pendapatan_jasa_load_data($awal, $akhir) {
    $sql = "select IFNULL(sum(rr.nominal),'0') as jasa from resep_r rr join resep r on (r.id = rr.id_resep) where date(r.waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

function penerimaan_kas_load_data($awal, $akhir) {
    $sql = "select masuk as penerimaan, keterangan as penerimaan_pengeluaran_nama from arus_kas where transaksi = 'Lain-lain' and keluar = '0' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function total_penerimaan_kas_load_data($awal, $akhir) {
    $sql = "select sum(masuk) as penerimaan_total from arus_kas where date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

function pendapatan_lain_lain_load_data($awal, $akhir) {
    $sql = "select IFNULL(sum(masuk),'0') as penerimaan_lain from arus_kas where transaksi = 'Lain-lain' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

function hna_load_data($awal, $akhir) {
    $sql = "select IFNULL(sum(dp.hna*dp.qty),'0') as total_hna from detail_penjualan dp join penjualan p on (p.id = dp.id_penjualan) where date(p.waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    //echo $sql;
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

function pengeluaran_kas_load_data($awal, $akhir) {
    $sql = "select IFNULL(keluar,'0') as pengeluaran, keterangan as penerimaan_pengeluaran_nama from arus_kas where transaksi = 'Lain-lain' and masuk = '0' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}
function total_pengeluaran_kas_load_data($awal, $akhir) {
    $sql = "select IFNULL(sum(keluar),'0') as pengeluaran_total from arus_kas where transaksi = 'Lain-lain' and date(waktu) between '".  date2mysql($awal)."' and '".  date2mysql($akhir)."'";
    $row = mysql_fetch_object(mysql_query($sql));
    return $row;
}

?>