<?php

function form_input($name, $value = NULL, $attr = NULL) {
    return '<input type=text name="'.$name.'" value="'.$value.'" '.$attr.' />';
}

function form_password($name, $value = NULL, $attr = NULL) {
    return '<input type=password name="'.$name.'" value="'.$value.'" '.$attr.' />';
}

function form_hidden($name, $value = NULL, $attr = NULL) {
    return '<input type=hidden name="'.$name.'" value="'.$value.'" '.$attr.' />';
}

function form_textarea($name, $value = NULL, $attr = NULL) {
    return '<textarea name="'.$name.'" '.$attr.'>'.$value.'</textarea>';
}

function form_upload($name, $value = NULL, $attr = NULL) {
    return '<input type=file name="'.$name.'"  value="'.$value.'" '.$attr.' />';
}

function form_button($value, $attr) {
    return '<input type=button value="'.$value.'" '.$attr.' />';
}

function form_radio($name, $value, $id, $label = null, $checked = FALSE) {
    $attr = "";
    if ($checked == TRUE) {
        $attr = "checked";
    }
    return '<input type=radio name="'.$name.'" value="'.$value.'" id="'.$id.'" '.$attr.' /><label for="'.$id.'">'.$label.'</label>';
}

function form_checkbox($name, $value, $id, $label = null, $checked = FALSE) {
    $attr = "";
    if ($checked == TRUE) {
        $attr = "checked";
    }
    return '<input type=checkbox name="'.$name.'" value="'.$value.'" id="'.$id.'" '.$attr.' /><label for="'.$id.'">'.$label.'</label>';
}

function paging_ajax($jmldata, $dataPerPage, $klik, $tab = NULL, $search) {
    /*
     * Parameter '$search' dalam bentuk string , bisa json string atau yang lain
     * contoh 1#nama_barang#nama_pabrik
     */

    $showPage = NULL;
    ob_start();
    echo "
        <div class='body-page'>";
    if (!empty($klik)) {
        $noPage = $klik;
    } else {
        $noPage = 1;
    }

    $dataPerPage = $dataPerPage;


    $jumData = $jmldata;
    $jumPage = ceil($jumData / $dataPerPage);
    $get = $_GET;
    
    if ($jumData > $dataPerPage) {
        $onclick = null;
        if ($noPage > 1) {
            $get['page'] = ($noPage - 1);
            $onclick = $klik;
        }
        $prev = null;
        $last = ' class="last-block" ';
        if ($klik > 1) {
            $prev = "onClick=\"paging(" . ($klik - 1) . "," . $tab . ", '" . $search . "')\" ";
        }
        echo "<div class='page-prev' $prev>prev</div>";
        for ($page = 1; $page <= $jumPage; $page++) {
            if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)) {
                if (($showPage == 1) && ($page != 2))
                    echo "<div class='titik'>...</div>";
                if (($showPage != ($jumPage - 1)) && ($page == $jumPage))
                    echo "<div class='titik'>...</div>";
                if ($page == $noPage)
                    echo " <div class='noblock'>" . $page . "</div> ";
                else {
                    $get['page'] = $page;
                    if ($tab != NULL) {
                        $get['tab'] = $tab;
                    }
                    $next = "onClick=\"paging(" . $page . "," . $tab . ", '" . $search . "')\" ";
                    //echo " <a class='block' href='?" . generate_get_parameter($get) . "'>" . $page . "</a> ";
                    if ($page == $jumPage) {
                        echo '<div  class="block" ' . $next . '>' . $page . '</div>';
                    } else {
                        echo '<div class="block" ' . $next . '>' . $page . '</div>';
                    }
                }
                $showPage = $page;
            }
        }
        $next = null;
        if ($klik < $jumPage) {
            $next = "onClick=\"paging(" . ($klik + 1) . "," . $tab . ", '" . $search . "')\" ";
        }
        echo "<div class='page-next' $next >next</div>";
    }
    echo "</div>";

    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
}

function generate_get_parameter($get, $addArr = array(), $removeArr = array()) {
    if ($addArr == null)
        $addArr = array();
    foreach ($removeArr as $rm) {
        unset($get[$rm]);
    }
    $link = "";
    $get = array_merge($get, $addArr);
    foreach ($get as $key => $val) {
        if ($link == null) {
            $link.="$key=$val";
        }else
            $link.="&$key=$val";
    }
    return $link;
}

