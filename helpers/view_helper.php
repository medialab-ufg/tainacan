<?php

class ViewHelper {

    public $metadata_types;
    public $default_metadata;
    public $special_metadata;
    public $hide_main_container = false;
    public $collection_id;
    public static $fixed_slugs = [
        'socialdb_property_fixed_title',
        'socialdb_property_fixed_description',
        'socialdb_property_fixed_content',
        'socialdb_property_fixed_source',
        'socialdb_property_fixed_license',
        'socialdb_property_fixed_thumbnail',
        'socialdb_property_fixed_attachments',
        'socialdb_property_fixed_tags',
        'socialdb_property_fixed_type'
        ];
   public $terms_fixed;
    
   public static $default_color_schemes = [
        'blue'   => ['#7AA7CF', '#0C698B'],
        'brown'  => ['#874A1D', '#4D311F'],
        'green'  => ['#3D8B55', '#242D11'],
        'violet' => ['#7852B2', '#31185C'],
        'grey'   => ['#58595B', '#231F20'],
    ];
    
    function __construct($collection_id = 0) {
        $this->terms_fixed = [
        'title'=> get_term_by('slug', 'socialdb_property_fixed_title','socialdb_property_type'),
        'description'=> get_term_by('slug', 'socialdb_property_fixed_description','socialdb_property_type'),
        'content'=> get_term_by('slug', 'socialdb_property_fixed_content','socialdb_property_type'),
        'source'=> get_term_by('slug', 'socialdb_property_fixed_source','socialdb_property_type'),
        'license'=> get_term_by('slug', 'socialdb_property_fixed_license','socialdb_property_type'),
        'thumbnail'=> get_term_by('slug', 'socialdb_property_fixed_thumbnail','socialdb_property_type'),
        'attachments'=> get_term_by('slug', 'socialdb_property_fixed_attachments','socialdb_property_type'),
        'tags'=> get_term_by('slug', 'socialdb_property_fixed_tags','socialdb_property_type'),
        'type'=> get_term_by('slug', 'socialdb_property_fixed_type','socialdb_property_type')
        ];
        $this->collection_id = $collection_id;
        //verifico se existe labels da colecao
        if($this->collection_id):
           $this->get_labels_fixed_properties($this->collection_id); 
        endif;
        //visibilidade dos metadados
        if($this->get_visibility($this->terms_fixed['attachments'], $this->collection_id)!==''
                &&$this->get_visibility($this->terms_fixed['title'], $this->collection_id)!==''
                &&$this->get_visibility($this->terms_fixed['type'], $this->collection_id)!==''
                &&$this->get_visibility($this->terms_fixed['content'], $this->collection_id)!==''
                ){
             $this->hide_main_container = true;
        }
    }
    
    public function get_metadata_types() {
        return $this->metadata_types = [
            'text' => __('Text', 'tainacan'),
            'textarea' => __('Long text', 'tainacan'),
            'date' => __('Date', 'tainacan'),
            'numeric' => __('Numeric', 'tainacan'),
            'autoincrement' => __('Auto-Increment', 'tainacan'),
            'relationship' => __('Relationship', 'tainacan'),
            'category' => __('Category', 'tainacan'),
            'voting' => __('Rankings', 'tainacan'),
            'compounds' => __('Compounds', 'tainacan'),
        ];
    }

    public function get_property_data_types() {
        return $this->metadata_types = ['text' => __('Text', 'tainacan'),
            'textarea' => __('Long text', 'tainacan'),
            'date' => __('Date', 'tainacan'),
            'numeric' => __('Numeric', 'tainacan'),
            'autoincrement' => __('Auto-Increment', 'tainacan')];
    }

    public function get_default_metadata() {
        return $this->default_metadata = [
            //'socialdb_object_dc_type' => 'Type',
            //'socialdb_object_from' => 'Format',
//            'item_name'  => 'Item Title',
//            'thumbnail_id' => 'Item Thumbnail',
//            'post_content' => 'Item Description',
//            'socialdb_object_dc_source' => 'Source',
//            'socialdb_license_id' => 'License Type'
        ];
    }
    
