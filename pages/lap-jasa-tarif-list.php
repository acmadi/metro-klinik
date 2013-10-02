<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';

?>
<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="20%">Nama Nakes</th>
    <th width="20%">Nama Pasien</th>
    <th width="20%">Nama Tindakan</th>
    <th width="5%">Nominal Rp.</th>
</tr>
</thead>

<tbody>
    <?php
    $param = array(
        'awal' => date2mysql($_GET['awal']),
        'akhir' => date2mysql($_GET['akhir']),
        'nakes' => $_GET['nakes']
    );
    
    $list_data = laporan_jasa_pelayanan_load_data($param);
    ?>
</tbody>
</table>