<?php
include_once '../models/transaksi.php';
include_once '../inc/functions.php';
?>
<script type="text/javascript">
$(function() {
    $( document ).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });
    /*$('.detail').on('mouseover',function() {
        $.ajax({
            url: 'pages/tooltip-detail-transaksi.php',
            data: 'id='+$(this).attr('id'),
            cache: false,
            success: function(msg) {
                $('.list-data tbody tr.detail').attr('title',msg);
                $( document ).tooltip();
            }
        });
    });*/
});
</script>
<table cellspacing="0" width="100%" class="list-data">
<thead>
    <tr class="italic">
        <th width="3%">No.</th>
        <th width="5%">Kode</th>
        <th width="5%">Tanggal</th>
        <th width="10%">Pasien</th>
        <th width="15%">Dokter</th>
        <th width="15%">Anamnesis</th>
        <th width="15%">Diagnosis</th>
        <th width="15%">Tindakan</th>
        <th width="5%">#</th>
    </tr>
</thead>
<tbody>
    <?php
    $limit = 10;
    $page  = $_GET['page'];
    if ($_GET['page'] === '') {
        $page = 1;
        $offset = 0;
    } else {
        $offset = ($page-1)*$limit;
    }
    
    $param = array(
        'id' => $_GET['id_pemeriksaan'],
        'limit' => $limit,
        'start' => $offset,
        'search' => $_GET['search']
    );
    $pemeriksaan = pemeriksaan_load_data($param);
    $list_data = $pemeriksaan['data'];
    $total_data= $pemeriksaan['total'];
    $id = "";
    $no = 1;
    $tindakan = "";
    foreach ($list_data as $key => $data) { 
        
        ?>
        <tr valign="top" id="<?= $data->id ?>" class="detail <?= ($id !== $data->id)?'odd':NULL ?>">
            <td align="center"><?= ($id !== $data->id)?($no+$offset):NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?$data->id:NULL ?></td>
            <td align="center"><?= ($id !== $data->id)?datefmysql($data->tanggal):NULL ?></td>
            <td title="<img src='img/pemeriksaan/<?= $data->foto ?>' width='200px' />"><?= ($id !== $data->id)?$data->pasien:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->dokter:NULL ?></td>
            <td><?= ($id !== $data->id)?$data->anamnesis:NULL ?></td>
            <td><?= $data->topik ?></td>
            <td><?= ($tindakan !== $data->id_tarif)?$data->tarif:NULL ?></td>
            <td class='aksi' align='center'>
                <!--<a class='edition' onclick="edit_pemeriksaan('<?= $str ?>');" title="Klik untuk edit">&nbsp;</a>-->
                <?php if ($id !== $data->id) { ?>
                    <a class='deletion' onclick="delete_pemeriksaan('<?= $data->id ?>','<?= $page ?>');" title="Klik untuk hapus">&nbsp;</a>
                <?php } ?>
            </td>
        </tr>
    <?php 
    if ($id !== $data->id) {
        $no++;
    }
    $tindakan = $data->id_tarif;
    $id = $data->id;
    }
    ?>
</tbody>
</table>
<?= paging_ajax($total_data, $limit, $page, '1', $_GET['search']) ?>