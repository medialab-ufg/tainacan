<?php
include_once(dirname(__FILE__) . '../../../../../models/object/object_model.php');
class FormItem extends Model{
     
    /**
     * gera as abas no formulario
     * @param type $collection_id
     */
     public function start($collection_id,$item_id,$properties) {
        $tabs = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization', true));
        $default_tab = get_post_meta($collection_id, 'socialdb_collection_default_tab', true);
        $allTabs = $this->sdb_get_post_meta_by_value($collection_id, 'socialdb_collection_tab');
        
        if ( (!$tabs || empty($tabs)) && !$default_tab && !$allTabs):
            ?>
            <?php
        else:
            ?>
            <input  type="hidden" 
                    name="tabs_properties" 
                    id="tabs_properties" 
                    value='<?php echo ($tabs && is_array($tabs)) ? json_encode($tabs) : ''; ?>'/>
            <!-- Abas para a Listagem dos metadados -->
            <ul id="tabs_item" class="nav nav-tabs" style="background: white">
                <li  role="presentation" class="active">
                    <a id="click-tab-default" href="#tab-default" aria-controls="tab-default" role="tab" data-toggle="tab">
                        <span  id="default-tab-title">
                        <?php echo (!$default_tab) ? _e('Default', 'tainacan') : $default_tab ?>
                        </span>
                    </a>
                </li>
                <?php 
                if($allTabs && is_array($allTabs)){
                    foreach ($allTabs as $tab) {
                        ?>
                        <li  role="presentation">
                            <a id="click-tab-<?php echo $tab->meta_id ?>" href="#tab-<?php echo $tab->meta_id ?>" aria-controls="tab-<?php echo $tab->meta_id ?>" role="tab" data-toggle="tab">
                                <span  id="<?php echo $tab->meta_id ?>-tab-title">
                                <?php echo $tab->meta_value ?>
                                </span>
                            </a>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            <div id="tab-content-metadata" class="tab-content" style="background: white;">
                <div id="tab-default"  class="tab-pane fade in active" style="background: white;margin-bottom: 15px;">
                    <div class="expand-all-div"  onclick="open_accordeon('default')" >
                        <a class="expand-all-link" href="javascript:void(0)">
                             <?php _e('Expand all', 'tainacan') ?>&nbsp;&nbsp;<span class="caret"></span></a>
                    </div>
                    <hr>
                    <div id="accordeon-default" class="multiple-items-accordion" style="margin-top:-20px;">
                          <?php $this->listPropertiesbyTab($properties) ?>
                    </div>
                </div>
                <?php 
                if($allTabs && is_array($allTabs)){
                    foreach ($allTabs as $tab) {
                        ?>
                        <div id="tab-<?php echo $tab->meta_id ?>"  class="tab-pane fade" style="background: white;margin-bottom: 15px;">
                            <div class="expand-all-div"  onclick="open_accordeon('<?php echo $tab->meta_id ?>')" >
                                <a class="expand-all-link" href="javascript:void(0)">
                                     <?php _e('Expand all', 'tainacan') ?>&nbsp;&nbsp;<span class="caret"></span></a>
                            </div>
                            <hr>
                            <div id="accordeon-<?php echo $tab->meta_id ?>" class="multiple-items-accordion" style="margin-top:-20px;">
                                <?php $this->listPropertiesbyTab($properties) ?>
                            </div>
                        </div>
                        <?php
                    }
                } ?>
            </div>    
        <?php
        endif;
        $this->initScripts();
    }
    
    
    public function listPropertiesbyTab($properties){
        echo '<pre>';
        var_dump($properties);
    }
    
    /**
     * scripts deste 
     */
    public function initScripts() {
        ?>
        <script>
            console.log(' -- Begin execution - Form item')
            $('.tabs').tab();
        </script>    
        <?php    
    }
}