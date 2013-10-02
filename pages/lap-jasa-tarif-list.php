<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';

?>
<table cellspacing="0" width="100%" class="list-data">
<thead>
<tr class="italic">
    <th width="3%">No.</th>
    <th width="5%">No. SIP</th>
    <th width="20%">Nama Nakes</th>
    <th width="5%">No. RM</th>
    <th width="20%">Nama Pasien</th>
    <th width="30%">Nama Tindakan</th>
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
    $no = 1;
    $dokter = "";
    $total = 0;
    $list_data = laporan_jasa_pelayanan_load_data($param);
    foreach ($list_data as $key => $data) { ?>
    <tr class="<?= ($key%2==0)?'even':'odd' ?>">
        <td align="center"><?= ($dokter !== $data->id_nakes)?$no:NULL ?></td>
        <td align="center"><?= ($dokter !== $data->id_nakes)?$data->no_sip:NULL ?></td>
        <td><?= ($dokter !== $data->id_nakes)?$data->nama:NULL ?></td>
        <td align="center"><?= $data->no_rm ?></td>
        <td><?= $data->pasien ?></td>
        <td><?= $data->tarif ?></td>
        <td align="right"><?= rupiah($data->nominal) ?></td>
    </tr>
    <?php 
    if ($dokter !== $data->id_nakes) {
        $no++;
    }
    $dokter = $data->id_nakes;
    $total = $total+$data->nominal;
    } ?>
    
</tbody>
<tfoot>
    <tr>
        <td colspan="6" align="right"><b>TOTAL</b></td>
        <td align="right"><?= rupiah($total) ?></td>
    </tr>
</tfoot>
</table>