    public function get_visibility( $property) {
        if(isset($property->term_id)){
            $visibility = get_term_meta($property->term_id, 'socialdb_property_visibility',true);
            if($visibility=='hide'){
                return 'style="display:none"';
            }elseif($this->collection_id!=0){
                $meta = get_post_meta($this->collection_id, 'socialdb_collection_fixed_properties_visibility', true);
                $array = explode(',', $meta) ;
                if(is_array($array) &&  ($key = array_search($property->term_id, $array)) !== false):
                    return 'style="display:none"';
                endif;
            }else{
              return '';   
            } 
        }
        return '';
    }
    
    public function get_labels_fixed_properties($collection_id){
        $labels_collection = ($collection_id!='') ? get_post_meta($collection_id, 'socialdb_collection_fixed_properties_labels', true) : false;
        foreach ($this->terms_fixed as $slug => $value) {
            if($labels_collection):
                $array = unserialize($labels_collection);
                if(!isset($this->terms_fixed[$slug]->name))
                    continue;
                    
                $this->terms_fixed[$slug]->name 
                        = (isset($array[$this->terms_fixed[$slug]->term_id]))?$array[$this->terms_fixed[$slug]->term_id]:$this->terms_fixed[$slug]->name;
            else:
                $this->terms_fixed[$slug]->name = $this->terms_fixed[$slug]->name;
            endif;
        }
        
    }
    

    public function get_special_metadata() {
        return $this->special_metadata = ['relationship', 'category', 'voting','compounds'];
    }

    public function get_metadata_icon($metadata_type) {
        echo get_template_directory_uri() . "/libraries/images/icons/icon-$metadata_type.png";
    }

    public function get_type_default_widget($type) {
        if ("text" == $type) {
            return "<option value='tree'>" . __('Tree', 'tainacan') . "</option>";
        } else if ("textarea" == $type) {
            return "<option value='searchbox'>" . __('Search box with autocomplete', 'tainacan') . "</option>";
        } else {
            return "<option value='from_to'>" . __('From/To', 'tainacan') . "</option>";
        }
    }

    public function render_tree_colors() {
        ?>
        <div id="color_field_property_search">
            <h5 style="color: black"><strong><?php _e('Set the facet color', 'tainacan'); ?></strong></h5>
            <div class="form-group" style="padding-left: 5px">
                <?php
                for ($i = 1; $i < 14; $i++) {
                    echo '<label class="radio-inline"> <input type="radio" class="color_property" name="color_facet" id="color_property' . $i . '" value="color_property' . $i . '" ';
                    echo '><img src="' . get_template_directory_uri() . '/libraries/images/cor' . $i . '.png">  </label>';
                };
                ?>
            </div>
        </div>
    <?php
    }

