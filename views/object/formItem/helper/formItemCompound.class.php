<?php
include_once (dirname(__FILE__) . '/../input/text.class.php');
include_once (dirname(__FILE__) . '/../input/date.class.php');
include_once (dirname(__FILE__) . '/../input/textarea.class.php');
include_once (dirname(__FILE__) . '/../input/numeric.class.php');
include_once (dirname(__FILE__) . '/../input/autoincrement.class.php');
include_once (dirname(__FILE__) . '/../input/object.class.php');
include_once (dirname(__FILE__) . '/../input/selectbox.class.php');
include_once (dirname(__FILE__) . '/../input/simpletree.class.php');
include_once (dirname(__FILE__) . '/../input/radio.class.php');
include_once (dirname(__FILE__) . '/../input/checkbox.class.php');
include_once (dirname(__FILE__) . '/../input/multipletree.class.php');

class FormItemCompound extends FormItem {

    public $textClass;
    public $dateClass;
    public $textareaClass;
    public $numericClass;
    public $autoincrementClass;
    public $selectboxClass;
    public $simpleTreeClass;
    public $radioClass;
    public $checkboxClass;
    public $multipleTreeClass;
    public $objectClass;

    public function __construct($collection_id,$value = false) {
        $this->textClass = new TextClass($collection_id,'',$value);
        $this->dateClass = new DateClass($collection_id,'',$value);
        $this->textareaClass = new TextAreaClass($collection_id,'',$value);
        $this->numericClass = new NumericClass($collection_id,'',$value);
        $this->autoincrementClass = new AutoIncrementClass($collection_id,'',$value);
        $this->selectboxClass = new SelectboxClass($collection_id,'',$value);
        $this->simpleTreeClass = new SimpleTreeClass($collection_id,'',$value);
        $this->radioClass = new RadioClass($collection_id,'',$value);
        $this->checkboxClass = new CheckboxClass($collection_id,'',$value);
        $this->multipleTreeClass = new MultipleTreeClass($collection_id,'',$value);
        $this->objectClass = new ObjectClass($collection_id,'',$value);
    }

