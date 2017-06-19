<?php
include_once (dirname(__FILE__) . '/../input/text.class.php');
include_once (dirname(__FILE__) . '/../input/date.class.php');
include_once (dirname(__FILE__) . '/../input/textarea.class.php');
include_once (dirname(__FILE__) . '/../input/numeric.class.php');
include_once (dirname(__FILE__) . '/../input/autoincrement.class.php');

class FormItemText extends FormItem {

    public $textClass;
    public $dateClass;
    public $textareaClass;
    public $numericClass;
    public $autoincrementClass;
    public $class;

    public function widget($property, $item_id,$showButton = true) {
        $this->textClass = new TextClass(0,'',$this->value);
        $this->dateClass = new DateClass(0,'',$this->value);
        $this->textareaClass = new TextAreaClass(0,'',$this->value);
        $this->numericClass = new NumericClass(0,'',$this->value);
        $this->autoincrementClass = new AutoIncrementClass(0,'',$this->value);
        //buscando o valor
        $values = $this->getValuePropertyHelper($item_id, $property_id);
        $this->setLastIndex();
        $isMultiple = ($property['metas']['socialdb_property_data_cardinality'] == 'n') ? true : false;
        $filledValues = ($values) ? count($values) : 1;
        $isKey = (isset($property['metas']['socialdb_property_data_mask']) && $property['metas']['socialdb_property_data_mask'] !== '') ? true:false;

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
                <?php $this->hasTextHelper($property);  ?>
                <?php if ($isRequired): ?>
                *
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
                <?php endif ?>
            </h2>
            <div>
                <?php $first = false;
                  foreach ($this->value as $index => $value):  ?>
                    <div id="container-field-<?php echo $property['id'] ?>-<?php echo $index ?>"
                         class="row" style="padding-bottom: 10px;margin-bottom: 10px;">
                        <div class="col-md-11">
                            <?php if ($property['type'] == 'text'): ?>
                                <?php $this->textClass->isKey = $isKey ?>
                                <?php $this->textClass->generate($property,['id'=>0], $item_id, $index) ?>
                            <?php elseif ($property['type'] == 'date'): ?>
                                <?php $this->dateClass->isKey = $isKey ?>
                                <?php $this->dateClass->generate($property,['id'=>0], $item_id, $index) ?>
                            <?php elseif ($property['type'] == 'textarea'): ?>
                                <?php $this->textareaClass->isKey = $isKey ?>
                                <?php $this->textareaClass->generate($property,['id'=>0], $item_id, $index) ?>
                            <?php elseif ($property['type'] == 'numeric' || $property['type'] == 'number'): ?>
                                <?php $this->numericClass->isKey = $isKey ?>
                                <?php $this->numericClass->generate($property,['id'=>0], $item_id, $index) ?>
                            <?php elseif ($property['type'] == 'autoincrement'): ?>
                                <?php $this->autoincrementClass->isKey = $isKey ?>
                                <?php $this->autoincrementClass->generate($property,['id'=>0], $item_id, $index) ?>
                            <?php endif; ?>
                        </div>
                        <?php if ($first): ?>
                            <div class="col-md-1">
                                <a style="cursor: pointer;" onclick="remove_container('<?php echo $property['id'] ?>','<?php echo $index ?>',<?php echo $item_id ?>)" class="pull-right">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        <?php else: $first = true; ?>    
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                    <div id="appendTextContainer"></div>
                <?php if ($isMultiple && $showButton): ?>
                    <button type="button"
                            class="btn btn-primary btn-lg btn-xs btn-block js-append-property-<?php echo $property['id'] ?>">
                        <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
        $this->initScriptsTextContainer($property, $item_id, $index);
    }

    public function appendContainerText($property,$item_id,$index) {
        $this->textClass = new TextClass();
        $this->dateClass = new DateClass();
        $this->textareaClass = new TextAreaClass();
        $this->numericClass = new NumericClass();
        $this->autoincrementClass = new AutoIncrementClass();
        $values = $this->getValuePropertyHelper($item_id, $property_id)
        ?>
        <div id="container-field-<?php echo $property['id'] ?>-<?php echo $index ?>"
             class="row" style="padding-bottom: 10px;margin-bottom: 10px;">
            <div class="col-md-11">
                <?php if ($property['type'] == 'text'): ?>
                    <?php $this->class = $this->textClass ?>
                    <?php $this->textClass->generate($property,['id'=>0], $item_id, $index) ?>
                <?php elseif ($property['type'] == 'date'): ?>
                    <?php $this->class = $this->dateClass ?>
                    <?php $this->dateClass->generate($property,['id'=>0], $item_id, $index) ?>
                <?php elseif ($property['type'] == 'textarea'): ?>
                    <?php $this->class = $this->textareaClass ?>
                    <?php $this->textareaClass->generate($property,['id'=>0], $item_id, $index) ?>
                <?php elseif ($property['type'] == 'numeric' || $property['type'] == 'number'): ?>
                    <?php $this->class = $this->numericClass ?>
                    <?php $this->numericClass->generate($property,['id'=>0], $item_id, $index) ?>
                <?php elseif ($property['type'] == 'autoincrement'): ?>
                    <?php $this->class = $this->autoincrementClass ?>
                    <?php $this->autoincrementClass->generate($property,['id'=>0], $item_id, $index) ?>
                <?php endif; ?>
            </div>
            <div class="col-md-1">
                <a style="cursor: pointer;" onclick="remove_container('<?php echo $property['id'] ?>','<?php echo $index ?>',<?php echo $item_id ?>)" class="pull-right">
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
    public function initScriptsTextContainer($property, $item_id, $index) {
        ?>
        <script>
            var index = <?php echo $index; ?> + 1;

            $('.js-append-property-<?php echo $property['id'] ?>').click(function(){
                console.log(<?php echo $item_id ?>,<?php echo $index ?>);
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        collection_id: $("#collection_id").val(),
                        operation: 'appendContainerText',
                        item_id:'<?php echo $item_id ?>',
                        property_details: '<?php echo serialize($property) ?>',
                        index: index
                    }
                }).done(function (result) {
                    $('#meta-item-<?php echo $property['id']; ?> #appendTextContainer').append(result);
                    index++;
                });
            });

            function remove_container(property_id,index_id,item_id){
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
