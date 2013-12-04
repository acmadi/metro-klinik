<?php
include_once '../config/database.php';
include_once '../inc/functions.php';
$method = $_GET['method'];
date_default_timezone_set("Asia/Jakarta");
if ($method === 'save_pemesanan') {
    session_start();
    $id             = $_POST['no_sp'];
    $tanggal        = date2mysql($_POST['tanggal'])." ".date("H:i:s");
    $tgl_datang     = date2mysql($_POST['tanggal_datang']);
    $id_supplier    = $_POST['id_supplier'];
    $id_barang      = $_POST['id_barang'];
    $id_kemasan     = $_POST['kemasan'];
    $jumlah         = $_POST['jumlah'];
    //$id_user        = 'NULL';
    $sql = "insert INTO pemesanan set
        id = '$id',
        tanggal = '$tanggal',
        tgl_datang = '$tgl_datang',
        id_supplier = '$id_supplier',
        id_users = '".$_SESSION['id_user']."'";
    
    mysql_query($sql);
    $id_pemesanan = $id;
    
    foreach ($id_barang as $key => $data) {
        $id_packing = mysql_fetch_object(mysql_query("select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'"));
        //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
        $sql = "insert into detail_pemesanan set
            id_pemesanan = '$id_pemesanan',
            id_kemasan = '".$id_packing->id."',
            jumlah = '$jumlah[$key]'";
        //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
        //echo $sql;
        mysql_query($sql);
    }
    
    $result['status'] = TRUE;
    $result['id_pemesanan'] = get_last_pemesanan();
    $result['id'] = $id_pemesanan;
    die(json_encode($result));
}

if ($method === 'delete_pemesanan') {
    $id     = $_GET['id'];
    mysql_query("delete from pemesanan where id = '$id'");
}

if ($method === 'save_penerimaan') {
    $faktur         = $_POST['faktur'];
    $tanggal        = date2mysql($_POST['tanggal']);
    $no_sp          = $_POST['no_sp'];
    $supplier       = $_POST['id_supplier'];
    $ppn            = $_POST['ppn'];
    $materai        = currencyToNumber($_POST['materai']);
    $tempo          = date2mysql($_POST['tempo']);
    //$id_user        = ""; // unUsed
    $disc_pr        = $_POST['disc_pr'];
    $disc_rp        = currencyToNumber($_POST['disc_rp']);
    $total          = currencyToNumber($_POST['total']);
    $id_penerimaan  = $_POST['id_penerimaan'];
    $hna            = $_POST['hna'];
    
    if ($id_penerimaan === '') {
        $sql = "insert into penerimaan set
            faktur = '$faktur',
            tanggal = '$tanggal',
            id_supplier = '$supplier',
            id_pemesanan = '$no_sp',
            ppn = '$ppn',
            materai = '$materai',
            jatuh_tempo = '$tempo',
            diskon_persen = '$disc_pr',
            diskon_rupiah = '$disc_rp',
            total = '$total'";
        mysql_query($sql);
        $id = mysql_insert_id();
        
        $id_barang  = $_POST['id_barang'];
        $id_kemasan = $_POST['satuan'];
        $jumlah     = $_POST['jumlah'];
        $no_batch   = $_POST['nobatch'];
        $ed         = $_POST['ed'];
        $harga      = $_POST['harga'];
        $diskon_pr  = $_POST['diskon_pr'];
        $diskon_rp  = $_POST['diskon_rp'];
        foreach ($id_barang as $key => $data) {
            $query  = mysql_query("select * from kemasan where id_barang = '$data' and id_kemasan = '$id_kemasan[$key]'");
            $rows   = mysql_fetch_object($query);
            
            $harga_a= currencyToNumber($harga[$key]);
            
            $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($diskon_pr[$key]/100))) / ($jumlah[$key]);
            $hpp_ppn	= ($ppn/100)*$base_hpp;
            $hpp 	= $base_hpp+$hpp_ppn;
            
            $sql = "insert into detail_penerimaan set
                id_penerimaan = '$id',
                id_kemasan = '".$rows->id."',
                nobatch = '$no_batch[$key]',
                expired = '".date2mysql($ed[$key])."',
                harga = '$harga_a',
                jumlah = '$jumlah[$key]',
                disc_pr = '$diskon_pr[$key]',
                disc_rp = '".currencyToNumber($diskon_rp[$key])."',
                hpp = '$hpp'
                ";
            mysql_query($sql);
            
            mysql_query("update barang set hna = '".$hna[$key]."' where id = '$data'");
            
            
            $stok= "insert into stok set
                waktu = '$tanggal ".date("H:i:s")."',
                id_transaksi = '$id',
                transaksi = 'Penerimaan',
                nobatch = '$no_batch[$key]',
                id_barang = '$data',
                ed = '".date2mysql($ed[$key])."',
                masuk = '".($jumlah[$key]*($rows->isi*$rows->isi_satuan))."'
            ";
            mysql_query($stok);
        }
        $result['action'] = 'add';
    } else {
        $sql = "update penerimaan set
            faktur = '$faktur',
            tanggal = '$tanggal',
            id_supplier = '$supplier',
            id_pemesanan = '$no_sp',
            ppn = '$ppn',
            materai = '$materai',
            jatuh_tempo = '$tempo',
            diskon_persen = '$disc_pr',
            diskon_rupiah = '$disc_rp',
            total = '$total'
            where id = '$id_penerimaan'";
        mysql_query($sql);
        $id = $id_penerimaan;
        mysql_query("delete from detail_penerimaan where id_penerimaan = '$id_penerimaan'");
        $id_barang  = $_POST['id_barang'];
        $id_kemasan = $_POST['satuan'];
        $jumlah     = $_POST['jumlah'];
        $no_batch   = $_POST['nobatch'];
        $ed         = $_POST['ed'];
        $harga      = $_POST['harga'];
        $diskon_pr  = $_POST['diskon_pr'];
        $diskon_rp  = $_POST['diskon_rp'];
        foreach ($id_barang as $key => $data) {
            $query = mysql_query("select * from kemasan where id_barang = '$data' and id_kemasan = '$id_kemasan[$key]'");
            $rows  = mysql_fetch_object($query);
            
            $base_hpp 	= ((currencyToNumber($harga[$key])*$jumlah[$key]) - ((currencyToNumber($harga[$key])*$jumlah[$key]) * ($diskon_pr[$key]/100))) / ($jumlah[$key]);
            $hpp_ppn	= ($ppn/100)*$base_hpp;
            $hpp 	= $base_hpp+$hpp_ppn;
            
            $sql = "insert into detail_penerimaan set
                id_penerimaan = '$id',
                id_kemasan = '".$rows->id."',
                nobatch = '$no_batch[$key]',
                expired = '".date2mysql($ed[$key])."',
                harga = '$harga_a',
                jumlah = '$jumlah[$key]',
                disc_pr = '$diskon_pr[$key]',
                disc_rp = '".currencyToNumber($diskon_rp[$key])."',
                hpp = '$hpp'
                ";
            mysql_query($sql);
        }
        $result['action'] = 'edit';
    }
    $result['status'] = TRUE;
    $result['id_penerimaan'] = $id;
    
    die(json_encode($result));
}

