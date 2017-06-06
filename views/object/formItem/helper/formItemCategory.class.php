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
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
             <h2>
                <?php echo $property['name']; ?>
            </h2>
            <div>
                <?php if($property['type'] == 'selectbox'): ?>
                    <?php $this->selectboxClass->generate($property, $item_id, 0, $index) ?>
                <?php elseif($property['type'] == 'tree'): ?>
                    <?php $this->simpleTreeClass->generate($property, $item_id, 0, $index) ?>
                <?php elseif($property['type'] == 'radio'): ?>
                    <?php $this->radioClass->generate($property, $item_id, 0, $index) ?>
                <?php elseif($property['type'] == 'checkbox' || $property['type'] == 'multipleselect'): ?>
                    <?php $this->checkboxClass->generate($property, $item_id, 0, $index) ?>
                <?php elseif($property['type'] == 'tree_checkbox'): ?>
                    <?php $this->multipleTreeClass->generate($property, $item_id, 0, $index) ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}