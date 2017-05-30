<?php

class ViewHelper {

    public $metadata_types;
    public $default_metadata;
    public $special_metadata;
    public $hide_main_container = false;
    public $mediaHabilitate = false;
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

    public function renderRepositoryLogo($_logo_id, $fallback_title) {
        $_max_img_width = "100%";
        $_max_img_height = "100%";

        if( isset($_logo_id) && get_the_post_thumbnail($_logo_id, 'thumbnail')) {
            $extraClass = "repository-logo";

            if (get_the_post_thumbnail($_logo_id, 'thumbnail')) {
              $_img_url = wp_get_attachment_url(get_post_thumbnail_id($_logo_id));
              $ret = '<img src="' . $_img_url . '" style="max-width: '. $_max_img_width .'; max-height: '. $_max_img_height .';" />';
            } else {
              $ret = empty($fallback_title) ? __('Tainacan', 'tainacan') : $fallback_title;
            }
        } else {
            $extraClass = "logo-tainacan";
            $ret = '<img src="'. get_template_directory_uri() . '/libraries/images/Tainacan_pb.svg'.'" style="max-width: '. $_max_img_width .'; max-height: '. $_max_img_height .';"/>';
        }

      return "<a class='col-md-3 navbar-brand $extraClass' href='" . site_url() . "'>" . $ret . "</a>";
    }