if ($method === 'delete_penerimaan') {
    $id     = $_GET['id'];
    mysql_query("delete from penerimaan where id = '$id'");
    mysql_query("delete from stok where id_transaksi = '$id' and transaksi = 'Penerimaan'");
}

if ($method === 'save_stokopname') {
    $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
    $id_barang  = $_POST['id_barang'];
    $nobatch    = $_POST['nobatch'];
    $ed         = $_POST['ed'];
    $masuk      = $_POST['masuk'];
    $keluar     = $_POST['keluar'];
    
    foreach ($id_barang as $key => $data) {
        $sql = "insert into stok set
            waktu = '$tanggal',
            transaksi = 'Stok Opname',
            nobatch = '$nobatch[$key]',
            id_barang = '$data',
            ed = '".date2mysql($ed[$key])."',
            masuk = '$masuk[$key]',
            keluar = '$keluar[$key]'
        ";
        mysql_query($sql);
    }
    die(json_encode(array('status' => TRUE)));
}

if ($method === 'delete_stokopname') {
    $id = $_GET['id'];
    mysql_query("delete from stok where id = '$id'");
}

if ($method === 'save_penjualannr') {
    session_start();
    $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
    $customer   = ($_POST['id_customer'] !== '')?"'".$_POST['id_customer']."'":"NULL";
    $diskon_pr  = $_POST['diskon_pr'];
    $diskon_rp  = currencyToNumber($_POST['diskon_rp']);
    $ppn        = $_POST['ppn'];
    $total      = currencyToNumber($_POST['total_penjualan']);
    $tuslah     = currencyToNumber($_POST['tuslah']);
    $asuransi   = ($_POST['asuransi'] !== '')?$_POST['asuransi']:'NULL';
    $embalage   = currencyToNumber($_POST['embalage']);
    $reimburse  = isset($_POST['reimburse'])?$_POST['reimburse']:'0';
    $uangserah  = currencyToNumber($_POST['pembayaran']);
    $pembayaran = currencyToNumber($_POST['pembulatan']); // yang dientrikan pembulatan pembayarannya
    
    $id_ikit    = isset($_POST['id_ikit'])?$_POST['id_ikit']:NULL;
    $sql = "insert into penjualan set
        waktu = '$tanggal',
        id_pelanggan = $customer,
        diskon_persen = '$diskon_pr',
        diskon_rupiah = '$diskon_rp',
        ppn = '$ppn',
        total = '$total',
        tuslah = '$tuslah',
        embalage = '$embalage',
        id_asuransi = $asuransi,
        reimburse = '$reimburse',
        bayar = '$uangserah'";
    //echo $sql;
    mysql_query($sql);
    $id_penjualan = mysql_insert_id();
    
    $query = "insert into detail_bayar_penjualan set
        waktu = '$tanggal',
        id_penjualan = '$id_penjualan',
        bayar = '$pembayaran'";
    mysql_query($query); // insert ke tabel detail pembayaran
    
    $query2= "insert into arus_kas set
        id_transaksi = '$id_penjualan',
        transaksi = 'Penjualan Non Resep',
        id_users = '$_SESSION[id_user]',
        waktu = '$tanggal',
        masuk = '$pembayaran'";
    mysql_query($query2);
    
    $id_barang  = $_POST['id_barang'];
    $kemasan    = isset($_POST['kemasan'])?$_POST['kemasan']:NULL;
    $jumlah     = $_POST['jumlah'];
    $harga_jual = $_POST['harga_jual'];
    $ed         = isset($_POST['ed'])?$_POST['ed']:NULL;
    $disc_pr    = isset($_POST['diskon_persen'])?$_POST['diskon_persen']:'0';
    $disc_rp    = isset($_POST['diskon_rupiah'])?$_POST['diskon_rupiah']:'0';
    
    if ($id_barang !== '') {
        foreach ($id_barang as $key => $data) {
            $query = mysql_query("select k.*, b.nama as nama_barang from kemasan k join barang b on (k.id_barang = b.id) where k.id = '$kemasan[$key]'");
            $rows  = mysql_fetch_object($query);
            $isi   = $rows->isi*$rows->isi_satuan;
            
            $sql = "insert into detail_penjualan set
                id_penjualan = '$id_penjualan',
                id_kemasan = '$kemasan[$key]',
                expired = ".(isset($ed[$key])?$ed[$key]:'NULL').",
                qty = '".($jumlah[$key]*$isi)."',
                harga_jual = '".currencyToNumber($harga_jual[$key])."',
                disc_pr = '$disc_pr[$key]',
                disc_rp = '".currencyToNumber($disc_rp[$key])."'";
            mysql_query($sql);
            
            $qry = "insert into detail_penjualan_nota set
                id_penjualan = '$id_penjualan',
                nama_barang = '".$rows->nama_barang."',
                jumlah = '".($jumlah[$key]*$isi)."',
                harga_jual = '".currencyToNumber($harga_jual[$key])."',
                disc_pr = '$disc_pr[$key]',
                disc_rp = '".currencyToNumber($disc_rp[$key])."'";
            //echo $qry."<br/>";
            mysql_query($qry);
            
            $last = mysql_fetch_object(mysql_query("select * from stok where id_barang = '$data' order by id desc limit 1"));
            
            //$fefo  = mysql_query("SELECT id_barang, ed, (sum(masuk)-sum(keluar)) as sisa FROM `stok` WHERE id_barang = '$data' and ed > '".date("Y-m-d")."' group by ed order by ed");
            //while ($val = mysql_fetch_object($fefo)) {
                
                $stok = "insert into stok set
                    waktu = '$tanggal',
                    id_transaksi = '$id_penjualan',
                    transaksi = 'Penjualan',
                    id_barang = '$data',
                    ed = ".(isset($ed[$key])?$ed[$key]:'NULL').",
                    keluar = '".($jumlah[$key]*$isi)."'";
                //echo $stok;
                mysql_query($stok);
            //}
        }
    }
        if ($id_ikit !== NULL) {
            $disc_pr = $_POST['diskon_persen_ik'];
            $disc_rp = $_POST['diskon_rupiah_ik'];
            foreach ($id_ikit as $nu => $rows) {
                $cek = mysql_fetch_object(mysql_query("select nama as nama_barang, harga_jual from item_kit where id = '$rows'"));
                $qry = "insert into detail_penjualan_nota set
                    id_penjualan = '$id_penjualan',
                    nama_barang = '".$cek->nama_barang."',
                    jumlah = '$jumlah[$nu]',
                    harga_jual = '".$cek->harga_jual."',
                    disc_pr = '$disc_pr[$nu]',
                    disc_rp = '".currencyToNumber($disc_rp[$nu])."'";
                mysql_query($qry);
                $get = mysql_query("select id.*, b.hna+(b.hna*(b.margin_non_resep/100)) as harga_jual, k.id_barang 
                    from item_kit i 
                    join item_kit_detail id on (i.id = id.id_item_kit) 
                    join kemasan k on (id.id_kemasan = k.id) 
                    join barang b on (k.id_barang = b.id)
                    where i.id = '$rows'");
                while ($do = mysql_fetch_object($get)) {
                    $sql = "insert into detail_penjualan set
                    id_penjualan = '$id_penjualan',
                    id_kemasan = '".$do->id_kemasan."',
                    qty = '".($jumlah[$nu]*$do->jumlah)."',
                    harga_jual = '".$do->harga_jual."',
                    disc_pr = '$disc_pr[$nu]',
                    disc_rp = '".currencyToNumber($disc_rp[$nu])."'";
                    mysql_query($sql);
                    
                    $stok = "insert into stok set
                        waktu = '$tanggal',
                        id_transaksi = '$id_penjualan',
                        transaksi = 'Penjualan',
                        id_barang = '".$do->id_barang."',
                        keluar = '".($do->harga_jual*$do->jumlah)."'";
                    //echo $stok;
                    mysql_query($stok);
                }
            }
        }
    die(json_encode(array('status' => TRUE, 'id' => $id_penjualan)));
}

if ($method === 'delete_penjualannr') {
    $id     = $_GET['id'];
    mysql_query("delete from penjualan where id = '$id'");
    mysql_query("delete from stok where transaksi = 'Penjualan' and id_transaksi = '$id'");
}

if ($method === 'delete_penjualan') {
    $id     = $_GET['id'];
    mysql_query("delete from penjualan where id = '$id'");
    mysql_query("delete from stok where transaksi = 'Penjualan' and id_transaksi = '$id'");
}

if ($method === 'save_retur_penerimaan') {
    $tanggal        = date2mysql($_POST['tanggal']);
    $id_supplier    = $_POST['id_supplier'];
    $id_barang      = $_POST['id_barang'];
    $id_kemasan     = $_POST['id_kemasan'];
    $ed             = $_POST['ed'];
    $jumlah         = $_POST['jumlah'];
    $id_retur       = $_POST['id_retur_penerimaan'];
    
    if ($id_retur === '') {
        $sql = "insert into retur_penerimaan set
            tanggal = '$tanggal',
            id_supplier = '$id_supplier'";
        mysql_query($sql);
        $id         = mysql_insert_id();
        foreach ($id_barang as $key => $data) {
            $kemasan = mysql_fetch_object(mysql_query("select id from kemasan where id_barang = '$data' and id_kemasan = '$id_kemasan[$key]'"));
            $query = "insert into detail_retur_penerimaan set
                id_retur_penerimaan = '$id',
                id_kemasan = '".$kemasan->id."',
                expired = '".date2mysql($ed[$key])."',
                jumlah = '$jumlah[$key]'
                ";
            //echo $query;
            mysql_query($query);
            $query1 = mysql_query("select dp.* from kemasan k
                join barang b on (k.id_barang = b.id) 
                join detail_penerimaan dp on (k.id = dp.id_kemasan)
                where b.id = '$data' and k.id = '$id_kemasan[$key]' and dp.expired = '".date2mysql($ed[$key])."'");
            $row = mysql_fetch_object($query1);
           $query2 = "insert into stok set
                waktu = '$tanggal ".date("H:i:s")."',
                id_transaksi = '$id',
                transaksi = 'Retur Penerimaan',
                nobatch = '".(isset($row->nobatch)?$row->nobatch:'')."',
                id_barang = '$data',
                ed = '".date2mysql($ed[$key])."',
                keluar = '$jumlah[$key]'";
           //echo $query2;
           mysql_query($query2);
        }
    }
    $result['status'] = TRUE;
    $result['action'] = 'add';
    die(json_encode($result));
}

if ($method === 'save_resep') {
    session_start();
    $noresep    = $_POST['noresep'];
    //$waktu      = date2mysql($_POST['waktu']).' '.date("H:i:s");
    $dokter     = $_POST['id_dokter'];
    $pasien     = $_POST['id_pasien'];
    $keterangan = $_POST['keterangan'];
    $id_resep   = $_POST['id_resep'];
    $id_daftar  = $_POST['id_pendaftaran'];
    
    //$id_user    = 'NULL';
    if ($id_resep === '') {
        $sql = "insert into resep set
            id = '$noresep',
            id_dokter = '$dokter',
            id_pendaftaran = '$id_daftar',
            id_pasien = '$pasien',
            keterangan = '$keterangan'";
        mysql_query($sql);
        $id = $noresep;
        $result['action'] = 'add';
    } else {
        $sql = "update resep set
            id_dokter = '$dokter',
            id_pasien = '$pasien',
            keterangan = '$keterangan'
            where id = '$id_resep'";
        mysql_query($sql);
        $id = $id_resep;
        mysql_query("delete from resep_r where id_resep = '$id'");
        $result['action'] = 'edit';
    }
    
    $customer   = $_POST['id_pasien'];
    $diskon_pr  = 0;
    $diskon_rp  = 0;
    $ppn        = 0;
    $total      = $_POST['total_penjualan'];
    $tuslah     = 0;
    $asuransi   = 'NULL';
    $embalage   = 0;
    $reimburse  = 0;
    $pembayaran = 0; // yang dientrikan pembulatan pembayarannya
    // cek apakah nomor resep pernah ditransaksikan
    $cek = mysql_query("select count(*) as jumlah, id from penjualan where id_resep = '$id_resep'");
    $row = mysql_fetch_object($cek);
    
    $sql = "insert into penjualan set
        waktu = NOW(),
        id_resep = '$id',
        id_pelanggan = '$pasien',
        total = '$total'";
    
    mysql_query($sql);
    $id_penjualan = mysql_insert_id();
    
    if (isset($_POST['no_r']) and isset($_POST['jp']) and isset($_POST['jpi']) and isset($_POST['id_tarif']) and isset($_POST['jasa']) and isset($_POST['hrg_pokok'])) {
            $no_r       = $_POST['no_r'];
            $id_barang  = isset($_POST['id_barang'])?$_POST['id_barang']:NULL;
            $jml_minta  = $_POST['jp'];
            $jml_tebus  = $_POST['jt'];
            $aturan     = $_POST['a'];
            $pakai      = $_POST['p'];
            $iterasi    = $_POST['it'];
            //$kekuatan   = $_POST['kekuatan'];
            $dosis_racik= $_POST['dr'];
            $jml_pakai  = $_POST['jpi'];
            $id_tarif   = $_POST['id_tarif'];
            $jasa_apt   = $_POST['jasa'];
            $harga_brg  = $_POST['hrg_pokok'];

            foreach ($no_r as $arr => $data) {

                if (isset($id_barang[$arr])) {
                    $query = "insert into resep_r set
                        id_resep = '$id',
                        r_no = '$data',
                        resep_r_jumlah = '$jml_minta[$arr]',
                        tebus_r_jumlah = '$jml_tebus[$arr]',
                        aturan = '$aturan[$arr]',
                        pakai = '$pakai[$arr]',
                        iter = '$iterasi[$arr]',
                        id_tarif = ".(($id_tarif[$arr] !== '0')?$id_tarif[$arr]:'NULL').",
                        nominal = '".  currencyToNumber($jasa_apt[$arr])."',
                        id_barang = '$id_barang[$arr]',
                        jual_harga = '".  currencyToNumber($harga_brg[$arr])."',
                        dosis_racik = '$dosis_racik[$arr]',
                        jumlah_pakai = '$jml_pakai[$arr]'";
                    //echo $query."<br/>";
                    mysql_query($query);
                    //$id_resep_r = mysql_insert_id();
                    $qwert = "insert into resep_ikit set
                        id_resep = '$id',
                        id_barang = '$id_barang[$arr]',
                        jumlah = '$jml_pakai[$arr]',
                        harga_jual = '".  currencyToNumber($harga_brg[$arr])."'";
                    //echo $qwert."<br/>";
                    mysql_query($qwert);

                    /*FROM PENJUALAN*/
                    $query = mysql_query("select k.*, b.nama as nama_barang from kemasan k join barang b on (k.id_barang = b.id) where b.id = '$id_barang[$arr]' and k.default_kemasan = '1'");
                    $rows  = mysql_fetch_object($query);
                    $isi   = $rows->isi*$rows->isi_satuan;
                    $sql = "insert into detail_penjualan set
                        id_penjualan = '$id_penjualan',
                        id_kemasan = '".$rows->id."',
                        qty = '".($jml_pakai[$arr]*$isi)."',
                        harga_jual = '".currencyToNumber($harga_brg[$arr])."'";
                    mysql_query($sql);

                    $qry = "insert into detail_penjualan_nota set
                        id_penjualan = '$id_penjualan',
                        nama_barang = '".$rows->nama_barang."',
                        jumlah = '".($jml_pakai[$arr]*$isi)."',
                        harga_jual = '".currencyToNumber($harga_brg[$arr])."'";
                    mysql_query($qry);

                    $last = mysql_fetch_object(mysql_query("select * from stok where id_barang = '$id_barang[$arr]' order by id desc limit 1"));

                    $fefo  = mysql_query("SELECT id_barang, ed, IFNULL((sum(masuk)-sum(keluar)),'0') as sisa FROM `stok` WHERE id_barang = '$id_barang[$arr]' and ed > '".date("Y-m-d")."' group by ed HAVING sisa > 0 order by ed limit 1");
                    $ed    = mysql_fetch_object($fefo);
                    $stok = "insert into stok set
                        waktu = NOW(),
                        id_transaksi = '$id_penjualan',
                        transaksi = 'Penjualan',
                        id_barang = '$id_barang[$arr]',
                        ed = ".(isset($ed->ed)?$ed->ed:'NULL').",
                        keluar = '".($jml_pakai[$arr]*$isi)."'";
                    //echo $stok;
                    mysql_query($stok);
                    /*END FROM PENJUAALAN*/

                }
            }
    }
    
    if (isset($_POST['id_ikit'])) {
        $id_ikit    = isset($_POST['id_ikit'])?$_POST['id_ikit']:NULL;
        $no_rik     = $_POST['no_rik'];
        $harga_brgik= $_POST['hrg_barangik'];
        $jml_mintaik= $_POST['jpik'];
        $jml_tebusik= $_POST['jtik'];
        $aturanik   = $_POST['aik'];
        $pakaiik    = $_POST['pik'];
        $iterasiik  = $_POST['itik'];
        //$kekuatan   = $_POST['kekuatan'];
        $dosis_racikik= $_POST['drik'];
        $jml_pakaiik= $_POST['jpiik'];
        $id_tarifik = $_POST['id_tarifik'];
        $jasa_aptik = $_POST['jasaik'];

        foreach ($no_rik as $arr => $data) {
            if (isset($id_ikit[$arr])) {
                //foreach ($id_ikit as $nu => $rows) {

                $qwert = "insert into resep_ikit set
                    id_resep = '$id',
                    id_item_kit = '$id_ikit[$arr]',
                    jumlah = '$jml_pakaiik[$arr]',
                    harga_jual = '". currencyToNumber($harga_brgik[$arr])."'";
                //echo $qwert."<br/>";
                mysql_query($qwert);

                $qru = "select id.*, b.hna+(b.hna*(b.margin_resep/100)) as harga_jual, k.id_barang 
                        from item_kit i 
                        join item_kit_detail id on (i.id = id.id_item_kit) 
                        join kemasan k on (id.id_kemasan = k.id) 
                        join barang b on (k.id_barang = b.id)
                        where i.id = '$id_ikit[$arr]'";

                $query = mysql_query("select * from item_kit where id = '$id_ikit[$arr]'");
                $rows  = mysql_fetch_object($query);

                $qry = "insert into detail_penjualan_nota set
                        id_penjualan = '$id_penjualan',
                        nama_barang = '".$rows->nama."',
                        jumlah = '$jml_pakaiik[$arr]',
                        harga_jual = '".$rows->harga_jual."'";
                mysql_query($qry);

                $get = mysql_query($qru);
                //echo $qru."<br/>";
                    while ($do = mysql_fetch_object($get)) {
                    $query = "insert into resep_r set
                        id_resep = '$id',
                        r_no = '$data',
                        resep_r_jumlah = '$jml_mintaik[$arr]',
                        tebus_r_jumlah = '$jml_tebusik[$arr]',
                        aturan = '$aturanik[$arr]',
                        pakai = '$pakaiik[$arr]',
                        iter = '$iterasiik[$arr]',
                        id_tarif = ".(($id_tarifik[$arr] !== '0')?$id_tarifik[$arr]:'NULL').",
                        nominal = '".  currencyToNumber($jasa_aptik[$arr])."',
                        id_barang = '".$do->id_barang."',
                        jual_harga = '".$do->harga_jual."',
                        dosis_racik = '$dosis_racikik[$arr]',
                        jumlah_pakai = '".($jml_pakaiik[$arr]*$do->jumlah)."'
                        ";
                    mysql_query($query);

                    /*FROM PENJUALAN*/
                    $query = mysql_query("select k.*, b.nama as nama_barang from kemasan k join barang b on (k.id_barang = b.id) where b.id = '".$do->id_barang."' and k.default_kemasan = '1'");
                    $rows  = mysql_fetch_object($query);
                    $isi   = $rows->isi*$rows->isi_satuan;
                    $sql = "insert into detail_penjualan set
                        id_penjualan = '$id_penjualan',
                        id_kemasan = '".$rows->id."',
                        qty = '".($jml_pakaiik[$arr]*$isi)."',
                        harga_jual = '".$do->harga_jual."'";
                    mysql_query($sql);

                    $last = mysql_fetch_object(mysql_query("select * from stok where id_barang = '".$do->id_barang."' order by id desc limit 1"));

                    $fefo  = mysql_query("SELECT id_barang, ed, IFNULL((sum(masuk)-sum(keluar)),'0') as sisa FROM `stok` WHERE id_barang = '".$do->id_barang."' and ed > '".date("Y-m-d")."' group by ed HAVING sisa > 0 order by ed limit 1");
                    $ed    = mysql_fetch_object($fefo);
                    $stok = "insert into stok set
                        waktu = NOW(),
                        id_transaksi = '$id_penjualan',
                        transaksi = 'Penjualan',
                        id_barang = '".$do->id_barang."',
                        ed = ".(isset($ed->ed)?$ed->ed:'NULL').",
                        keluar = '".($jml_pakaiik[$arr]*$isi)."'";
                    //echo $stok;
                    mysql_query($stok);
                    /*END FROM PENJUAALAN*/

                    }
                //}
            }
        }
    }
    
    $result['status'] = TRUE;
    $result['id'] = $id;
    die(json_encode($result));
}

if ($method === 'delete_resep') {
    $id = $_GET['id'];
    mysql_query("delete from resep where id = '$id'");
}

if ($method === 'save_penjualan') {
    session_start();
    $tanggal    = date2mysql($_POST['tanggal']).' '.date("H:i:s");
    $customer   = ($_POST['id_customer'] !== '')?$_POST['id_customer']:"NULL";
    $diskon_pr  = $_POST['diskon_pr'];
    $diskon_rp  = currencyToNumber($_POST['diskon_rp']);
    $ppn        = $_POST['ppn'];
    $total      = $_POST['total_penjualan'];
    $tuslah     = currencyToNumber($_POST['tuslah']);
    $asuransi   = ($_POST['asuransi'] !== '')?$_POST['asuransi']:'NULL';
    $embalage   = currencyToNumber($_POST['embalage']);
    $reimburse  = isset($_POST['reimburse'])?$_POST['reimburse']:'0';
    $pembayaran = currencyToNumber($_POST['pembulatan']); // yang dientrikan pembulatan pembayarannya
    $id_resep   = $_POST['id_resep'];
    // cek apakah nomor resep pernah ditransaksikan
    $cek = mysql_query("select count(*) as jumlah, id from penjualan where id_resep = '$id_resep'");
    $row = mysql_fetch_object($cek);
    if ($row->jumlah === '0') {
        $sql = "insert into penjualan set
            waktu = '$tanggal',
            id_resep = '$id_resep',
            id_pelanggan = '$customer',
            diskon_persen = '$diskon_pr',
            diskon_rupiah = '$diskon_rp',
            ppn = '$ppn',
            total = '$total',
            tuslah = '$tuslah',
            embalage = '$embalage',
            id_asuransi = $asuransi,
            reimburse = '$reimburse'";
        //echo $sql;
        mysql_query($sql);
        $id_penjualan = mysql_insert_id();
    
        $query = "insert into detail_bayar_penjualan set
            waktu = '$tanggal',
            id_penjualan = '$id_penjualan',
            bayar = '$pembayaran'";
        mysql_query($query);
        
        $query2= "insert into arus_kas set
            id_transaksi = '$id_penjualan',
            transaksi = 'Penjualan Resep',
            id_users = '$_SESSION[id_user]',
            waktu = '$tanggal',
            masuk = '$pembayaran'";
        mysql_query($query2);
        
    $id_barang  = $_POST['id_barang'];
    $kemasan    = $_POST['kemasan'];
    $jumlah     = $_POST['jumlah'];
    $harga_jual = $_POST['harga_jual'];
        foreach ($id_barang as $key => $data) {
            $query = mysql_query("select k.*, b.nama as nama_barang from kemasan k join barang b on (k.id_barang = b.id) where k.id = '$kemasan[$key]'");
            $rows  = mysql_fetch_object($query);
            $isi   = $rows->isi*$rows->isi_satuan;
            $sql = "insert into detail_penjualan set
                id_penjualan = '$id_penjualan',
                id_kemasan = '$kemasan[$key]',
                qty = '".($jumlah[$key]*$isi)."',
                harga_jual = '$harga_jual[$key]'
                ";
            mysql_query($sql);
            
            $qry = "insert into detail_penjualan_nota set
                id_penjualan = '$id_penjualan',
                nama_barang = '".$rows->nama_barang."',
                jumlah = '".($jumlah[$key]*$isi)."',
                harga_jual = '".currencyToNumber($harga_jual[$key])."'";
            //echo $qry."<br/>";
            mysql_query($qry);
            
            $last = mysql_fetch_object(mysql_query("select * from stok where id_barang = '$data' order by id desc limit 1"));
            
            $fefo  = mysql_query("SELECT id_barang, ed, IFNULL((sum(masuk)-sum(keluar)),'0') as sisa FROM `stok` WHERE id_barang = '$data' and ed > '".date("Y-m-d")."' group by ed HAVING sisa > 0 order by ed limit 1");
            $ed    = mysql_fetch_object($fefo);
            $stok = "insert into stok set
                waktu = '$tanggal',
                id_transaksi = '$id_penjualan',
                transaksi = 'Penjualan',
                id_barang = '$data',
                ed = '".$ed->ed."',
                keluar = '".($jumlah[$key]*$isi)."'";
            //echo $stok;
            mysql_query($stok);
        }
    } else {
        $id_penjualan = $row->id;
        $sql = "insert into detail_bayar_penjualan set
            waktu = '$tanggal',
            id_penjualan = '$id_penjualan',
            bayar = '$pembayaran'";
        mysql_query($sql);
    }
    die(json_encode(array('status' => TRUE, 'id' => $id_penjualan)));
}

if ($method === 'save_pemeriksaan') {
    $qwe = mysql_query("select count(*) as jumlah from pemeriksaan where tanggal like '%".date("Y-m")."%'");
    $row = mysql_fetch_object($qwe);
    if (!isset($row->jumlah)) {
        $str = "PR.001-".date("m")."/".date("Y");
    } else {
        $str = "PR.".str_pad((string)($row->jumlah+1), 3, "0", STR_PAD_LEFT)."-".date("m")."/".date("Y");
    }
    
    $id_daftar  = $_POST['id_pendaftaran'];
    $id_pemeriksaan = $_POST['id_pemeriksaan'];
    $id         = $str;
    $tanggal    = date2mysql($_POST['tanggal']);
    $anamnesis  = $_POST['anamnesis'];
    $id_dokter  = $_POST['id_dokter'];
    $perawat1   = ($_POST['id_perawat'] !== '')?$_POST['id_perawat']:'NULL';
    $perawat2   = ($_POST['id_perawat2'] !== '')?$_POST['id_perawat2']:'NULL';
    
    $id_diagnosis = isset($_POST['id_diagnosis'])?$_POST['id_diagnosis']:'NULL';
    $id_tindakan  = isset($_POST['id_tindakan'])?$_POST['id_tindakan']:'NULL';
    $id_rek_tindakan = isset($_POST['id_rek_tindakan'])?$_POST['id_rek_tindakan']:'NULL';
    $nominal      = isset($_POST['nominal'])?$_POST['nominal']:'NULL';
    $UploadDirectory	= '../img/pemeriksaan/'; //Upload Directory, ends with slash & make sure folder exist
    $NewFileName= "";
        // replace with your mysql database details
    //echo $id_diagnosis." - ".$id_tindakan." -  ".$id_rek_tindakan."<br/>";
    if (!@file_exists($UploadDirectory)) {
            //destination folder does not exist
            die("Make sure Upload directory exist!");
    }
    if(isset($_FILES['mFile']['name'])) {

            $FileName           = strtolower($_FILES['mFile']['name']); //uploaded file name
            $FileTitle		= mysql_real_escape_string($_POST['pasien']); // file title
            $ImageExt		= substr($FileName, strrpos($FileName, '.')); //file extension
            $FileType		= $_FILES['mFile']['type']; //file type
            //$FileSize		= $_FILES['mFile']["size"]; //file size
            $RandNumber   		= rand(0, 9999999999); //Random number to make each filename unique.
            //$uploaded_date		= date("Y-m-d H:i:s");
            
            switch(strtolower($FileType))
            {
                    //allowed file types
                    case 'image/png': //png file
                    case 'image/gif': //gif file 
                    case 'image/jpeg': //jpeg file
                    case 'application/pdf': //PDF file
                    case 'application/msword': //ms word file
                    case 'application/vnd.ms-excel': //ms excel file
                    case 'application/x-zip-compressed': //zip file
                    case 'text/plain': //text file
                    case 'text/html': //html file
                            break;
                    default:
                            die('Unsupported File!'); //output error
            }


            //File Title will be used as new File name
            $NewFileName = preg_replace(array('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'), array('_', '.', ''), strtolower($FileTitle));
            $NewFileName = $NewFileName.'_'.$RandNumber.$ImageExt;
       //Rename and save uploded file to destination folder.
       if(move_uploaded_file($_FILES['mFile']["tmp_name"], $UploadDirectory . $NewFileName ))
       {
            //die('Success! File Uploaded.');
       }else{
            //die('error uploading File!');
       }
    }
    $sql = "update pemeriksaan set
        id = '$id',
        tanggal = '$tanggal ".date("H:i:s")."',
        id_dokter = '$id_dokter',
        anamnesis = '$anamnesis',
        foto = '$NewFileName'
            where id_auto = '$id_pemeriksaan'";
   mysql_query($sql);
   
   if ($id_diagnosis !== 'NULL') {
        foreach ($id_diagnosis as $key => $data) {
            $query = "insert into diagnosis set
                 id_pemeriksaan = '$id_pemeriksaan',
                 waktu = '$tanggal ".date("H:i:s")."',
                 id_penyakit = '$data'";
            mysql_query($query);
        }
   }

   if ($id_tindakan !== 'NULL') {
        foreach ($id_tindakan as $key => $data) {
            $get   = mysql_fetch_object(mysql_query("select * from tarif where id = '$data'"));
            $query = "insert into tindakan set
                 waktu = '$tanggal ".date("H:i:s")."',
                 id_pemeriksaan = '$id_pemeriksaan',
                 id_tarif = '$data',
                 jasa_dokter = '".$get->jasa_dokter."',
                 jasa_perawat = '".$get->jasa_perawat."',
                 jasa_sarana = '".$get->jasa_sarana."',
                 nominal = '".$get->nominal."'
                 ";
            mysql_query($query);
            
            if ($id_dokter !== '') {
                $tarif = mysql_fetch_object(mysql_query("select jasa_dokter from tarif where id = '$data'"));
                $nominal = $tarif->jasa_dokter;
                $jasa  = "insert into perawat_pemeriksaan set
                    id_pemeriksaan = '$id_pemeriksaan',
                    id_nakes = $id_dokter,
                    id_tarif = '$data',
                    nominal = '".$nominal."'";
                mysql_query($jasa);
            }
            if ($_POST['id_perawat'] !== '') {
                $tarif = mysql_fetch_object(mysql_query("select jasa_perawat from tarif where id = '$data'"));
                $nominal = $tarif->jasa_perawat;
                if ($_POST['id_perawat2'] !== '') {
                    $nominal = $tarif->jasa_perawat/2;
                }
                $jasa  = "insert into perawat_pemeriksaan set
                    id_pemeriksaan = '$id_pemeriksaan',
                    id_nakes = $perawat1,
                    id_tarif = '$data',
                    nominal = '".$nominal."'";
                mysql_query($jasa);
            }
            if ($_POST['id_perawat2'] !== '') {
                $tarif = mysql_fetch_object(mysql_query("select jasa_perawat from tarif where id = '$data'"));
                $nominal = $tarif->jasa_perawat;
                if ($_POST['id_perawat'] !== '') {
                    $nominal = $tarif->jasa_perawat/2;
                }
                $jasa  = "insert into perawat_pemeriksaan set
                    id_pemeriksaan = '$id_pemeriksaan',
                    id_nakes = $perawat2,
                    id_tarif = '$data',
                    nominal = '".$nominal."'";
                mysql_query($jasa);
            }
            
        }
   }
   
   if ($id_rek_tindakan !== 'NULL') {
        foreach ($id_rek_tindakan as $key => $data) {
            $get   = mysql_fetch_object(mysql_query("select * from tarif where id = '$data'"));
            $query = "insert into rek_tindakan set
                 waktu = '$tanggal ".date("H:i:s")."',
                 id_pemeriksaan = '$id_pemeriksaan',
                 id_tarif = '$data'";
            mysql_query($query);
        }
   }
   die(json_encode(array('status' => TRUE, 'id' => $id_daftar)));
}

if ($method === 'delete_pemeriksaan') {
    mysql_query("delete from pemeriksaan where id = '$_GET[id]'");
    mysql_query("delete from diagnosis where id_pendaftaran = '$_GET[id_daftar]'");
    mysql_query("delete from tindakan where id_pendaftaran = '$_GET[id_daftar]'");
    mysql_query("delete from rek_tindakan where id_pendaftaran = '$_GET[id_daftar]'");
    mysql_query("update pendaftaran set waktu_pelayanan = NULL and id_dokter = NULL where id = '$_GET[id_daftar]'");
}

if ($method === 'save_inkaso') {
    session_start();
    $noref      = $_POST['noref'];
    $tanggal    = date2mysql($_POST['tanggal']);
    $id_penerimaan= $_POST['id_penerimaan'];
    $cara_bayar = $_POST['cara_bayar'];
    $id_bank    = ($_POST['bank'] !== '')?$_POST['bank']:'NULL';
    $no_trans   = $_POST['notransaksi'];
    $keterangan = $_POST['keterangan'];
    $nominal    = currencyToNumber($_POST['nominal']);
    
    $sql = "insert into inkaso set
        no_ref = '$noref',
        tanggal = '$tanggal',
        id_penerimaan = '$id_penerimaan',
        cara_bayar = '$cara_bayar',
        id_bank = $id_bank,
        no_transaksi = '$no_trans',
        keterangan = '$keterangan',
        nominal = '$nominal'";
    mysql_query($sql);
    $id = mysql_insert_id();
    
    $query2= "insert into arus_kas set
        id_transaksi = '$id',
        transaksi = 'Inkaso',
        id_users = '$_SESSION[id_user]',
        waktu = '$tanggal ".date("H:i:s")."',
        keluar = '$nominal'";
    mysql_query($query2);
    
    die(json_encode(array('status' => TRUE, 'id' => $id)));
}

if ($method === 'delete_inkaso') {
    $id = $_GET['id'];
    mysql_query("delete from inkaso where id = '$id'");
}

if ($method === 'add_rencana_pemesanan') {
    $id = $_GET['id'];
    mysql_query("insert INTO defecta set
        id_barang = '$id',
        jumlah = '1'");
}

if ($method === 'delete_pemesanan_plant') {
    $id = $_GET['id'];
    mysql_query("delete from defecta where id = '$id'");
}

if ($method === 'save_rencana_pemesanan') {
    session_start();
    $id             = $_POST['no_sp'];
    $tanggal        = date2mysql($_POST['tanggal'])." ".date("H:i:s");
    $tgl_datang     = date2mysql($_POST['tanggal_datang']);
    $id_supplier    = $_POST['id_supplier'];
    $id_barang      = $_POST['id_barang'];
    $id_kemasan     = $_POST['kemasan'];
    $jumlah         = $_POST['jumlah'];
    //$id_user        = 'NULL';
    $sql = "insert INTO pemesanan set
        id = '$id',
        tanggal = '$tanggal',
        tgl_datang = '$tgl_datang',
        id_supplier = '$id_supplier',
        id_users = '".$_SESSION['id_user']."'";
    mysql_query($sql);
    $id_pemesanan = $id;
    
    foreach ($id_barang as $key => $data) {
        $sql = "insert into detail_pemesanan set
            id_pemesanan = '$id_pemesanan',
            id_kemasan = '$id_kemasan[$key]',
            jumlah = '$jumlah[$key]'";
        //echo "select id from kemasan where id_barang = '$data' and id_kemasan = '".$id_kemasan[$key]."'<br/>";
        //echo $sql;
        mysql_query($sql);
        mysql_query("update defecta set status = '1' where id_barang = '".$data."'");
    }
    
    $result['status'] = TRUE;
    $result['id_pemesanan'] = get_last_pemesanan();
    $result['id'] = $id_pemesanan;
    die(json_encode($result));
}

if ($method === 'save_pendaftaran') {
    $waktu      = date("Y-m-d H:i:s");
    $pasien     = $_GET['pasien'];
    $spesialis  = $_GET['spesialis'];
    $noantri    = $_GET['noantri'];
    $id_daftar  = $_GET['id_pendaftaran'];
    
    if ($id_daftar === '') {
        $sql = "insert into pendaftaran set
            waktu = '$waktu',
            id_pelanggan = '$pasien'";
        mysql_query($sql);
        $id_pendaftaran = mysql_insert_id();
        
        $sql2= "insert into pemeriksaan set
            id_pendaftaran = '$id_pendaftaran',
            tanggal_antri = NOW(),
            no_antri = '$noantri',
            id_spesialisasi = '$spesialis'";
        mysql_query($sql2);
        $id_antri = mysql_insert_id();
    } else {
        
        $sql2= "insert into pemeriksaan set
            id_pendaftaran = '$id_daftar',
            tanggal_antri = NOW(),
            no_antri = '$noantri',
            id_spesialisasi = '$spesialis'";
        mysql_query($sql2);
        $id_antri = mysql_insert_id();
    }
    $result['id'] = $id_antri;
    $result['status'] = TRUE;
    die(json_encode($result));
}

if ($method === 'save_in_out_uang') {
    session_start();
    $tanggal = date2mysql($_POST['waktu']).' '.date("H:i:s");
    $jenis   = $_POST['jenis'];
    $nominal = currencyToNumber($_POST['nominal']);
    $keterangan = $_POST['keterangan'];
    
    if ($jenis === 'masuk') {
        $sql = "insert into arus_kas set
        transaksi = 'Lain-lain',
        id_users = '$_SESSION[id_user]',
        waktu = '$tanggal',
        masuk = '$nominal',
        keterangan = '$keterangan'
        ";
    } else {
        $sql = "insert into arus_kas set
        transaksi = 'Lain-lain',
        id_users = '$_SESSION[id_user]',
        waktu = '$tanggal',
        keluar = '$nominal',
        keterangan = '$keterangan'
        ";
    }
    mysql_query($sql);
    $result['status'] = TRUE;
    die(json_encode($result));
}

if ($method === 'delete_in_out_uang') {
    $id = $_GET['id'];
    mysql_query("delete from arus_kas where id = '$id'");
}

if ($method === 'save_billing') {
    session_start();
    $id_pasien  = $_POST['id_pasien'];
    $id_daftar  = $_POST['id_pendaftaran'];
    $pembayaran = currencyToNumber($_POST['pembayaran']);
    $total      = currencyToNumber($_POST['total_tagihan']);
    $nominal    = currencyToNumber($_POST['serahuang']);
    $id_bank    = ($_POST['bank'] !== '')?$_POST['bank']:'NULL';
    $nokartu    = $_POST['nokartu'];
    $cara_bayar = $_POST['cara_bayar'];
    $diskon     = $_POST['diskon'];
    $sql = "insert into pembayaran_billing set
        id_pendaftaran = '$id_daftar',
        tanggal = NOW(),
        waktu = NOW(),
        total = '$total',
        bayar = '$pembayaran',
        diskon = '$diskon',
        uang_serah = '$nominal',
        cara_bayar = '$cara_bayar',
        id_bank = $id_bank,
        no_kartu = '$nokartu'";
    mysql_query($sql);
    $id = mysql_insert_id();
    $upt = "update pendaftaran set is_bayar = '1' where id = '$id_daftar'";
    mysql_query($upt);
    
    $qyr = mysql_query("select p.id, p.total 
        FROM penjualan p 
        join resep r on (p.id_resep = r.id) 
        where r.id_pendaftaran = '$id_daftar'");
    while ($row = mysql_fetch_object($qyr)) {
        $query = "insert into arus_kas set 
            transaksi = 'Penjualan Resep',
            id_transaksi = '".$row->id."',
            id_users = '".$_SESSION['id_user']."',
            waktu = NOW(),
            masuk = '".$row->total."',
            diskon = '".($row->total*($diskon/100))."'";
        mysql_query($query);
    }
    
    $qwe = mysql_query("select t.id, t.nominal 
        from tindakan t
        join pemeriksaan pm on (t.id_pemeriksaan = pm.id_auto)
        join pendaftaran pdf on (pm.id_pendaftaran = pdf.id)
        where pm.id_pendaftaran = '$id_daftar'");
    while ($rows= mysql_fetch_object($qwe)) {
        $query = "insert into arus_kas set 
            transaksi = 'Penjualan Jasa',
            id_transaksi = '".$rows->id."',
            id_users = '".$_SESSION['id_user']."',
            waktu = NOW(),
            masuk = '".$rows->nominal."',
            diskon = '".($rows->nominal*($diskon/100))."'";
        mysql_query($query);
    }
    
    $result['status'] = TRUE;
    $result['id'] = $id;
    $result['id_pelanggan'] = $id_pasien;
    $result['id_pendaftaran'] = $id_daftar;
    die(json_encode($result));
}

if ($method === 'delete_billing') {
    $id =   $_GET['id'];
    mysql_query("delete from pembayaran_billing where id = '$id'"); 
}
?>