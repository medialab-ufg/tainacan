<?php
include_once(dirname(__FILE__) . '../../../../models/object/object_model.php');

class FormItemMultiple extends Model {
    
    public $metadatas;
    public $isView;
    public $itemId;
    public $mediaHabilitate = false;
    public $collection_id = false;
    public $terms_fixed;
    public $title;
    public $operation;
    public $allPropertiesIds = [];
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
    
    function __construct($collection_id = 0,$title = '',$operation = '') {
        $this->collection_id = $collection_id;
        $this->terms_fixed = [
            'title' => get_term_by('slug', 'socialdb_property_fixed_title', 'socialdb_property_type'),
            'description' => get_term_by('slug', 'socialdb_property_fixed_description', 'socialdb_property_type'),
            'content' => get_term_by('slug', 'socialdb_property_fixed_content', 'socialdb_property_type'),
            'source' => get_term_by('slug', 'socialdb_property_fixed_source', 'socialdb_property_type'),
            'license' => get_term_by('slug', 'socialdb_property_fixed_license', 'socialdb_property_type'),
            'thumbnail' => get_term_by('slug', 'socialdb_property_fixed_thumbnail', 'socialdb_property_type'),
            'attachments' => get_term_by('slug', 'socialdb_property_fixed_attachments', 'socialdb_property_type'),
            'tags' => get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type'),
            'type' => get_term_by('slug', 'socialdb_property_fixed_type', 'socialdb_property_type')
        ];
        $this->title = $title;
        $this->operation = $operation;
        $this->itemId = 'multiple';
    }
    /**
     * 
     * @param Array $data O array com os tipos separados image,pdf,video,other
     * @param Array $properties O array com os tipos separados image,pdf,video,other
     */
    public function start($data,$properties) {
        $this->initScripts();
        ?>
        <div class="row" style="padding-right: 0px;padding-left: 0px;">
            <?php $this->loadMetadataContainer($properties); ?>
            <?php $this->loadItemsContainer($data); ?>
        </div>    
        <?php
    }
    
    /**
     * 
     * @param type $properties
     */
    public function loadMetadataContainer($properties_raw) {
        $this->structureProperties($properties_raw);
        ?>
        <input type="hidden" id="item-multiple-selected">
        <div id='form_properties_items' class="col-md-3 menu_left_files menu-left-size">
            <h3 style="display:none;" id='labels_items_selected' >
                <?php _e('Editting ','tainacan') ?>
                <span id='number_of_items_selected'></span>
                <?php _e(' item/items ','tainacan') ?>
            </h3>
            <div class="expand-all-item btn white tainacan-default-tags">
                <div class="action-text" 
                     style="display: inline-block">
                         <?php _e('Expand all', 'tainacan') ?></div>
                &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
            </div>
            <div id="multiple_accordion" class="multiple-items-accordion">
                <?php $this->listPropertiesbyTab('default') ?>
            </div>
        </div> 
        <div id='no_properties_items' class="col-md-3 menu-left-size">
            <h3> <?php _e('Select items to edit...','tainacan') ?> </h3>
        </div>
        <?php    
    }
    
