<?php
include_once (dirname(__FILE__) . '/../input/object.class.php');

class FormItemObject extends FormItem{

    public $objectClass;

    public function widget($property,$item_id) {
        $this->objectClass = new ObjectClass($this->collection_id,'',$this->value);
        $values = $this->getValuePropertyHelper($item_id, $property_id);
        $isMultiple = ($property['metas']['socialdb_property_data_cardinality'] == 'n') ? true : false;
        $filledValues = ($values) ? count($values) : 1;
        $isRequired = ($property['metas'] && $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false') ? true : false;
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
             <h2>
                <?php echo $property['name']; ?>
                <?php
                if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                ?>
                <?php if ($isRequired): ?>
                *
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
                <?php endif ?>
            </h2>
            <div>
                <?php $this->objectClass->generate($property,['id'=>0], $item_id, 0) ?>
            </div>
        </div>
        <?php
    }
}
