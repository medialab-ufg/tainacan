<?php
include_once (dirname(__FILE__) . '/../input/selectbox.class.php');
include_once (dirname(__FILE__) . '/../input/simpletree.class.php');
include_once (dirname(__FILE__) . '/../input/radio.class.php');
include_once (dirname(__FILE__) . '/../input/checkbox.class.php');
include_once (dirname(__FILE__) . '/../input/multipletree.class.php');

class FormItemCategory extends FormItem{
    public $selectboxClass;
    public $simpleTreeClass;
    public $radioClass;
    public $checkboxClass;
    public $multipleTreeClass;


    public function widget($property,$item_id) {
        $this->selectboxClass = new SelectboxClass(0,'',$this->value);
        $this->simpleTreeClass = new SimpleTreeClass(0,'',$this->value);
        $this->radioClass = new RadioClass(0,'',$this->value);
        $this->checkboxClass = new CheckboxClass(0,'',$this->value);
        $this->multipleTreeClass = new MultipleTreeClass(0,'',$this->value);
        $isRequired = ($property['metas'] && $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false') ? true : false;
        ?>
        <style>
            .category-properties h2{
                font-size: 12px;
                text-indent: 2%;
                font-weight: bold;
                color: black;
                margin-left: -30px;
            }
        </style>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
             <h2>
                <?php echo $property['name']; ?>
                <?php $this->hasTextHelper($property);  ?>
                <?php if ($isRequired): ?>
                *
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
                <?php endif ?>
            </h2>
            <div>
                <?php if($property['type'] == 'selectbox'): ?>
                    <?php $this->selectboxClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'tree'): ?>
                    <?php $this->simpleTreeClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'radio'): ?>
                    <?php $this->radioClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'checkbox' || $property['type'] == 'multipleselect'): ?>
                    <?php $this->checkboxClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'tree_checkbox'): ?>
                    <?php $this->multipleTreeClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php endif; ?>
                <div class="category-properties" id="appendCategoryMetadata_<?php echo $property['id']; ?>_0_0">
                </div>
            </div>
        </div>
        <?php 
        //CASO EXISTA VALORES DE CATEGORIAS,BUSCO SEUS METADADOS
        if($this->value && is_array($this->getValues($this->value[0][0])) && !empty($this->getValues($this->value[0][0]))): 
        ?>
        <script>
        var ids = '<?php echo implode(',', $this->getValues($this->value[0][0])) ?>';
        Hook.register('appendCategoryMetadataHere',function(args){
             var categories = args[0]
             var item_id = args[1];
             var seletor = args[2];
             $(seletor)
                  .html('<center><img width="100" heigth="100" src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><?php _e('Loading metadata for this field', 'tainacan') ?></center>');
             $.ajax({
                 url: $('#src').val() + '/controllers/object/object_controller.php',
                 type: 'POST',
                 data: {
                     operation: 'appendCategoryMetadata',
                     properties_to_avoid: '<?php echo implode(',', $this->allPropertiesIds) ?>', categories: categories,object_id:item_id ,item_id:item_id,collection_id:$('#collection_id').val()}
             }).done(function (result) {
                 if(result !== ''){
                     $(seletor).css('border','1px solid #ccc');
                     $(seletor).css('padding','5px;');
                     $(seletor).css('margin-top','10px');
                     $(seletor).css('height','auto');
                     $(seletor).html(result);
                 }else{
                     $(seletor).html('');
                 }
             });
           });
        Hook.call('appendCategoryMetadataHere',[ids, <?php echo $item_id ?>, '#appendCategoryMetadata_<?php echo $property['id']; ?>_0_0']);
       </script>
        <?php 
        endif; ?>
        <?php
    }
}
