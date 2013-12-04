<?php

include_once '../config/database.php';

function apotek_atribute() {
    $sql = mysql_query("select * from klinik");
    $row = mysql_fetch_object($sql);
    return $row;
}

function load_data_barang($param) {
    $q = "and b.aktif = '".$_GET['aktif']."'";
    if ($param['id'] !== '') {
        $q.= "and b.id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q.="and (
            b.nama like ('%".$param['search']."%') or 
            p.nama like ('%".$param['search']."%') or 
            g.nama like ('%".$param['search']."%') or 
            b.rak like ('%".$param['search']."%') or 
            b.indikasi like ('%".$param['search']."%') or 
            b.dosis like ('%".$param['search']."%') or 
            b.kandungan like ('%".$param['search']."%') or 
            b.perhatian like ('%".$param['search']."%') or 
            b.kontra_indikasi like ('%".$param['search']."%') or 
            b.efek_samping like ('%".$param['search']."%') or 
            b.aturan_pakai like ('%".$param['search']."%') or 
            k.nama like ('%".$param['search']."%'))";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select b.*, p.nama as pabrik, g.nama as golongan, f.id as id_farmakoterapi, st.nama as satuan, sd.nama as sediaan 
        from barang b 
        left join pabrik p on (b.id_pabrik = p.id)
        left join golongan g on (b.id_golongan = g.id)
        left join satuan st on (b.satuan_kekuatan = st.id)
        left join kelas_terapi k on (k.id = b.id_kelas_terapi)
        left join farmako_terapi f on (f.id = k.id_farmako_terapi)
        left join sediaan sd on (b.id_sediaan = sd.id) where b.id is not NULL $q order by b.nama
        ";
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

function load_data_pabrik($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = " and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from pabrik where id is not NULL $q order by nama";
    
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

function load_data_jadwal_praktek($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and j.id = '".$param['id']."'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select j.*, d.nama from jadwal_dokter j join
         dokter d on (d.id = j.id_dokter) order by d.nama";
    
    $query = mysql_query($sql.$limit);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    $total = mysql_num_rows(mysql_query($sql));
//    $page  = mysql_num_rows(mysql_query("select j.*, d.nama from jadwal_dokter j join
//         dokter d on (d.id = j.id_dokter) group by d.id"));
    $result['data'] = $data;
    $result['total']= $total;
    
    return $result;
}

function load_data_instansi($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = " and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from instansi where id is not NULL $q order by nama";
    
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

function load_data_supplier($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = "and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = " and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from supplier where id is not NULL $q order by nama";
    
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

function load_data_bank($param = null) {
    $q = null; $limit = NULL;
    if (isset($param['id']) and $param['id'] !== '') {
        $q = "and id = '".$param['id']."'";
    }
    if (isset($param['search']) and $param['search'] !== '') {
        $q = " and nama like '%".$param['search']."%'";
    }
    if (isset($param)) {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    $sql = "select * from bank where id is not NULL $q order by nama";
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

function load_data_customer($param) {
    $q = null; $limit = NULL;
    if (isset($param['id']) and $param['id'] !== '') {
        $q = "and p.id = '".$param['id']."'";
    }
    if (isset($param['search']) and $param['search'] !== '') {
        $q = "and p.nama like '%".$param['search']."%' 
            or p.id like '%".$param['search']."%' 
            or p.alamat like '%".$param['search']."%' 
            or p.kabupaten like '%".$param['search']."%' 
            or p.telp like '%".$param['search']."%' 
            or p.email like '%".$param['search']."%'";
    }
    if (!isset($param['print'])) {
        $limit = " limit ".$param['start'].", ".$param['limit']."";
    }
    $sql = "select p.*, a.nama as asuransi from pelanggan p
        left join asuransi a on (p.id_asuransi = a.id) where p.id is not NULL $q 
        order by p.id";
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

function load_data_karyawan($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = "and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = "and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from karyawan where id is not NULL $q 
        order by nama";
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

function load_data_layanan($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = "and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = "and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from tarif where id is not NULL $q 
        order by nama";
    
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

function load_data_asuransi($id = null) {
    $q = null;
    if ($id !== NULL) {
        $q = "where id = '$id'";
    }
    
    $sql = "select * from asuransi $q order by nama asc";
    //echo $sql;
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function load_data_asuransi_list($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = "and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = "and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from asuransi where id is not NULL $q order by nama asc";
    
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

function golongan_load_data() {
    $sql = "select * from golongan order by nama asc";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function satuan_load_data($status = null) {
    $q = NULL;
    if ($status != NULL) {
        $q = "where is_satuan_kemasan = '$status'";
    }
    $sql = "select * from satuan $q order by nama asc";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function sediaan_load_data($status = null) {
    $q = NULL;
    if ($status != NULL) {
        $q = "where id = '$status'";
    }
    $sql = "select * from sediaan $q order by nama asc";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function admr_load_data() {
    return array('Oral','Rektal','Infus','Topikal','Sublingual','Transdermal','Intrakutan','Subkutan','Intravena','Intramuskuler','Vagina','Injeksi','Intranasal','Intraokuler','Intraaurikuler','Intrapulmonal','Implantasi','Subkutan','Intralumbal','Intrarteri');
}

function load_data_akun() {
    $sql = "select * from akun order by kode asc";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function load_data_dokter($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = "and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = "and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from dokter where id is not NULL $q 
        order by nama";
    
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

function perundangan_load_data() {
    return array('Bebas','Bebas Terbatas','OWA','Keras','Psikotropika','Narkotika');
}

function farmakoterapi_load_data() {
    $sql = "select * from farmako_terapi ORDER by nama";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function fda_load_data() {
    return array('A','B','C','D','X');
}

function kemasan_load_data($id) {
    $sql = "select k.*, s.nama from kemasan k
        join satuan s on (k.id_kemasan = s.id) where k.id_barang = '$id'";
    $query = mysql_query($sql);
    $data = array();
    while ($row = mysql_fetch_object($query)) {
        $data[] = $row;
    }
    return $data;
}

function dinamic_load_data($id_kemasan) {
    $sql = mysql_query("select * from dinamic_harga_jual where id_kemasan = '$id_kemasan'");
    $data = array();
    while ($row = mysql_fetch_object($sql)) {
        $data[] = $row;
    }
    return $data;
}

function load_data_golongan($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from golongan where id is not NULL $q order by nama";
    
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

function load_data_farmakoterapi($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = " and nama like '%".$param['search']."%'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from farmako_terapi where id is not NULL $q order by nama";
    
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

function load_data_kelasterapi($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and k.id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = " and (k.nama like '%".$param['search']."%' or f.nama like '%".$param['search']."%')";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select k.*, f.nama as farmakoterapi from kelas_terapi k 
        join farmako_terapi f on (k.id_farmako_terapi = f.id) 
        where k.id is not NULL $q order by k.nama";
    
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

function penyakit_load_data($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    if ($param['search'] !== '') {
        $q = " and (topik like '%".$param['search']."%' or sub_kode like '%".$param['search']."%')";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select * from penyakit where id is not NULL $q order by topik";
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

function tarif_load_data() {
    $sql = mysql_query("select * from tarif order by nama");
    $data = array();
    while ($row = mysql_fetch_object($sql)) {
        $data[] = $row;
    }
    return $data;
}

function get_data_day() {
    return array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu');
}

function item_kit_load_data($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select i.*, concat_ws(' ',b.nama,b.kekuatan,s.nama) as nama_barang, st.nama as kemasan, id.jumlah from item_kit i
        join item_kit_detail id on (i.id = id.id_item_kit)
        join kemasan k on (id.id_kemasan = k.id)
        join barang b on (k.id_barang = b.id)
        left join satuan s on (s.id = b.satuan_kekuatan)
        left join satuan st on (k.id_kemasan = st.id)
        where i.id is not NULL $q order by i.nama
        ";
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

function load_data_user_account($param) {
    $q = null;
    if ($param['id'] !== '') {
        $q = " and id = '".$param['id']."'";
    }
    $limit = " limit ".$param['start'].", ".$param['limit']."";
    $sql = "select u.*, k.nama, k.email, k.telp, k.alamat from users u
        join karyawan k on (u.id_karyawan = k.id)
        ";
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

?>