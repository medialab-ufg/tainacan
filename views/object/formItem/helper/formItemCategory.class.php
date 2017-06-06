<?php
include_once (dirname(__FILE__) . '/../input/selectbox.class.php');
include_once (dirname(__FILE__) . '/../input/simpletree.class.php');
include_once (dirname(__FILE__) . '/../input/radio.class.php');
include_once (dirname(__FILE__) . '/../input/checkbox.class.php');
include_once (dirname(__FILE__) . '/../input/mutipletree.class.php');

class FormItemText extends FormItem{
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
        $this->multipleTreeClass = new multipleTreeClass();
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
             <h2>
                <?php echo $property['name']; ?>
            </h2>
            <div>
                <div id="container-field" 
                     class="row" style="padding-bottom: 10px;margin-bottom: 10px;">
                    <div class="col-md-11">
                        <?php if($property['type'] == 'text'): ?>
                            <?php $this->textClass->generate($property['id'], $item_id, 0, $index) ?>
                        <?php elseif($property['type'] == 'date'): ?>
                            <?php $this->dateClass->generate($property['id'], $item_id, 0, $index) ?>
                        <?php elseif($property['type'] == 'textarea'): ?>
                            <?php $this->textareaClass->generate($property['id'], $item_id, 0, $index) ?>
                        <?php elseif($property['type'] == 'numeric' || $property['type'] == 'number'): ?>
                            <?php $this->numericClass->generate($property['id'], $item_id, 0, $index) ?>
                        <?php elseif($property['type'] == 'autoincrement'): ?>
                            <?php $this->textClass->generate($property['id'], $item_id, 0, $index) ?>
                        <?php endif; ?>
                    </div>
                </div>    
            </div>
        </div>
        <?php
    }
}