    /**
     * 
     * @param type $data
     */
    public function loadItemsContainer($data){
        ?>
        <div class='col-md-9' id="no_item_uploaded" style='display:none;'>
            <h3 style="text-align: center;"><?php _e('No items uploaded','tainacan') ?></h3>
        </div>
        <div class='col-md-9 pull-right' 
             style="background-color: white;border: 3px solid #E8E8E8;margin-left: 15px;">
            <?php if($this->operation !== 'add-files'): ?>
            <h3>
                <?php if($this->operation === 'add-social-network-beta'): ?> 
                    <?php echo $this->title ?>
                    <button type="button" onclick="back_main_list_discard();"
                            class="btn btn-default pull-right"> 
                                <?php _e('Cancel','tainacan') ?>
                    </button>
                <?php else: ?> 
                    <?php echo $this->title ?>
                    <button type="button" onclick="back_main_list();"
                            class="btn btn-default pull-right"> 
                                <?php _e('Cancel','tainacan') ?>
                    </button>
                <?php endif; ?> 
            </h3>
            <?php else: ?>
            <h3>
                <?php echo $this->title ?>
                <button type="button" onclick="back_main_list();"
                        class="btn btn-default pull-right"> 
                            <?php _e('Cancel','tainacan') ?>
                </button>
                <br>
                <small id="draft-text"></small>
            </h3>
            <?php endif ?>
            <hr>
            <!----------------------------- BUTTONS -------------------------------------->
           <div style="padding-bottom: 20px;" >
               <?php if($this->operation == 'add-files'): ?>
               <button type="button"
                        onclick="upload_more_files()" 
                        class="btn btn-primary">
                    <span class="glyphicon glyphicon-upload"></span>&nbsp;<?php _e('Upload more files','tainacan') ?>
                </button>
               <?php endif; ?>
               <div class="btn-group">
                   <button id="selectOptions" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php _e('Select','tainacan') ?> <span class="caret"></span>
                   </button>
                   <ul class="dropdown-menu">
                       <li><a onclick='selectAll()'  style="cursor: pointer;"> <?php _e('All','tainacan') ?></a></li>
                     <li><a onclick='unselectAll()'  style="cursor: pointer;"><?php _e('None','tainacan') ?></a></li>
                   </ul>
               </div>    
               <button id="removeSelectedButton"  onclick='removeSelected()' type="button" class="btn btn-default" >
                   <span  class="glyphicon glyphicon-trash"></span>
               </button>
               <button id="buttonSelectedAttachments" style="display: none;" onclick='selectedAttachments()' type="button" class="btn btn-default" >
                   <?php _e('Select Attachments','tainacan') ?>
               </button>
               <button id="buttonBackItems" style="display: none;" onclick='backItemsEditting()' type="button" class="btn btn-default" >
                   <?php _e('Edit Items','tainacan') ?>
               </button>
            </div>
            <!----------------------------- FIM : BUTTONS -------------------------------------->
            <div style="max-height: 500px;overflow-y: scroll">
                <div  id="selectable">
                    <?php
                        $class = new ItemsClass($this->collection_id);
                        $class->listItems($data);
                    ?>
                </div>    
            </div>  
            <!--------------- FIM: container todos os itens  ----------------------------->
            <div style="display: none" class="col-md-12" id='attachments_item_upload'>
                 <h3><?php _e('Attachments','tainacan'); ?></h3>
                 <div  id="dropzone_new" class="dropzone" style="min-height: 150px;">
                 </div>
            </div>
            <div class="col-md-12" style="padding: 15px;">
                 <?php if(isset($edit_multiple)): ?> 
                  <input type="hidden" id="edit_multiple" name="edit_multiple" value="true">
                 <button type="button" onclick="back_main_list();"
                        class="btn btn-lg btn-default pull-left"> 
                            <?php _e('Cancel','tainacan') ?>
                </button>
                 <?php else: ?>   
                 <button type="button" onclick="back_main_list_socialnetwork();"
                        class="btn btn-lg btn-default pull-left"> 
                            <?php _e('Cancel','tainacan') ?>
                </button>
                 <?php endif; ?>   
                  <button type="button" 
                          onclick="saveItems()"
                         id="submit_button" 
                         class="btn btn-lg btn-success pull-right">
                             <?php _e('Submit','tainacan'); ?>
                 </button>
            </div>
        <?php
    }
    
    /**
     * veririca se um metadado nao esta no array que ordenam as abas
     * @param type $arrayMapTabs
     * @param type $properties
     * @return type
     */
    public function structureProperties($properties) {
        $types = ['property_data', 'property_object', 'property_term', 'property_compounds','fixeds'];
        foreach ($types as $type) {
            if ($properties[$type] && is_array($properties[$type])) {
                foreach ($properties[$type] as $data) {
                     $this->metadatas['default'][]  = $data;
                }
            }
        }
    }
    
