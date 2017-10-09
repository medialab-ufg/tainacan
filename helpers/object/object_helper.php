<?php
/*
 * Object Controller's view helper 
 * */

class ObjectHelper extends ViewHelper {

    public function add_tabs($collection_id = false) {
        if(!$collection_id)
            $collection_id = $this->collection_id;
        $tabs = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization', true));
        $default_tab = get_post_meta($collection_id, 'socialdb_collection_default_tab', true);
        if (!$tabs || empty($tabs) && !$default_tab):
            _t('Expand all', 1);
        else: ?>
            <input type="hidden" name="tabs_properties" id="tabs_properties"
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
                <div id="tab-default"  class="tab-pane fade in active" style="background: white;margin-bottom: 15px;">
                    <div class="expand-all-div"  onclick="open_accordeon('default')" >
                        <a class="expand-all-link" href="javascript:void(0)">
                             <?php _e('Expand all', 'tainacan') ?>&nbsp;&nbsp;<span class="caret"></span></a>
                    </div>
                    <hr>
                    <div id="accordeon-default" class="multiple-items-accordion" style="margin-top:-20px;"></div>
                </div>
            </div>    
        <?php
        endif;
    }

    public function setValidation($collection_id,$id,$slug) {
        $required = get_post_meta($collection_id, 'socialdb_collection_property_'.$id.'_required', true);
        if($required&&$required=='true'): ?>
            <a id='required_field_<?php echo $slug; ?>' class="pull-right validade-meta-field validade-error">
                <span class="glyphicon glyphicon-remove"  title="<?php _t('This metadata is required!',1)?>"
                      data-toggle="tooltip" data-placement="top" ></span>
            </a>
            <a id='ok_field_<?php echo $slug; ?>' class="pull-right validate-meta-field validate-ok" style="display: none;">
                <span class="glyphicon glyphicon-ok" title="<?php _t('Field filled successfully!',1)?>"
                      data-toggle="tooltip" data-placement="top"></span>
            </a>
            <input type="hidden" id='core_validation_<?php echo $slug ?>' class='core_validation' value='false'>
            <input type="hidden" id='core_validation_<?php echo $slug ?>_message'
                   value='<?php echo sprintf(__('The field license is required', 'tainacan'), $slug); ?>'>
            <input type="hidden" id='fixed_id_<?php echo $slug ?>' value='<?php echo $id; ?>'>
        <?php
        endif;
    }

    public function renderCollectionPagination($_total_items_, $items_per_page, $page_id, $proper_str, $extra_class,$loop = false) {
        if( $_total_items_> $items_per_page ) {
            if($loop && $page_id == $loop->max_num_pages){
                $_num_pages_ = $loop->max_num_pages;
            }else{
                $_num_pages_ = ceil($_total_items_ / $items_per_page);
            }
            $limit = (!isset($page_id) ||$page_id=='1') ? $items_per_page : (($page_id - 1)  * $items_per_page) + $items_per_page
            ?>
            <!-- TAINACAN: div com a paginacao da listagem -->
            <div class="col-md-12 center_pagination <?php echo $extra_class; ?>">
                               
                <input type="hidden" id="number_pages" name="number_pages" value="<?php echo $_num_pages_; ?>">
                <div class="pagination_items col-md-4 pull-left">
                    <a href="javascript:void(0)" class="btn btn-default btn-sm first" data-action="first"><span class="glyphicon glyphicon-backward"></span><!--&laquo;--></a>
                    <a href="javascript:void(0)" class="btn btn-default btn-sm previous" data-action="previous"><span class="glyphicon glyphicon-step-backward"></span><!--&lsaquo;--></a>
                    <input type="text" readonly="readonly" data-max-page="0" class="pagination-count"
                           data-current-page="<?php if (isset($page_id)) echo $page_id; ?>" />
                    <a href="javascript:void(0)" class="btn btn-default btn-sm next" data-action="next"><span class="glyphicon glyphicon-step-forward"></span><!--&rsaquo;--></a>
                    <a href="javascript:void(0)" class="btn btn-default btn-sm last" data-action="last"><span class="glyphicon glyphicon-forward"></span><!--   &raquo; --></a>
                </div>

                <div class="col-md-4 center">
                    <?php echo $proper_str; ?> <span class="base-page-init"> <?php echo (!isset($page_id) ||$page_id=='1') ? 1 : ( ($page_id - 1) * $items_per_page)  + 1 ; ?>  </span> -
                    <span class='per-page'> <?php echo ($limit > $_total_items_) ? $_total_items_ : $limit ;?> </span>
                    <?php _t(' of ', 1); ?> <span class="col-total-items"> <?php echo $_total_items_; ?> </span>
                </div>

                <div class="col-md-3 pull-right per_page">
                    <?php _t('Items per page:', 1); ?>
                    <select name="items-per-page" class="col-items-per-page">
                       <?php $this->getItemsPerPage(); ?>
                    </select>
                </div>

            </div>
        <?php }
    }
    
