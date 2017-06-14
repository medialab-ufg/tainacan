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
        $this->selectboxClass = new SelectboxClass();
        $this->simpleTreeClass = new SimpleTreeClass();
        $this->radioClass = new RadioClass();
        $this->checkboxClass = new CheckboxClass();
        $this->multipleTreeClass = new MultipleTreeClass();
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
                <div class="category-properties" id="appendCategoryMetadata_<?php echo $property['id']; ?>_0_0"></div>
            </div>
        </div>
        <?php
    }
}