    public function render_button_cardinality($property,$i) {
        if ($property['metas']['socialdb_property_data_cardinality'] && $property['metas']['socialdb_property_data_cardinality'] == 'n'):
            ?>
               <button type="button" 
                       id="button_property_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                       onclick="show_fields_metadata_cardinality(<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                       style="margin-top: 5px;<?php echo (is_array($property['metas']['value'])&&($i+1)<count($property['metas']['value']))? 'display:none':'' ?>" 
                       class="btn btn-primary btn-lg btn-xs btn-block">
                    <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                </button>
            <?php
        elseif ($property['metas']['socialdb_property_compounds_cardinality'] && $property['metas']['socialdb_property_compounds_cardinality'] == 'n'):
            ?>
               <button type="button" 
                       id="button_property_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                       onclick="show_fields_metadata_cardinality_compounds(<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                       style="margin-top: 5px;<?php echo (is_array($property['metas']['value'])&&($i+1)<count($property['metas']['value']))? 'display:none':'' ?>" 
                       class="btn btn-primary btn-lg btn-xs btn-block">
                    <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                </button>
            <?php
        endif;
    }
    
     public function render_cardinality_property($property,$is_data = 'false') {
        if ($property['metas']['socialdb_property_data_cardinality'] && $property['metas']['socialdb_property_data_cardinality'] == 'n'):
            return 50;
        elseif($property['metas']['socialdb_property_object_cardinality'] && $property['metas']['socialdb_property_object_cardinality'] == 'n'):
             return 50;
        elseif($property['metas']['socialdb_property_compounds_cardinality'] && $property['metas']['socialdb_property_compounds_cardinality'] == 'n'):
             return 50;
        else:
            return 1;
        endif;
    }

    public static function render_icon($icon, $ext = "svg", $alt="") {
        if ($alt == "") { $alt = __( ucfirst( $icon ), 'tainacan'); }
        $img_path = get_template_directory_uri() . '/libraries/images/icons/icon-'.$icon.'.'.$ext;

        return "<img alt='$alt' title='$alt' src='$img_path' />";
    }
    
     /**
     * function get_collection_by_object($object_id)
     * @param int $object_id O id do item
     * @return mix Retorna o post da colecao
     * 
     * @author: Eduardo Humberto 
     */
    public function helper_get_collection_by_object($object_id) {
        $categories = wp_get_object_terms($object_id, 'socialdb_category_type');
        foreach ($categories as $category) {
            $result = $this->helper_get_collection_by_category_root($category->term_id);
            if (!empty($result)) {
                return $result;
            }
        }
    }
    /**
     * 
     */
    public function get_id_list_properties($id,$source) {
        return ($this->terms_fixed[$id]) ? 'meta-item-'.$this->terms_fixed[$id]->term_id :  $source ;
    }
    /**
     * function get_collection_by_category_root($user_id)
     * @param int a categoria raiz de uma colecao
     * @return array(wp_post) a colecao de onde pertence a categoria root
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function  helper_get_collection_by_category_root($category_root_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                    SELECT p.* FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                    WHERE pm.meta_key LIKE 'socialdb_collection_object_type' and pm.meta_value like '$category_root_id'
            ";
        $result = $wpdb->get_results($query);


        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * @param string $current O nome da etapa atual
     * @param string $item O nome da etapa com a qual se quer comparar
     * @return string Nome da classe que sera atribuida a elemento, se for mesmo que a etapa atual
     * @author: Rodrigo Guimarães
     */
    private function is_current($current, $item) {
        if ($current == $item)
            echo "current";
    }

    public function render_header_config_steps($current_step) {
        $path = get_template_directory_uri();
        ?>
        <div class="col-md-12 no-padding" id="collection-steps">
            <ul class="col-md-10">
                <li class="col-md-2 <?php $this->is_current($current_step,'config'); ?> config">
                    <a onclick="showCollectionConfiguration('<?php echo $path ?>');">
                        <h4> 1. <?php _e('Configurations', 'tainacan')?> </h4>
                    </a>
                </li>
                <li class="col-md-2 <?php $this->is_current($current_step,'categories'); ?> categories">
                    <a onclick="showTaxonomyZone('<?php echo $path ?>');">
                        <h4> 2. <?php _e('Categories', 'tainacan')?> </h4>
                    </a>
                </li>
                <li class="col-md-3 <?php $this->is_current($current_step,'metadata'); ?> metadata">
                    <a onclick="showPropertiesAndFilters('<?php echo $path ?>');" class="config-section-header">
                        <h4> 3. <?php _e('Metadata and Filters', 'tainacan')?> </h4>
                    </a>
                </li>
                <li class="col-md-2 <?php $this->is_current($current_step,'layout'); ?> layout">
                    <a onclick="showLayout('<?php echo $path ?>');">
                        <h4> 4. <?php _e('Layout', 'tainacan')?> </h4>
                    </a>
                </li>
            </ul>

            <button type="submit" id="button_save_and_next" class="btn btn-primary">
                <?php _e('Save & Next', 'tainacan'); ?>
            </button>
        </div>

    <?php }

    public static function render_config_title($title) {
        echo "<h3 class='topo'> $title <button onclick='backToMainPage();' class='btn btn-default pull-right'>";
        echo  __('Back','tainacan') . "</button></h3> <hr>";
    }

    public function render_modal_header($span, $title, $extra_html="") {
        $_modal_header = "<div class='modal-header'>";
        $_modal_header .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true' class='glyphicon glyphicon-$span'></span></button>";
        $_modal_header .= "<h4 class='modal-title' id='modal-$span'>" . $title . $extra_html  . "</h4>";
        $_modal_header .= "</div>";
        
        return $_modal_header;
    }

    public function render_modal_footer($button_action="", $title) {
        $close_string = __('Close', 'tainacan');
        $_modal_footer = "<div class='modal-footer'>";
        $_modal_footer .= "<button type='button' class='btn btn-default' data-dismiss='modal'> $close_string </button>";
        $_modal_footer .= "<button type='button' class='btn btn-primary' onclick='$button_action'>$title</button>";
        $_modal_footer .= "</div>";

        return $_modal_footer;
    }
    
    public static function render_default_submit_button() {
        return "<input type='submit' class='btn btn-primary pull-right' value='". __('Save', 'tainacan') ."'/>";
    }

} // ViewHelper