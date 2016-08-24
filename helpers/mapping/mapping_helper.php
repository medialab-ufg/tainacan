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
            /* cabecalhos */
            .headers-mapping{
                border-style: solid;
                border-color: #595959;
                border-width: 1px;
            }
            /* Para os tipos tainacan - li */
            .tainacan-properties-li{
                color: white;
                cursor: move;
                height: 80px;
                padding-left: 5px;
                padding-bottom: 20px;
                padding-top: 20px;
                padding-right: 20px;
                background-color: #3366FF;
                border-style: solid;
                border-color: #A6A6A6;
                border-width: 1px;
            }
            /* Para os tipos de criacao - li */
            .tainacan-create-properties-li{
                padding-left: 5px;
                padding-right: 10px;
                padding-top: 10px;
                padding-bottom: 10px;
                height: 80px;
                border-style: solid;
                border-color: #A6A6A6;
                border-width: 1px;
            }
            /* Para os tipos genericos - li */
            .generic-properties-li{
                cursor: move;
                color: white;
                height: 80px;
                padding-left: 5px;
                padding-bottom: 20px;
                padding-top: 20px;
                padding-right: 20px;
                background-color: #008000;
                border-style: solid;
                border-color: #A6A6A6;
                border-width: 1px;
            }
            /* a borda para os ul */
            .border-table-mapping{
                border-style: solid;
                border-color: #A6A6A6;
                border-width: 1px;
                border-top-style: none;
            }
            /* style dos inputs bootstrap */
            .style-input {
                padding: 5px;
                border: 1px solid #ccc;
                border-radius: 4px; 
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
                id="tainacan-properties-ul"><?php $this->list_tainacan($tainacan_properties) ?></ul>
            <ul class="col-md-3 connected-tainacan no-padding border-table-mapping" 
                style="overflow-y: hidden;"
                id="tainacan-mapped-ul"><?php $this->list_new_properties(count($generic_properties)) ?>
            </ul>
            <ul class="col-md-3 connected-generic no-padding border-table-mapping" 
                id="generic-mapped-ul"><?php $this->list_properties($generic_properties) ?>
            </ul>
            <ul class="col-md-3 connected-generic no-padding border-table-mapping" 
                id="generic-properties-ul">
            </ul>
            <input type="hidden" id="count_found_properties" name="count_found_properties" value="<?php echo count($generic_properties); ?>">
            <input type="hidden" id="mapped_tainacan_properties" name="mapped_tainacan_properties" value="">
            <input type="hidden" id="mapped_generic_properties" name="mapped_generic_properties" value="">
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
            <li id="<?php echo $property['value'] ?>" class="tainacan-properties-li"><?php echo $property['name'] ?></li>
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
            <li id="<?php echo $property['name_field'] ?>" 
                class="generic-properties-li"><?php echo $property['name_field'] ?></li>
            <?php
        }
    }
    /**
     * 
     * @param type $tainacan_properties
     */
    public function list_new_properties($limit) {
        for($i = 0;$i<$limit;$i++) {
            ?>
            <li  id="new_<?php echo $i ?>" class="tainacan-create-properties-li">
                <div class="col-md-12 no-padding">
                    <input type="checkbox" 
                           class="col-md-1 no-padding"
                           onchange="set_name_mapped(this,<?php echo $i ?>)"
                           id="create_property_<?php echo $i ?>" 
                           name="create_property_<?php echo $i ?>" 
                           value="true">
                    <div class="col-md-1 no-padding" ></div>      
                    <input type="text" 
                           class="col-md-10 no-padding style-input"
                           id="name_property_<?php echo $i ?>" 
                           name="name_property_<?php echo $i ?>" 
                           placeholder="<?php _e('Create','tainacan') ?>">
                </div>
                <select name="widget_property_<?php echo $i ?>" class="form-control" style="margin-top: 33px;">
                    <option value="text"><?php _e('Text','tainacan') ?></option>
                    <option value="numeric"><?php _e('Numeric','tainacan') ?></option>
                    <option value="textarea"><?php _e('Textarea','tainacan') ?></option>
                    <option value="date"><?php _e('Date','tainacan') ?></option>
                </select>    
            </li>
            <?php
        }
    }
}