    private function getItemsPerPage() {
        // By default, let 10 items selected
        $_show_values = [5,8,10,15,25,50];
        foreach ($_show_values as $k => $vl) {
            if($k == 2) {
                $select = 'selected';
            } else {
                $select = "";
            }
            echo "<option value='$vl' $select> $vl </option>";
        }
    }

    public static function getTableViewData($table_metas, $curr_post, $item_id, $fixed_metas) {
        if(is_array($table_metas) && count($table_metas) > 0) {

            $_item_title_ = get_the_title();
            $_trim_desc = get_the_content();

            $_DEFAULT_EMPTY_VALUE = "--";
            foreach ($table_metas as $item_meta_info):
                $fmt = str_replace("\\", "", $item_meta_info);
                if (is_string($fmt)):
                    $_meta_obj = json_decode($fmt);
                    if (is_object($_meta_obj)):
                        $_META = ['id' => $_meta_obj->id, 'tipo' => $_meta_obj->tipo];
                        if ($curr_post === 0)
                            echo '<input type="hidden" name="meta_id_table" value="' . $_META['id'] . '" data-mtype="' . $_META['tipo'] . '">';

                        if ($_META['tipo'] === 'property_data') {
                            $meta_type = get_term_meta($_META['id'], 'socialdb_property_data_widget', true);
                            $check_fixed = get_term($_meta_obj->id);
                            $_out_ = $_DEFAULT_EMPTY_VALUE;

                            if (in_array($check_fixed->slug, $fixed_metas)) {
                                $_slug = $check_fixed->slug;
                                $base = str_replace("socialdb_property_fixed_", "", $_slug);
                                switch ($base) {
                                    case "title":
                                        $_out_ = $_item_title_;
                                        break;
                                    case "description":
                                        $_out_ = $_trim_desc;
                                        break;
                                    case "source":
                                        $_out_ = get_post_meta($item_id, "socialdb_object_dc_source")[0];
                                        break;
                                    case "type":
                                        $_out_ = get_post_meta($item_id, "socialdb_object_dc_type")[0];
                                        break;
                                    case "license":
                                        $_out_ = get_post_meta($item_id, "socialdb_license_id")[0];
                                        break;
                                }
                            } else {
                                $__item_meta = get_post_meta($item_id, "socialdb_property_$_meta_obj->id", true) ?: $_DEFAULT_EMPTY_VALUE;
                                if (!empty($__item_meta)) {
                                    $_out_ = $__item_meta;
                                }
                            }

                            if ($meta_type == 'date') {
                                $date_temp = explode('-', $_out_);
                                if (count($date_temp) > 1):
                                    $_out_ = $date_temp[2] . '/' . $date_temp[1] . '/' . $date_temp[0];
                                endif;

                                $data = get_post_meta($item_id, "socialdb_property_{$_meta_obj->id}_0_date", true);
                                if (is_plugin_active('data_aacr2/data_aacr2.php') && $data){
                                    $_out_ = $data;
                                }
                            }

                            if($meta_type == 'user') {
                                $user = get_user_by("id", $_out_);
                                $_out_ = $user->data->display_name;
                            }

                            echo '<input type="hidden" name="item_table_meta" value="' . $_out_ . '" />';
                        } else if ($_META['tipo'] === 'property_term') {
                            $_current_object_terms_ = get_the_terms($item_id, "socialdb_category_type");
                            $_father_name = get_term($_META['id'])->name;
                            $_father_category_id = (int) get_term_meta($_META['id'])['socialdb_property_term_root'][0];
                            $_item_meta_val = $_DEFAULT_EMPTY_VALUE;

                            foreach ($_current_object_terms_ as $curr_term) {
                                if ($curr_term->parent == $_father_category_id) {
                                    $_item_meta_val = $curr_term->name;
                                }
                            }
                            ?>
                            <input id="tableV-meta-<?= $_META['id']; ?>" type="hidden" name="item_table_meta"
                                   data-parent="<?= $_father_name ?>" value="<?= $_item_meta_val; ?>" />
                            <?php
                        } else if ($_META['tipo'] == 'property_object') {
                            $_prop_key = "socialdb_property_" . (string) $_META['id'];
                            $_related_obj_id =  get_post_meta($item_id, $_prop_key);
                            $_father_name = get_term($_META['id'])->name;
                            $_item_meta_val = $_DEFAULT_EMPTY_VALUE;
                            $values = [];
                            if (count($_related_obj_id) > 0) {
                                foreach ($_related_obj_id as $value) {
                                    $_obj_id = get_post($value)->ID;
                                    if ($_obj_id != $item_id) {
                                        $values[] = get_post($_obj_id)->post_title;
                                    }
                                }
                            }
                            ?>
                            <input id="tableV-meta-<?= $_META['id']; ?>" type="hidden" name="item_table_meta"
                                   data-parent="<?= $_father_name ?>" value="<?= (count($values)>0) ? implode('<br>', $values) : $_item_meta_val; ?>" />
                            <?php
                        }
                    endif;
                endif;
            endforeach;
        }
    }

}