    public function get_metadata_types() {
        $this->metadata_types = [
            'text' => __('Text', 'tainacan'),
            'textarea' => __('Long text', 'tainacan'),
            'date' => __('Date', 'tainacan'),
            'numeric' => __('Numeric', 'tainacan'),
            'autoincrement' => __('Auto-Increment', 'tainacan'),
            'relationship' => __('Relationship', 'tainacan'),
            'category' => __('Category', 'tainacan'),
            'voting' => __('Rankings', 'tainacan'),
            'metadata_compound' => __('Compounds', 'tainacan'),
        ];

        if(has_action("add_new_user_properties"))
        {
            $this->metadata_types['user'] = __('User', 'tainacan');
        }

        return $this->metadata_types;
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
        return $this->special_metadata = ['relationship', 'category', 'voting','compounds','metadata_compound', 'user'];
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
                <?php if($i>0): ?>
                <div class="col-md-1">
                    <a style="cursor: pointer;" onclick="remove_container(<?php echo $property['id'] ?>,<?php echo $i ?>)" class="pull-right">
                        <span class="glyphicon glyphicon-remove"></span>
                    </a>
                </div>    
                <?php endif; ?>
                <br>
               <button type="button" 
                       id="button_property_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                       onclick="show_fields_metadata_cardinality(<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                       style="margin-top: 50px;<?php echo (isset($property['metas']['value'])&&is_array($property['metas']['value'])&&($i+1)<count($property['metas']['value']))? 'display:none':'' ?>" 
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
        if (isset($property['metas']['socialdb_property_data_cardinality']) && $property['metas']['socialdb_property_data_cardinality'] == 'n'):
            return 25;
        elseif(isset($property['metas']['socialdb_property_object_cardinality']) && $property['metas']['socialdb_property_object_cardinality'] == 'n'):
             return 25;
        elseif(isset($property['metas']['socialdb_property_compounds_cardinality']) && $property['metas']['socialdb_property_compounds_cardinality'] == 'n'):
             return 25;
        else:
            return 1;
        endif;
    }
    
    public function get_date_edit($value){
        if(strpos($value, '-')!==false){
             return explode('-', $value)[2].'/' .explode('-',$value)[1].'/' .explode('-',$value)[0];
        }else{
            return $value;
        }
    }

    public static function render_icon($icon, $ext = "svg", $alt="") {
        if ($alt == "") { $alt = __( ucfirst( $icon ), 'tainacan'); }
        $img_path = get_template_directory_uri() . '/libraries/images/icons/icon-'.$icon.'.'.$ext;

        return "<img alt='$alt' title='$alt' src='$img_path' class='icon-$icon'/>";
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

            <button type="submit" id="conclude_config" class="btn btn-default btn-lg pull-right">
                <?php _e('Conclude', 'tainacan'); ?>
            </button>
        </div>

    <?php }

    public static function render_config_title($title,$has_link = false) {
        $onclick = 'backToMainPage();';
        $onclick = "backRoute($('#slug_collection').val());";
        echo "<h3 class='topo'> $title ";
        self::buttonVoltar((__("Events", 'tainacan') == $title) ? $has_link : false);
        echo  "</h3><hr>";
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
        $_modal_footer .= "<button type='button' class='btn btn-default pull-left' data-dismiss='modal'> $close_string </button>";
        $_modal_footer .= "<button type='button' class='btn btn-primary' onclick='$button_action'>$title</button>";
        $_modal_footer .= "</div>";

        return $_modal_footer;
    }
    
    public static function render_default_submit_button() {
        return "<input type='submit' class='btn btn-primary pull-right' value='". __('Save', 'tainacan') ."'/>";
    }

    public static function collection_view_modes() {
        return [
          'cards'   => __('Cards', 'tainacan'),
          'list'    => __('List', 'tainacan'),
          'gallery' => __('Gallery', 'tainacan'),
          'slideshow' => __('Slideshow', 'tainacan'),
        ];
    }
    
    /**
     * 
     * @param type $data
     * @return type
     */
    public function check_privacity_collection($collection_id) {
        $result = array();
        $get_privacity = wp_get_object_terms($collection_id, 'socialdb_collection_type');
        if ($get_privacity) {
            foreach ($get_privacity as $privacity) {
                $privacity_name = $privacity->name;
            }
        }
        $moderator =  verify_collection_moderators($collection_id, get_current_user_id());

        if ($privacity_name == 'socialdb_collection_public' || current_user_can('manage_options')) {
            return true;
        } elseif ($privacity_name == 'socialdb_collection_private') {
            if ($moderator) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * function get_collection_by_category_root($user_id)
     * @param int a categoria raiz de uma colecao
     * @return array(wp_post) a colecao de onde pertence a categoria root
     * @ metodo responsavel em retornar as colecoes de um determinando usuario
     * @author: Eduardo Humberto 
     */
    public function get_collection_by_category_root($category_root_id) {
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
    
    public static function getCollectionColors($col_id) {
       return unserialize(get_post_meta($col_id,'socialdb_default_color_scheme', true) );
    }
    
    public function render_statistic_menu() {
        $current_step = 'sa';
        $path = get_template_directory_uri();
        ?>
        <div class="col-md-12 no-padding" id="collection-steps">
            <ul class="col-md-10">
                <li id="dashboard" class="col-md-2 <?php $this->is_current($current_step,'config'); ?>">
                    <a onclick="showCollectionConfiguration('<?php echo $path ?>');">
                        <h4> <?php _e('Dashboard', 'tainacan')?> </h4>
                    </a>
                </li>
                <li id="stats" class="col-md-2 <?php $this->is_current($current_step,'categories'); ?> categories">
                    <a> <h4> <?php _e('Statistics', 'tainacan')?> </h4> </a>
                </li>
            </ul>

            <button type="submit" id="conclude_config" class="btn btn-default btn-lg pull-right">
                <?php _e('Conclude', 'tainacan'); ?>
            </button>
        </div>
        <?php
    }

    private function prepareYouTubeVideo($URL) {
        $_fst = explode("v=", $URL);
        $_sec = explode("&", $_fst[1]);
        $_video_id = $_sec[0];

        return "http://www.youtube.com/embed/" . $_video_id . "?html5=1";
    }

    public function videoSlideItemHtml($_item_id) {
        $_item_type = get_post_meta($_item_id, "socialdb_object_dc_type")[0];
        
        if($_item_type === "video") {
            $_vd = get_post_meta($_item_id, 'socialdb_object_content')[0];
            if (strpos($_vd, 'youtube') !== false) {
                $videoURL = $this->prepareYouTubeVideo($_vd);
            } elseif (strpos($_vd, 'vimeo') !== false) {
                $step1 = explode('/', rtrim($_vd, '/'));
                $video_id = end($step1);
                $videoURL = "https://player.vimeo.com/video/" . $video_id;
            } else {
                $_check_val = intval($_vd);

                if($_check_val > 0 && is_int($_check_val)) {
                    $_vd = get_post_meta($_item_id, 'socialdb_object_dc_source')[0];
                    $videoURL = $this->prepareYouTubeVideo($_vd);
                } else {
                    echo get_item_thumb_image($_item_id, "large");
                    return;
                }
            }
            echo "<div class='embed-responsive embed-responsive-16by9 $_item_type'><iframe class='embed-responsive-item' src='$videoURL'></iframe></div>";
        } else { ?>           
            <div class="col-md-12">               
                <?php echo get_item_thumb_image($_item_id, "large"); ?>
            </div>
            <?php 
        }
    }
    
    public static function buttonVoltar($redirect = false){
        if($redirect){ ?>
            <!--button onclick="backToMainPage();" class="btn btn-default pull-right"><?php _e('Back to collection', 'tainacan') ?></button-->
            <button onclick="window.location = '<?php echo $redirect ?>'" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
            <?php
        }else{
            ?>
            <!--button onclick="backToMainPage();" class="btn btn-default pull-right"><?php _e('Back to collection', 'tainacan') ?></button-->
            <button onclick="backRoute($('#slug_collection').val());" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
            <?php
        }
    }
    
    /**
     * 
     * @param type $show_target_properties
     */
    public function commomFieldsProperties($show_target_properties = false) {
        if($show_target_properties)
            $this->getTargetProperties();
        ?>
        <div  class="form-group" style="margin-top:15px;">
            <label for="property_lock_field"><?php _e('Lock this field','tainacan'); ?></label><br>
            <input type="checkbox" name="socialdb_event_property_lock_field" class="property_lock_field"  value="true">&nbsp;<?php _e('Lock this field','tainacan'); ?>
        </div>
        <?php
    }
######################### Propriedades filtro ##################################    
    /**
     * 
     */
    public function getTargetProperties() {
        $this->setJavascriptTragetProperties();
        ?>
        <div class="form-group" style="margin-top:15px;">
            <label for="property_object_required"><?php _e('Properties to use in search','tainacan'); ?></label>
            <div id="properties_target" style="height: 100px;overflow-y: scroll;" >
                <center><?php _e('No properties found','tainacan') ?>!</center>
            </div>
            <input type="hidden" name="socialdb_event_property_to_search_in" id="properties_to_search_in">
        </div>
        <div class="form-group">
            <label for="property_avoid_items"><?php _e('Avoid selected items','tainacan'); ?></label><br>
            <input type="checkbox" name="socialdb_event_property_avoid_items" class="property_avoid_items"  value="true">&nbsp;<?php _e('Search only no selected items','tainacan'); ?><br>
        </div>
        <?php
    }
    
    private function setJavascriptTragetProperties(){
        ?>
        <script>
            function setTargetProperties(seletor){
                $('#properties_target').html('');
                if($(seletor).val()===''){
                     $('#properties_target').html('<center><?php _e('No properties found','tainacan') ?>!</center>');
                }else{
                    $.ajax({
                       type: "POST",
                       url: $('#src').val() + "/controllers/property/property_controller.php",
                       data: { operation: 'setTargetProperties',categories:$(seletor).val() }
                   }).done(function(result) {
                       var json = JSON.parse(result);
                       if(json.properties.length==0){
                            $('#properties_target').html('<center><?php _e('No properties found','tainacan') ?>!</center>');
                       }else{
                            var is_checked_title = '';
                            if($('#properties_to_search_in').val().split(',').indexOf(json.title.id.toString())>=0){
                                is_checked_title = 'checked="checked"'
                            }
                            $('#properties_target').append('<input type="checkbox" '+is_checked_title+' value="'+json.title.id+'" onchange="setValuesTargetProperties()" class="target_values">&nbsp;'+json.title.labels.join('/')+'<br>');
                            $.each(json.properties,function(index,property){
                                var is_checked = '';
                                //console.log($('#properties_to_search_in').val().split(',').indexOf(property.id),$('#properties_to_search_in').val().split(','),property.id);
                                if($('#properties_to_search_in').val().split(',').indexOf(property.id.toString())>=0){
                                    is_checked = 'checked="checked"'
                                }
                                $('#properties_target').append('<input type="checkbox" '+is_checked+' value="'+property.id+'" onchange="setValuesTargetProperties()" class="target_values">&nbsp;'+property.name+' ('+property.type+')<br>');
                            })
                       }
                   });
               }
            }
            
            /**
            *
            ** @returns {undefined}             */
            function setValuesTargetProperties(){
                var size = $('.target_values').length;
                var values = [];
                if(size>0){
                    $.each($('.target_values'),function(index,value){
                        if($(value).is(':checked')){
                           values.push($(value).val());
                        }
                    });
                }
                $('#properties_to_search_in').val(values.join(','));
            }
        </script>
        <?php
    }
} // ViewHelper