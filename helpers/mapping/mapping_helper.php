<?php
/*
 * Mapping Controller's view helper 
 * */
class MappingHelper extends ViewHelper {
    
    /**
     * 
     * @param array $tainacan_properties
     * @param array $generic_properties
     */
    public function generate_mapping_table($tainacan_properties,$generic_properties) {
        include_once ( dirname(__FILE__).'/../../views/mapping/js/mapping_table_js.php');
        ?>
        <style>
            .headers-mapping{
                border-style: solid;
                border-color: #595959;
                border-width: 1px;
            }
            .tainacan-properties-li{
                color: white;
                padding-left: 5px;
                background-color: #3366FF;
                border-style: solid;
                border-color: #A6A6A6;
                border-width: 1px;
            }
            .generic-properties-li{
                color: white;
                padding-left: 5px;
                background-color: #008000;
                border-style: solid;
                border-color: #A6A6A6;
                border-width: 1px;
            }
            .border-table-mapping{
                border-style: solid;
                min-height: 30px;
                border-color: #A6A6A6;
                border-width: 1px;
                border-top-style: none;
            }
        </style>
        <div class="col-md-12" >
            <div class="col-md-3 headers-mapping"><?php _e('Collection','tainacan') ?></div>
            <div class="col-md-3 headers-mapping"><?php _e('Map >>','tainacan') ?></div>
            <div class="col-md-3 headers-mapping"><?php _e('Map <<','tainacan') ?></div>
            <div class="col-md-3 headers-mapping"><?php _e('URL','tainacan') ?></div>
        </div>    
        <div class="col-md-12" >   
            <ul class="col-md-3 connected-tainacan no-padding border-table-mapping" 
                style="padding-bottom: 5px;"
                id="tainacan-properties-ul"><?php $this->list_tainacan($tainacan_properties) ?></ul>
            <ul class="col-md-3 connected-tainacan no-padding border-table-mapping" 
                style="padding-bottom: 5px;"
                id="tainacan-mapped-ul"></ul>
            <ul class="col-md-3 connected-generic no-padding border-table-mapping" 
                style="padding-bottom: 5px;"
                id="generic-mapped-ul"><?php $this->list_properties($generic_properties) ?>
            </ul>
            <ul class="col-md-3 connected-generic no-padding border-table-mapping" 
                style="padding-bottom: 5px;"
                id="generic-properties-ul">
            </ul>
        </div>
        <?php
    }
    
    /**
     * 
     * @param type $tainacan_properties
     */
    public function list_tainacan($tainacan_properties) {
        foreach ($tainacan_properties as $property) {
            ?>
            <li class="tainacan-properties-li"><?php echo $property['name'] ?></li>
            <?php
        }
    }
    
    /**
     * 
     * @param type $tainacan_properties
     */
    public function list_properties($generic_properties) {
        foreach ($generic_properties as $property) {
            ?>
            <li class="generic-properties-li"><?php echo $property['name_inside_tag'] ?></li>
            <?php
        }
    }
}