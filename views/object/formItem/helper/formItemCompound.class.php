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

    public function __construct() {
        $this->textClass = new TextClass();
        $this->dateClass = new DateClass();
        $this->textareaClass = new TextAreaClass();
        $this->numericClass = new NumericClass();
        $this->autoincrementClass = new AutoIncrementClass();
        $this->selectboxClass = new SelectboxClass();
        $this->simpleTreeClass = new SimpleTreeClass();
        $this->radioClass = new RadioClass();
        $this->checkboxClass = new CheckboxClass();
        $this->multipleTreeClass = new MultipleTreeClass();
        $this->objectClass = new ObjectClass();
    }

    public function widget($property, $item_id) {

        $values = $this->getValuePropertyHelper($item_id, $property_id);
        $isMultiple = ($property['metas']['socialdb_property_compounds_cardinality'] == 'n') ? true : false;
        $filledValues = ($values) ? count($values) : 1;
        $childrenProperties = $property['metas']['socialdb_property_compounds_properties_id'];
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
            <h2>
                <?php echo $property['name']; ?>
                <?php
                if (has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                ?>
            </h2>
            <div>
                <?php for ($index = 0; $index < $filledValues; $index++): ?>
                    <div id="container-field" 
                         class="row" style="padding-bottom: 10px;margin-bottom: 10px;">
                        <div class="col-md-11">
                            <?php if (is_array($childrenProperties)): ?>
                                <?php
                                foreach ($childrenProperties as $child):
                                    $object = (isset($child['metas']['socialdb_property_object_category_id']) && !empty($child['metas']['socialdb_property_object_category_id'])) ? true : false;
                                    ?>
                                    <div style="padding-bottom: 15px; " class="col-md-12">
                                        <p style="color: black;"><?php echo $child['name']; ?></p>
                                    <?php if ($child['type'] == 'text'): ?>
                                        <?php $this->textClass->generate($child['id'], $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'date'): ?>
                                        <?php $this->dateClass->generate($child['id'], $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'textarea'): ?>
                                        <?php $this->textareaClass->generate($child['id'], $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'numeric' || $child['type'] == 'number'): ?>
                                        <?php $this->numericClass->generate($child['id'], $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'autoincrement'): ?>
                                        <?php $this->textClass->generate($child['id'], $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'selectbox'): ?>
                                        <?php $this->selectboxClass->generate($child, $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'tree'): ?>
                                        <?php $this->simpleTreeClass->generate($child, $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'radio'): ?>
                                        <?php $this->radioClass->generate($child, $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'checkbox' || $child['type'] == 'multipleselect'): ?>
                                        <?php $this->checkboxClass->generate($child, $item_id, 0, $index) ?>
                                    <?php elseif ($child['type'] == 'tree_checkbox'): ?>
                                        <?php $this->multipleTreeClass->generate($child, $item_id, 0, $index) ?>
                                     <?php elseif ($object): ?>
                                        <?php $this->objectClass->generate($child, $item_id, 0, $index) ?>
                                    <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>    
                        <?php endif; ?>
                        </div>
            <?php if ($index > 0): ?>
                            <div class="col-md-1">
                                <a style="cursor: pointer;" onclick="remove_container(<?php echo $property['id'] ?>,<?php echo $$index ?>)" class="pull-right">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div> 
                    <?php endif; ?>
                    </div>    
                <?php endfor; ?>
        <?php if ($isMultiple): ?>
                    <button type="button" 
                            onclick="show_fields_metadata_cardinality(<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                            style="margin-top: 50px;" 
                            class="btn btn-primary btn-lg btn-xs btn-block">
                        <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                    </button>
        <?php endif; ?>
            </div>
        </div>
        <?php
    }

}