    public function widget($property, $item_id) {
        $values = $this->getValuePropertyHelper($item_id, $property_id);
        $this->setLastIndex();
        $isMultiple = ($property['metas']['socialdb_property_compounds_cardinality'] == 'n') ? true : false;
        $filledValues = ($values) ? count($values) : 1;
        $childrenProperties = $property['metas']['socialdb_property_compounds_properties_id'];
        $isRequired = ($property['metas'] && $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false') ? true : false;
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
            <h2>
                <?php echo $property['name']; ?>
                <?php
                if (has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                ?>
                <?php if ($isRequired && $property['metas']['socialdb_property_required'] == 'true'): ?>
                    *
                    <span id="AllFieldsShouldBeFilled<?php echo $property['id']; ?>"></span>
                <?php elseif ($isRequired && $property['metas']['socialdb_property_required'] === 'true_one_field'): ?>
                    (*)
                    <input 
                        type="hidden" 
                        value="false" 
                        compound="<?php echo $property['id']; ?>"
                        class="validate-class compound-one-field-should-be-filled">
                <?php else: ?>
                    <span id="someFieldsAreRequired<?php echo $property['id']; ?>"></span>
                <?php endif ?>
                <?php $this->hasTextHelper($property);  ?>
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>    
            </h2>
            <div>
               <?php $first = false;
                  foreach ($this->value as $index => $value):  ?>
                    <div id="container-field" 
                         class="row" style="padding-bottom: 10px;margin-bottom: 10px;">
                        <div class="col-md-11">
                            <?php if (is_array($childrenProperties)): ?>
                                <?php
                                foreach ($childrenProperties as $child):
                                    $child['metas']['socialdb_property_required'] = ($isRequired && $property['metas']['socialdb_property_required'] == 'true') ? 'true' : $child['metas']['socialdb_property_required'];
                                    $isRequiredChildren = ($child['metas'] && $child['metas']['socialdb_property_required']&&$child['metas']['socialdb_property_required'] != 'false') ? true : false;
                                    $object = (isset($child['metas']['socialdb_property_object_category_id']) && !empty($child['metas']['socialdb_property_object_category_id'])) ? true : false;
                                    $isKey = (isset($child['metas']['socialdb_property_data_mask']) && $child['metas']['socialdb_property_data_mask'] !== '') ? true:false;
                                    ?>
                                    <div style="padding-bottom: 15px; " class="col-md-12">
                                        <p style="color: black;">
                                            <?php echo $child['name']; ?>
                                            <?php 
                                            if ($isRequiredChildren): ?>
                                            <?php $this->validateIcon('alert-compound-'.$child['id'],__('Required field','tainacan')) ?>
                                             *
                                             <script>
                                                 $('#someFieldsAreRequired<?php echo $property['id']; ?>').html('(*)')
                                             </script>
                                            <?php endif ?>
                                            <?php $this->hasTextHelper($child);  ?>
                                        </p>
                                    <?php if ($child['type'] == 'text'): ?>
                                        <?php $this->textClass->isKey = $isKey ?>
                                        <?php $this->textClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'date'): ?>
                                        <?php $this->dateClass->isKey = $isKey ?>
                                        <?php $this->dateClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'textarea'): ?>
                                        <?php $this->textareaClass->isKey = $isKey ?>
                                        <?php $this->textareaClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'numeric' || $child['type'] == 'number'): ?>
                                        <?php $this->numericClass->isKey = $isKey ?>
                                        <?php $this->numericClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'autoincrement'): ?>
                                         <?php $this->autoincrementClass->isKey = $isKey ?>
                                        <?php $this->autoincrementClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'selectbox'): ?>
                                        <?php $this->selectboxClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'tree'): ?>
                                        <?php $this->simpleTreeClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'radio'): ?>
                                        <?php $this->radioClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'checkbox' || $child['type'] == 'multipleselect'): ?>
                                        <?php $this->checkboxClass->generate($property,$child, $item_id,$index) ?>
                                    <?php elseif ($child['type'] == 'tree_checkbox'): ?>
                                        <?php $this->multipleTreeClass->generate($property,$child, $item_id,$index) ?>
                                     <?php elseif ($object): ?>
                                        <?php $this->objectClass->generate($property, $child,$item_id, $index) ?>
                                    <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>    
                        <?php endif; ?>
                        </div>
                    <?php if ($first): ?>
                            <div class="col-md-1">
                                <a style="cursor: pointer;" onclick="remove_container(<?php echo $property['id'] ?>,<?php echo $$index ?>)" class="pull-right">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div> 
                    <?php else: $first = true; ?>    
                    <?php endif; ?>
                    </div>    
                <?php endforeach; ?>
                <div id="appendCompoundsContainer"></div>
        <?php if ($isMultiple): ?>
                <center>
                    <button type="button"  
                            style="width: 50%;margin-bottom: 5px;"
                            class="btn btn-primary btn-lg btn-xs js-append-property-<?php echo $property['id'] ?>">
                        <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                    </button>
                </center>    
        <?php endif; ?>
            </div>
        </div>
        <?php
        $this->initScriptsCompoundsContainer($property, $item_id, $index);
    }
    
    public function appendContainerCompounds($property,$item_id,$index) {
        $values = $this->getValuePropertyHelper($item_id, $property_id);
        $childrenProperties = $property['metas']['socialdb_property_compounds_properties_id'];
        ?>
        <div id="container-field-<?php echo $property['id'] ?>-<?php echo $index ?>" 
             class="row" style="padding-bottom: 10px;margin-bottom: 10px;">
            <div class="col-md-11">
                <?php if (is_array($childrenProperties)): ?>
                    <?php
                    foreach ($childrenProperties as $child):
                        $isRequiredChildren = ($child['metas'] && $child['metas']['socialdb_property_required']&&$child['metas']['socialdb_property_required'] != 'false') ? true : false;
                        $object = (isset($child['metas']['socialdb_property_object_category_id']) && !empty($child['metas']['socialdb_property_object_category_id'])) ? true : false;
                        ?>
                        <div style="padding-bottom: 15px; " class="col-md-12">
                            <p style="color: black;">
                                <?php echo $child['name']; ?>
                                <?php 
                                if ($isRequiredChildren): ?>
                                <?php $this->validateIcon('alert-compound-'.$child['id'],__('Required field','tainacan')) ?>
                                 *
                                 <script>
                                     $('#someFieldsAreRequired<?php echo $property['id']; ?>').html('(*)')
                                 </script>
                                <?php endif ?>
                                <?php $this->hasTextHelper($child);  ?>
                            </p>
                        <?php if ($child['type'] == 'text'): ?>
                            <?php $this->textClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'date'): ?>
                            <?php $this->dateClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'textarea'): ?>
                            <?php $this->textareaClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'numeric' || $child['type'] == 'number'): ?>
                            <?php $this->numericClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'autoincrement'): ?>
                            <?php $this->textClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'selectbox'): ?>
                            <?php $this->selectboxClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'tree'): ?>
                            <?php $this->simpleTreeClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'radio'): ?>
                            <?php $this->radioClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'checkbox' || $child['type'] == 'multipleselect'): ?>
                            <?php $this->checkboxClass->generate($property,$child, $item_id,$index) ?>
                        <?php elseif ($child['type'] == 'tree_checkbox'): ?>
                            <?php $this->multipleTreeClass->generate($property,$child, $item_id,$index) ?>
                         <?php elseif ($object): ?>
                            <?php $this->objectClass->generate($property, $child,$item_id, $index) ?>
                        <?php endif; ?>
                        </div>
                    <?php endforeach; ?>    
            <?php endif; ?>
            </div>
            <div class="col-md-1">
                <a style="cursor: pointer;" onclick="remove_container_compounds(<?php echo $property['id'] ?>,<?php echo $index ?>,'<?php echo $item_id ?>')" class="pull-right">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </div> 
        </div>    
        <?php
    }
    
    public function setLastIndex(){
       if($this->value && is_array($this->value)){
          //$this->value[] = '';
       }else{
         $this->value = [''];
       }
    }
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsCompoundsContainer($property, $item_id, $index) {
        ?>
        <script>
            var index = <?php echo $index; ?> + 1;

            $('.js-append-property-<?php echo $property['id'] ?>').click(function(){
                //console.log(<?php echo $item_id ?>,<?php echo $index ?>);
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        collection_id: $("#collection_id").val(),
                        operation: 'appendContainerCompounds',
                        item_id:'<?php echo $item_id ?>',
                        property_details: '<?php echo htmlentities(serialize($property)) ?>',
                        index: index
                    }
                }).done(function (result) {
                    $('#meta-item-<?php echo $property['id']; ?> #appendCompoundsContainer').append(result);
                    index++;
                });
            });
            
            function remove_container_compounds(property_id,index_id,item_id){
                $('#container-field-'+property_id+'-'+index_id).remove();
                $.ajax({
                       url: $('#src').val() + '/controllers/object/form_item_controller.php',
                       type: 'POST',
                       data: {
                           operation: 'removeIndexValues',
                           item_id:item_id,
                           compound_id:property_id,
                           index: index_id
                       }
                   });
            }
        </script> 
        <?php
    }

}