function date2mysql($tgl) {
    $tgl = explode("/", $tgl);
    if (empty($tgl[2]))
        return "";
    $news = "$tgl[2]-$tgl[1]-$tgl[0]";
    return $news;
}

function datefmysql($tgl) {
    if ($tgl == '' || $tgl == null) {
        return "";
    } else {
        $tgl = explode("-", $tgl);
        $new = $tgl[2] . "/" . $tgl[1] . "/" . $tgl[0];
        return $new;
    }
}

function datetimefmysql($dt, $time = NULL) {
    if ($dt !== NULL) {
        $var = explode(" ", $dt);
        $var1 = explode("-", $var[0]);
        $var2 = "$var1[2]/$var1[1]/$var1[0]";
        if ($time != NULL) {
            return $var2 . ' ' . $var[1];
        } else {
            return $var2;
        }
    } else {
        return '';
    }
}

function datetime2mysql($dt) {
    $var = explode(" ", $dt);
    $var1 = explode("/", $var[0]);
    $var2 = "$var1[2]-$var1[1]-$var1[0]";
    return $var2 . ' ' . $var[1];
}

function rupiah($jml) {
    $int = number_format($jml, 0, '', '.');
    return $int;
}

function currencyToNumber($a) {
    return str_ireplace(".", "", $a);
}

function get_last_pemesanan() {
    $sql = mysql_query("select substr(id, 4,3) as id  from pemesanan order by tanggal desc limit 1");
    $row = mysql_fetch_object($sql);
    if (!isset($row->id)) {
        $result = "SP.001/".date("m/Y");
    } else {
        $result = "SP.".str_pad((string)($row->id+1), 3, "0", STR_PAD_LEFT)."/".date("m/Y");
    }
    return $result;
}

function indo_tgl($tgl) {
    $baru = explode("-", $tgl);
    if ($baru[1] == '01')
        $mo = "Januari";
    if ($baru[1] == '02')
        $mo = "Februari";
    if ($baru[1] == '03')
        $mo = "Maret";
    if ($baru[1] == '04')
        $mo = "April";
    if ($baru[1] == '05')
        $mo = "Mei";
    if ($baru[1] == '06')
        $mo = "Juni";
    if ($baru[1] == '07')
        $mo = "Juli";
    if ($baru[1] == '08')
        $mo = "Agustus";
    if ($baru[1] == '09')
        $mo = "September";
    if ($baru[1] == '10')
        $mo = "Oktober";
    if ($baru[1] == '11')
        $mo = "November";
    if ($baru[1] == '12')
        $mo = "Desember";
    $new = "$baru[2] $mo $baru[0]";

    return $new;
}

function get_list_bulan() {
    return array(
        array('01' => 'Januari')
    );
}

function hitungUmur($tgl) {
    $tanggal = explode("-", $tgl);
    $tahun = $tanggal[0];
    $bulan = $tanggal[1];
    $hari = $tanggal[2];

    $day = date('d');
    $month = date('m');
    $year = date('Y');

    $tahun = $year - $tahun;
    $bulan = $month - $bulan;
    $hari = $day - $hari;

    $jumlahHari = 0;
    $bulanTemp = ($month == 1) ? 12 : $month - 1;
    if ($bulanTemp == 1 || $bulanTemp == 3 || $bulanTemp == 5 || $bulanTemp == 7 || $bulanTemp == 8 || $bulanTemp == 10 || $bulanTemp == 12) {
        $jumlahHari = 31;
    } else if ($bulanTemp == 2) {
        if ($tahun % 4 == 0)
            $jumlahHari = 29;
        else
            $jumlahHari = 28;
    }else {
        $jumlahHari = 30;
    }

    if ($hari <= 0) {
        $hari+=$jumlahHari;
        $bulan--;
    }
    if ($bulan < 0 || ($bulan == 0 && $tahun != 0)) {
        $bulan+=12;
        $tahun--;
    }
    if ($tgl !== '0000-00-00') {
        return $tahun . " Tahun " . $bulan . " Bulan " . $hari . " Hari";
    } else {
        return '-';
    }
}
?>