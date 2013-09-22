<?php
/* All tiles on the homepage are configured here, be sure to check the tutorials/docs on http://metro-webdesign.info */

/* GROUP 1 */

$tile[] = array("type"=>"img","group"=>0,"x"=>0,"y"=>0,'width'=>1,'height'=>1,"background"=>"#6950AB","url"=>"penjualan-nr.php",
	"img"=>"img/icons/penjualan.png","desc"=>"Penjualan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>1,"y"=>0,'width'=>1,'height'=>1,"background"=>"#009000","url"=>"pemesanan.php",
	"img"=>"img/icons/pemesanan.png","desc"=>"Pemesanan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>2,"y"=>2,'width'=>1,'height'=>1,"background"=>"#0072bc","url"=>"inkaso.php",
	"img"=>"img/icons/pemesanan.png","desc"=>"Inkaso","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>2,"y"=>1,'width'=>1,'height'=>1,"background"=>"#009fb0","url"=>"pemeriksaan.php",
	"img"=>"img/icons/pendaftaran.png","desc"=>"Pemeriksaan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>2,"y"=>0,'width'=>1,'height'=>1,"background"=>"#ff0084","url"=>"pendaftaran.php",
	"img"=>"img/icons/pendaftaran2.png","desc"=>"Pendaftaran Antrian","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

/*SLIDESHOW TILE - only in full version 
$tile[] = array("type"=>"slideshow","group"=>0,"x"=>0,"y"=>1,"width"=>1,"height"=>1,"background"=>"#6950ab","url"=>"welcome.php",
	"images"=>array("img/img1.png","img/img2.jpg","img/img3.jpg"),
	"effect"=>"slide-right","speed"=>5000,"arrows"=>true,
	"labelText"=>"Slideshow","labelColor"=>"#11528f","labelPosition"=>"bottom",
	"classes"=>"noClick");*/

$tile[] = array("type"=>"img","group"=>0,"x"=>1,"y"=>2,'width'=>1,'height'=>1,"background"=>"#614040","url"=>"penerimaan.php",
	"img"=>"img/icons/penerimaan.png","desc"=>"Penerimaan Produk","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>0,"y"=>2,'width'=>1,'height'=>1,"background"=>"#15aa64","url"=>"stok-opname.php",
	"img"=>"img/icons/resep.png","desc"=>"Stok Opname","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");


$tile[] = array("type"=>"img","group"=>0,"x"=>0,"y"=>1,'width'=>1,'height'=>1,"background"=>"#9a0f0f","url"=>"retur-penerimaan.php",
	"img"=>"img/icons/retur.png","desc"=>"Retur","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>1,"y"=>1,'width'=>1,'height'=>1,"background"=>"#d34927","url"=>"retur-penerimaan.php",
	"img"=>"img/icons/pemusnahan.png","desc"=>"Pemusnahan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>0,"x"=>3,"y"=>0,'width'=>1,'height'=>1,"background"=>"#b44528","url"=>"uang-in-out.php",
	"img"=>"img/icons/trans.png","desc"=>"Pemasukan / Pengeluaran Uang","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");


/* GROUP 2*/
/*<br />
SLIDEFX TILE -  only in full version 
$tile[] = array("type"=>"slidefx","group"=>1,"x"=>0,"y"=>0,'width'=>2,'height'=>1,"background"=>"#333","url"=>"external:img/metro_slide.png",
	"text"=>"Click to see in full","img"=>"img/metro_slide_300x150.png","classes"=>"lightbox"
);
*/

$tile[] = array("type"=>"img","group"=>1,"x"=>0,"y"=>0,'width'=>1,'height'=>1,"background"=>"#6950AB","url"=>"barang.php",
	"img"=>"img/icons/drug.png","desc"=>"Produk","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

//$tile[] = array("type"=>"img","group"=>1,"x"=>3,"y"=>1,'width'=>1,'height'=>1,"background"=>"#6950AB","url"=>"instansi.php",
//	"img"=>"img/icons/instansi.png","desc"=>"Instansi","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
//	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

//$tile[] = array("type"=>"slide","group"=>1,"x"=>0,"y"=>1,'width'=>2,'height'=>1,"background"=>"#00BFFF","url"=>"pabrik.php",
//	"text"=>"<b>Data Pabrik <br/>Manajemen data pabrik</b>","img"=>"img/pabrik.jpg","imgSize"=>1,
//	"slidePercent"=>0.40,
//	"slideDir"=>"up", // can be up, down, left or right
//	"doSlideText"=>true,"doSlideLabel"=>true,
//	"labelText"=>"Pabrik","labelColor"=>"#00BFFF","labelPosition"=>"top",
//);

$tile[] = array("type"=>"img","group"=>1,"x"=>0,"y"=>2,'width'=>1,'height'=>1,"background"=>"#6950AB","url"=>"pelanggan.php",
	"img"=>"img/icons/user.png","desc"=>"Pelanggan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>1,"x"=>1,"y"=>2,'width'=>1,'height'=>1,"background"=>"#eb6796","url"=>"golongan.php",
	"img"=>"img/icons/golongan.png","desc"=>"Golongan Harga","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");


//$tile[] = array("type"=>"img","group"=>1,"x"=>2,"y"=>0,'width'=>1,'height'=>1,"background"=>"#00709f","url"=>"supplier.php",
//	"img"=>"img/icons/supplier.png","desc"=>"Supplier","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
//	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

//$tile[] = array("type"=>"simple","group"=>1,"x"=>2,"y"=>1,'width'=>1,'height'=>1,"background"=>"#bd1e4a","url"=>"bank.php",
//"title"=>"Bank","text"=>"Manajemen data bank.
//");

$tile[] = array("type"=>"img","group"=>1,"x"=>1,"y"=>1,'width'=>1,'height'=>1,"background"=>"#e8641b","url"=>"farmakoterapi.php",
	"img"=>"img/icons/kategori-produk.png","desc"=>"Kategori Produk","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>1,"x"=>2,"y"=>1,'width'=>1,'height'=>1,"background"=>"#00709f","url"=>"penyakit.php",
	"img"=>"img/icons/farmakoterapi.png","desc"=>"Penyakit ICD X","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>1,"x"=>2,"y"=>0,'width'=>1,'height'=>1,"background"=>"#0072bc","url"=>"pabrik.php",
	"img"=>"img/icons/asuransi.png","desc"=>"Instansi-instansi","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>1,"x"=>1,"y"=>0,'width'=>1,'height'=>1,"background"=>"#be1e4a","url"=>"karyawan.php",
	"img"=>"img/icons/karyawan.png","desc"=>"Karyawan & Dokter","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>1,"x"=>0,"y"=>1,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"layanan.php",
	"img"=>"img/icons/layanan.png","desc"=>"Layanan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>1,"x"=>2,"y"=>2,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"user-account.php",
	"img"=>"img/icons/user.png","desc"=>"User Account","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

/*
SLIDESHOW TILE - only in full version
$tile[] = array("type"=>"slideshow","group"=>1,"x"=>2,"y"=>0,"width"=>1,"height"=>1,"background"=>"#6950ab","url"=>"newtab:http://google.com",
	"images"=>array("img/chars/a.png","img/chars/b.png","img/chars/c.png","img/chars/d.png","img/chars/e.png","img/chars/f.png","img/chars/g.png"),
	"effect"=>"slide-right, slide-left, slide-down, slide-up, flip-vertical, flip-horizontal, fade",
	"speed"=>1500,"arrows"=>false,
	"labelText"=>"Random fx","labelColor"=>"#453B5E","labelPosition"=>"top");
*/

/*FLIP TILE - only in full version
$tile[] = array("type"=>"flip","group"=>1,"x"=>2,"y"=>1,'width'=>1,'height'=>1,"background"=>"#C82345","url"=>"accordions.php","img"=>"img/metro_150x150.png",
	"text"=>"<h4 style='color:#FFF;'>Click for accordions!</h4>");
*/
	
/* GROUP 3 */
$tile[] = array("type"=>"img","group"=>2,"x"=>0,"y"=>0,'width'=>1,'height'=>1,"background"=>"#5f5f5f","url"=>"arus-stok.php",
	"img"=>"img/icons/chart.png","desc"=>"Arus Stok",
	"showDescAlways"=>true,"showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");
	
$tile[] = array("type"=>"img","group"=>2,"x"=>1,"y"=>0,'width'=>1,'height'=>1,"background"=>"#4577a4","url"=>"lap-sp.php",
	"img"=>"img/icons/lap-sp.png","desc"=>"Pemesanan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>2,"y"=>0,'width'=>1,'height'=>1,"background"=>"#175e88","url"=>"lap-penjualan.php",
	"img"=>"img/icons/penjualan.png","desc"=>"Penjualan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>0,"y"=>1,'width'=>1,'height'=>1,"background"=>"#3c17b7","url"=>"lap-resep.php",
	"img"=>"img/icons/lap-resep.png","desc"=>"Resep","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>1,"y"=>1,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"lap-penerimaan.php",
	"img"=>"img/icons/lap-resep.png","desc"=>"Penerimaan","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>2,"y"=>1,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"lap-hutang.php",
	"img"=>"img/icons/lap-resep.png","desc"=>"Hutang","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>0,"y"=>2,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"lap-arus-kas.php",
	"img"=>"img/icons/lap-resep.png","desc"=>"Arus Kas","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>1,"y"=>2,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"lap-statistik-obat.php",
	"img"=>"img/icons/lap-resep.png","desc"=>"Analisa Probabilitas","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>2,"y"=>2,'width'=>1,'height'=>1,"background"=>"#180052","url"=>"lap-analisis-abc.php",
	"img"=>"img/icons/lap-resep.png","desc"=>"Analisa ABC","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");

$tile[] = array("type"=>"img","group"=>2,"x"=>3,"y"=>0,'width'=>1,'height'=>1,"background"=>"#4c4344","url"=>"expired-date.php",
	"img"=>"img/icons/expiry.png","desc"=>"Expiry Date","showDescAlways"=>true,"imgWidth"=>1,"imgHeight"=>1,
	"labelText"=>"","labelColor"=>"#000","labelPosition"=>"","classes"=>"");


?> 