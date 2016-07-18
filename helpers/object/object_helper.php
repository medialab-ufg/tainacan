<?php
/*
 * Object Controller's view helper 
 * */
class ObjectHelper extends ViewHelper {
    public $foo;
    
    public function getFoo() {
        return $this->foo = "bingo";
    }
    
    public function add_tabs() {
        $tabs = unserialize(get_post_meta($this->collection_id, 'socialdb_collection_update_tab_organization',true));
        $default_tab = get_post_meta($this->collection_id, 'socialdb_collection_default_tab', true);
        ?>
        <input  type="hidden" 
                name="tabs_properties" 
                id="tabs_properties" 
                value='<?php echo ($tabs&&is_array($tabs)) ? json_encode($tabs) :  ''; ?>'/>
        <!-- Abas para a Listagem dos metadados -->
        <ul id="tabs_item" class="nav nav-tabs" style="background: white">
            <li  role="presentation" class="active">
                <a id="click-tab-default" href="#tab-default" aria-controls="tab-default" role="tab" data-toggle="tab">
                    <span  id="default-tab-title">
                        <?php echo (!$default_tab) ? _e('Default', 'tainacan') : $default_tab ?>
                    </span>
                </a>
            </li>
        </ul>
        <div id="tab-content-metadata" class="tab-content" style="background: white">
            <div id="tab-default"  class="col-md-12 tab-pane fade in active" style="background: white">
                <div    style="margin-bottom:0%" 
                        onclick="open_accordeon('default')"
                        class="expand-all-item btn white tainacan-default-tags">
                    <div class="action-text" 
                         style="display: inline-block;">
                             <?php _e('Expand all', 'tainacan') ?></div>
                    &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
                </div>
                <div id="accordeon-default" class="multiple-items-accordion">
                </div>
            </div>
        </div>    
        <?php
    }
    
    
    //public function 
}