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
        $tabs = unserialize(get_post_meta($this->collection_id, 'socialdb_collection_update_tab_organization', true));
        $default_tab = get_post_meta($this->collection_id, 'socialdb_collection_default_tab', true);
        if (!$tabs || empty($tabs) && !$default_tab):
            ?>
            <div    style="<?php echo ($this->hide_main_container) ? 'margin-bottom:0%' : '' ?>" 
                    class="expand-all-item btn white tainacan-default-tags">
                <div class="action-text" 
                     style="display: inline-block;">
            <?php _e('Expand all', 'tainacan') ?></div>
                &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
            </div>   
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
            </ul>
            <div id="tab-content-metadata" class="tab-content" style="background: white;">
                <div id="tab-default"  class="tab-pane fade in active" style="background: white;margin-bottom: 15px;margin-top: 15px;">
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
        endif;
    }

    public function setValidation($collection_id,$id,$slug) {
        $required = get_post_meta($collection_id, 'socialdb_collection_property_'.$id.'_required', true);
        if($required&&$required=='true'):
        ?>
        <a id='required_field_<?php echo $slug ?>' style="padding: 3px;margin-left: -30px;" >
            &nbsp; <span class="glyphicon glyphicon glyphicon-star" title="<?php echo __('This metadata is required!', 'tainacan') ?>" 
                         data-toggle="tooltip" data-placement="top" ></span>
        </a>
        <a id='ok_field_<?php echo $slug ?>'  style="display: none;padding: 3px;margin-left: -30px;" >
            &nbsp; <span class="glyphicon glyphicon-ok-circle" title="<?php echo __('Field filled successfully!', 'tainacan') ?>" 
                         data-toggle="tooltip" data-placement="top" ></span>
        </a>
        <input type="hidden" 
               id='core_validation_<?php echo $slug ?>' 
               class='core_validation' 
               value='false'>
        <input type="hidden" 
               id='core_validation_<?php echo $slug ?>_message'  
               value='<?php echo sprintf(__('The field license is required', 'tainacan'), $slug); ?>'>
        <?php
        endif;
    }

    //public function 
}
