<?php
include_once(dirname(__FILE__) . '../../../../../models/object/object_model.php');
include_once (dirname(__FILE__) . '/formItemText.class.php');
include_once (dirname(__FILE__) . '/formItemCategory.class.php');
include_once (dirname(__FILE__) . '/formItemObject.class.php');
include_once (dirname(__FILE__) . '/formItemCompound.class.php');
//fixos
include_once (dirname(__FILE__) . '/formItemTitle.class.php');
include_once (dirname(__FILE__) . '/formItemThumbnail.class.php');
include_once (dirname(__FILE__) . '/formItemAttachment.class.php');
include_once (dirname(__FILE__) . '/formItemContent.class.php');
include_once (dirname(__FILE__) . '/formItemThumbnail.class.php');
include_once (dirname(__FILE__) . '/formItemDescription.class.php');
include_once (dirname(__FILE__) . '/formItemSource.class.php');
include_once (dirname(__FILE__) . '/formItemTags.class.php');
include_once (dirname(__FILE__) . '/formItemLicense.class.php');
include_once (dirname(__FILE__) . '/formItemType.class.php');

class FormItem extends Model{
     
    public $metadatas;
    public $isView;
    public $itemId;
    public $mediaHabilitate = false;
    public $collection_id = false;
    public $fixed_slugs_helper = [
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
        $this->collection_id = $collection_id;
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
        //verifico se existe labels da colecao
        if($this->collection_id !== 0):
           $this->get_labels_fixed_properties($this->collection_id); 
        endif;
    }
    /**
     * gera as abas no formulario
     * @param type $collection_id
     */
     public function start($collection_id,$item_id,$properties) {
        $this->itemId = $item_id;
        $tabs = unserialize(get_post_meta($collection_id, 'socialdb_collection_update_tab_organization', true));
        $ordenation = unserialize(get_post_meta($collection_id, 'socialdb_collection_properties_ordenation', true));
        $default_tab = get_post_meta($collection_id, 'socialdb_collection_default_tab', true);
        $allTabs = $this->sdb_get_post_meta_by_value($collection_id, 'socialdb_collection_tab');
        $this->structureProperties($ordenation,$tabs,$allTabs,$properties);
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
                          <?php $this->listPropertiesbyTab('default') ?>
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
                                <?php $this->listPropertiesbyTab($tab->meta_id) ?>
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
    
    /**
     * Metodo que organiza os os metadados de acordo com sua aba
     * @param type $propertiesOrdenation
     * @param type $mappingTabsProperties
     * @param type $allTabs
     */
    public function structureProperties($propertiesOrdenation,$mappingTabsProperties,$allTabs,$properties) {
        $arrayIds = ['default'=>[]];
        //todas as abas
        if($allTabs && is_array($allTabs)){
            foreach ($allTabs as $tab) {
                $arrayIds[$tab->meta_id] = [];
            }
        }            
        //olhando na ordenacao
        if($propertiesOrdenation && is_array($propertiesOrdenation)){
            foreach ($propertiesOrdenation as $tab => $ordenation) {
                $arrayIds[$tab] = explode(',', $ordenation);
            }
        }
        //olhando no mapeamento
        if($mappingTabsProperties && isset($mappingTabsProperties[0])){
            foreach ($mappingTabsProperties[0] as $property_id => $tab) {
                if($property_id && $tab && !in_array($property_id, $arrayIds[$tab]) && !in_array('compounds-'.$property_id, $arrayIds[$tab])){
                    $arrayIds[$tab][] = $property_id;
                }
            }
        }
        //possiveis metadados nao ordenados
        $arrayMapTabs = $this->verifyPropertiesWithoutTabs($arrayIds, $properties);
        $this->setMetadata($arrayMapTabs, $properties);
    }
    
    /**
     * veririca se um metadado nao esta no array que ordenam as abas
     * @param type $arrayMapTabs
     * @param type $properties
     * @return type
     */
    public function verifyPropertiesWithoutTabs($arrayMapTabs,$properties){
        $types = ['property_data','property_object','property_term','property_compounds'];
        foreach ($types as $type) {
            if($properties[$type] && is_array($properties[$type])){
                foreach ($properties[$type] as $data) {
                    $tab = false;
                    foreach ($arrayMapTabs as $tabs => $values) {
                        if(in_array($data['id'], $values) || in_array('compounds-'.$data['id'], $values) ){
                            $tab = $tabs;
                        }
                    }
                    if(!$tab){
                        $arrayMapTabs['default'][] = $data['id'];
                    }
                }
            }
        }
        return $arrayMapTabs;
    }
    
    /**
     * metodo que olha no array de propriedades e retorna as informacoes 
     * necessarias
     * 
     * @param type $id
     * @param type $properties
     */
    public function getPropertyDetail($id,$properties) {
        $types = ['property_data','property_object','property_term','property_compounds'];
        foreach ($types as $type) {
            if($properties[$type] && is_array($properties[$type])){
                foreach ($properties[$type] as $data) {
                    if($data['id']==$id){
                            return $data;
                    }
                }
            }
        }
        $term = get_term_by('id', $id,'socialdb_property_type');
        if(in_array($term->slug, $this->fixed_slugs)){
            return ['id'=>$term->term_id,'name'=>$term->name,'slug'=>$term->slug];
        }
        return false;
    }
    
    /**
     * setando a variavel da classe com os dados para serem listados na
     * @param type $arrayMapTabs
     * @param type $properties
     */
    public function setMetadata($arrayMapTabs,$properties) {
          foreach ($arrayMapTabs as $tab => $values) {
              foreach ($values as $id) {
                  $values =  $this->getPropertyDetail($id,$properties);
                  if($values)
                    $this->metadatas[$tab][] = $values;
              }
          }
    }
    
    
    /**
     * 
     * @param type $properties1
     */
    public function listPropertiesbyTab($tab_id){
        if(is_array($this->metadatas[$tab_id])){
            foreach ($this->metadatas[$tab_id] as $property) {
                if(in_array($property['slug'], $this->fixed_slugs)){
                   if($property['slug'] == 'socialdb_property_fixed_title'){
                       $class = new FormItemTitle($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_thumbnail'){
                       $class = new FormItemThumbnail($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_content'){
                       $class = new FormItemContent($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_description'){
                       $class = new FormItemDescription($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_attachments'){
                       $class = new FormItemAttachment($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_source'){
                       $class = new FormItemSource($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_type'){
                       $class = new FormItemType($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_tags'){
                       $class = new FormItemTags($this->collection_id); 
                   }else if($property['slug'] == 'socialdb_property_fixed_license'){
                       $class = new FormItemLicense($this->collection_id); 
                   }
                   $class->widget($property, $this->itemId);
                }else{
                    $data = ['text','textarea','date','number','numeric','auto-increment'];
                    $term = ['selectbox','radio','checbox','tree','tree_checkbox','multipleselect'];
                    $object = (isset ($property['metas']['socialdb_property_object_category_id']) && !empty($property['metas']['socialdb_property_object_category_id'])) ? true : false;
                    if(in_array($property['type'], $data) && !$object){
                       $class = new FormItemText(); 
                       $class->widget($property, $this->itemId);
                    }else if(in_array($property['type'], $term) && !$object){
                       $class = new FormItemCategory(); 
                       $class->widget($property, $this->itemId);
                    }else if($object){
                       $class = new FormItemObject($this->collection_id); 
                       $class->widget($property, $this->itemId);
                    }else if($property['type'] == __('Compounds','tainacan')){
                       $class = new FormItemCompound(); 
                       $class->widget($property, $this->itemId);
                    }
                }
            }
        }
    }
    
    /**
     * 
     * @param type $item_id
     * @param type $property_id
     * @return boolean
     */
    public function getValuePropertyHelper($item_id,$property_id) {
        $meta = get_post_meta($item_id, 'socialdb_property_helper_'.$property_id, true);
        if($meta){
            $array = unserialize($meta);
            return $array;
        }else{
            return false;
        }
    }
    
    /**
     * 
     * @param type $collection_id
     */
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
    
    /**
     * scripts deste 
     */
    public function initScripts() {
        ?>
        <script>
            console.log(' -- Begin execution - Form item')
            $('.tabs').tab();
            $(".multiple-items-accordion").accordion({
                    active: false,
                    collapsible: true,
                    header: "h2",
                    heightStyle: "content",
                    beforeActivate: function(event, ui) {
                            // The accordion believes a panel is being opened
                           if (ui.newHeader[0]) {
                               var currHeader  = ui.newHeader;
                               var currContent = currHeader.next('.ui-accordion-content');
                            // The accordion believes a panel is being closed
                           } else {
                               var currHeader  = ui.oldHeader;
                               var currContent = currHeader.next('.ui-accordion-content');
                           }
                            // Since we've changed the default behavior, this detects the actual status
                           var isPanelSelected = currHeader.attr('aria-selected') == 'true';

                            // Toggle the panel's header
                           currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));

                           // Toggle the panel's icon
                           currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);

                            // Toggle the panel's content
                           currContent.toggleClass('accordion-content-active',!isPanelSelected)    
                           if (isPanelSelected) { currContent.slideUp(); }  else { currContent.slideDown(); }

                           return false; // Cancels the default action
                       }
                    
                });
        </script>    
        <?php    
    }
}