    /**
     *
     */
    public function validateIcon($id,$text = '') {
        ?>
            &nbsp;<span id="<?php echo $id ?>" class="<?php echo $id ?> pull-right validateIcon" style="color:red;font-size: 11px;display: none;"><?php echo $text ?>&nbsp;<span style="color:red;font-size: 13px;" class="glyphicon glyphicon-exclamation-sign pull-right"></span></span>
        <?php
    }
    /**
     *
     * @param type $item_id
     * @param type $property_id
     * @return boolean
     */
    public function getValuePropertyHelper($item_id, $property_id) {
        $meta = get_post_meta($item_id, 'socialdb_property_helper_' . $property_id, true);
        if ($meta && $meta != '') {
            $array = unserialize($meta);
            return $array;
        } else {
            return false;
        }
    }

    public function getValues($array){
       $ids = [];
       if(is_array($array)){
          $values = $array['values'];
          foreach ($values as $key => $value) {
            $meta = $this->sdb_get_post_meta($value);
            if(isset($meta->meta_value))
                $ids[] = $meta->meta_value;
          }
       }
       return $ids;
    }

    /**
    *
    */
    public function viewValue($property,$values,$type){
        //sessao
        return false;
    }
    
    public function hasTextHelper($property){
        if($property['metas'] &&$property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))){
            ?>
             <span     title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                       data-toggle="tooltip" 
                       data-placement="top"  class="glyphicon glyphicon-info-sign"></span>
            <script type="text/javascript">
            $(function () {
              $('[data-toggle="tooltip"]').tooltip()
            })
            </script>
            <?php
        }
    }
    
    /**
     *
     * @param type $properties1
     */
    public function listPropertiesbyTab($tab_id) {
        if (is_array($this->metadatas[$tab_id])) {
            foreach ($this->metadatas[$tab_id] as $property) {
                $this->allPropertiesIds[] = $property['id'];
                if(has_filter('property_is_visible')){
                    if(!apply_filters('property_is_visible', $property,$this->collection_id)){
                        continue;
                    }
                }
                if (in_array($property['slug'], $this->fixed_slugs)) {
                    $visibility = (get_term_meta($property['id'],'socialdb_property_visibility',true));
                    if($visibility == 'hide'){
                        continue;
                    }
                    if ($property['slug'] == 'socialdb_property_fixed_title' && !$this->isMediaFocus) {
                        $class = new FormItemTitle($this->collection_id);
                        $class->widget($property, $this->itemId);
//                    }  else if ($property['slug'] == 'socialdb_property_fixed_content'&& !$this->isMediaFocus) {
//                        $class = new FormItemContent($this->collection_id);
//                        $class->widget($property, $this->itemId);
//                    } 
                    }else if ($property['slug'] == 'socialdb_property_fixed_description') {
                        $class = new FormItemDescription($this->collection_id);
                        $class->widget($property, $this->itemId);
                    } else if ($property['slug'] == 'socialdb_property_fixed_attachments' && !$this->isMediaFocus) {
                        //$class = new FormItemAttachment($this->collection_id);
                        //$class->widget($property, $this->itemId);
                    } else if ($property['slug'] == 'socialdb_property_fixed_source') {
                        $class = new FormItemSource($this->collection_id);
                        $class->widget($property, $this->itemId);
                    } else if ($property['slug'] == 'socialdb_property_fixed_tags') {
                        $class = new FormItemTags($this->collection_id);
                        $class->widget($property, $this->itemId);
                    } else if ($property['slug'] == 'socialdb_property_fixed_license') {
                        $class = new FormItemLicense($this->collection_id);
                        $class->widget($property, $this->itemId);
                    }
                } else {
                    $data = ['text', 'textarea', 'date', 'number', 'numeric', 'auto-increment'];
                    $term = ['selectbox', 'radio', 'checkbox', 'tree', 'tree_checkbox', 'multipleselect'];
                    $object = (isset($property['metas']['socialdb_property_object_category_id']) && !empty($property['metas']['socialdb_property_object_category_id'])) ? true : false;
                    if (in_array($property['type'], $data) && !$object) {
                        $class = new FormItemText();
                        $class->widget($property, $this->itemId);
                    } else if (in_array($property['type'], $term) && !$object) {
                        $class = new FormItemCategory();
                        $class->allPropertiesIds = $this->allPropertiesIds;
                        $class->widget($property, $this->itemId);
                    } else if ($object) {
                        $class = new FormItemObject($this->collection_id);
                        $class->widget($property, $this->itemId);
                    } else if ($property['type'] == __('Compounds', 'tainacan')) {
                        $class = new FormItemCompound( $this->collection_id);
                        $class->widget($property, $this->itemId);
                    }
                }
            }
        }
    }
    
    /**
     * scripts deste
     */
    public function initScripts() {
        ?>
        <script>
            hide_modal_main();
            Hook.clearActions('get_single_item_value');
            Hook.clearActions('get_multiple_item_value');
            $('input ,select').focus(function(){
                showChangesUpdate();
            });

            $('.tabs').tab();
            $(".multiple-items-accordion").accordion({
                active: false,
                collapsible: true,
                header: "h2",
                heightStyle: "content",
                beforeActivate: function (event, ui) {
                    // The accordion believes a panel is being opened
                    if (ui.newHeader[0]) {
                        var currHeader = ui.newHeader;
                        var currContent = currHeader.next('.ui-accordion-content');
                        // The accordion believes a panel is being closed
                    } else {
                        var currHeader = ui.oldHeader;
                        var currContent = currHeader.next('.ui-accordion-content');
                    }
                    // Since we've changed the default behavior, this detects the actual status
                    var isPanelSelected = currHeader.attr('aria-selected') == 'true';

                    // Toggle the panel's header
                    currHeader.toggleClass('ui-corner-all', isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top', !isPanelSelected).attr('aria-selected', ((!isPanelSelected).toString()));

                    // Toggle the panel's icon
                    currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e', isPanelSelected).toggleClass('ui-icon-triangle-1-s', !isPanelSelected);

                    // Toggle the panel's content
                    currContent.toggleClass('accordion-content-active', !isPanelSelected)
                    if (isPanelSelected) {
                        currContent.slideUp();
                    } else {
                        currContent.slideDown();
                    }

                    return false; // Cancels the default action
                }

            });
            
            function saveItems(){
                var has_selected = [];
                var allIds = [];
                $.each($("input:checkbox[name='selected_items']"), function () {
                    allIds.push($(this).val());
                });
                <?php if($this->operation == 'add-files'): ?>
                $.each($("input:checkbox[name='selected_items']:checked"), function () {
                    has_selected.push($(this).val());
                });
                 <?php endif;  ?>
                if(has_selected.length > 0){
                    swal({
                        title: '<?php _e('Attention', 'tainacan') ?>',
                        text: '<?php _e('Save only ', 'tainacan') ?>' + has_selected.length + ' <?php _e(' selected items ?', 'tainacan') ?>',
                        type: "info",
                        showCancelButton: true,
                        confirmButtonClass: 'btn-primary',
                        closeOnConfirm: true,
                        closeOnCancel: true
                    },
                    function (isConfirm) {
                        if(isConfirm){
                            publishItems(has_selected);
                        }/*else{
                            publishItems(allIds);
                        }*/
                    });
                }else{
                    publishItems(allIds);
                }
            }
            
            function back_main_list_discard() {
                swal({
                    title: '<?php _e('Attention','tainacan') ?>',
                    text: '<?php _e('Confirm your action','tainacan') ?>',
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-primary',
                    closeOnConfirm: true,
                    closeOnCancel: true,
                    confirmButtonText: "<?php _e('Back and discard','tainacan') ?>",
                    cancelButtonText: "<?php _e('Just back', 'tainacan') ?>",
                },
                        function (isConfirm) {
                            $('#form').hide();
                            $("#tainacan-breadcrumbs").hide();
                            $('#configuration').hide();
                            $('#main_part').show();
                            $('#display_view_main_page').show();
                            $("#container_three_columns").removeClass('white-background');
                            $('#menu_object').show();
                            if (isConfirm) {
                                $.ajax({
                                    url: $('#src').val() + '/controllers/object/object_draft_controller.php',
                                    type: 'POST',
                                    data: {operation: 'clear_betafiles', collection_id: $('#collection_id').val()}
                                }).done(function (result) {
                                    // $('html, body').animate({
                                    //   scrollTop: parseInt($("#wpadminbar").offset().top)
                                    // }, 900);  
                                });
                            }
                        });
            }
            
            function back_main_list() {
                $('#form').hide();
                $("#tainacan-breadcrumbs").hide();
                $('#configuration').hide();
                $('#main_part').show();
                $('#display_view_main_page').show();
                $("#container_three_columns").removeClass('white-background');
                $('#menu_object').show();
                /*PDF CASE*/
                var idImage = [];
                var ids = sessionStorage.getItem('pdf_ids');

                //IndexedDB
                window.indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB;
                window.IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction || window.msIDBTransaction;
                window.IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange || window.msIDBKeyRange;

                if (window.indexedDB && ids)
                {
                    const dbname = "pdf_thumbnails", dbversion = 1, tbname = "pdf_thumbnails";
                    var db, request = indexedDB.open(dbname, dbversion);
                    ids = ids.split(",");

                    //Banco aberto com sucesso
                    request.onsuccess = function(event)
                    {
                        db = event.target.result;
                        delete_pdf_db(ids, db, tbname);
                    };
                }

                wpquery_clean();
            }
            
            function publishItems(ids){
                show_modal_main();
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'publishItems', 
                        items: ids, 
                        collection_id: $("#collection_id").val()
                    }
                }).done(function (result) {
                    var result = JSON.parse(result);

                    if(result.there_are_pdfFiles)
                    {
                        generate_pdfThumb();
                    }else
                    {
                        finish_process();
                    }
                });
            }
            
            function showChangesUpdate(){
                var d = new Date();
                var string = '<?php _e('Saved at ','tainacan') ?>'+ d.getDate()+'/'+(d.getMonth()+1)+'/'+d.getFullYear()
                       + ' - ' + d.getHours()+':'+d.getMinutes();
                $('#draft-text').text(string);
            }

            function finish_process()
            {
                hide_modal_main();
                $('#form').hide();
                $("#tainacan-breadcrumbs").hide();
                $('#configuration').hide();
                $('#main_part').show();
                $('#display_view_main_page').show();

                wpquery_clean();
                showAlertGeneral('<?php _e('Success','tainacan') ?>', '<?php _e('Operation was successfully!','tainacan') ?>', 'success');
            }
          </script>
        <?php
    }    
}

include_once (dirname(__FILE__) . '/helper/formItemText.class.php');
include_once (dirname(__FILE__) . '/helper/formItemCategory.class.php');
include_once (dirname(__FILE__) . '/helper/formItemObject.class.php');
include_once (dirname(__FILE__) . '/helper/formItemCompound.class.php');
//fixos
include_once (dirname(__FILE__) . '/helper/formItemTitle.class.php');
include_once (dirname(__FILE__) . '/helper/formItemThumbnail.class.php');
include_once (dirname(__FILE__) . '/helper/formItemAttachment.class.php');
include_once (dirname(__FILE__) . '/helper/formItemContent.class.php');
include_once (dirname(__FILE__) . '/helper/formItemThumbnail.class.php');
include_once (dirname(__FILE__) . '/helper/formItemDescription.class.php');
include_once (dirname(__FILE__) . '/helper/formItemSource.class.php');
include_once (dirname(__FILE__) . '/helper/formItemTags.class.php');
include_once (dirname(__FILE__) . '/helper/formItemLicense.class.php');
include_once (dirname(__FILE__) . '/helper/formItemType.class.php');
//container que lista
include_once (dirname(__FILE__) . '/items/items.class.php');

