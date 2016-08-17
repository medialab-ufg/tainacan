<?php
include_once ('../../helpers/view_helper.php');
include_once ('../../helpers/mapping/mapping_helper.php');
$helper = new MappingHelper;
?>
<div class="col-md-12">
    <br>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?php
    $helper->generate_mapping_table($tainacan_properties, $generic_properties);
    ?>
</div>