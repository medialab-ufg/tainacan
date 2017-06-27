<?php

class SimpleTreeClass extends FormItem {
    var $hasDefaultValue;
    public function generate($compound, $property, $item_id, $index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        $autoValidate = false;
        $this->hasDefaultValue = (isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!='') ? $property['metas']['socialdb_property_default_value'] : false;
        $values = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id]))) ? $this->getValues($this->value[$index_id][$property_id]) : false;
        $values = (!$values && $this->hasDefaultValue) ? [$this->hasDefaultValue] : $values;
        if($values && is_array($values) && $property['has_children'] && is_array($property['has_children'])){
            foreach ($values as $value) {
                foreach ($property['has_children'] as $child) {
                    if($value == $child->term_id){
                        $autoValidate = true;
                    }
                }
            }
        }
        $this->isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false');
        //var_dump($this->getValues($this->value[$property_id][$index_id]));
        $isView = $this->viewValue($property,$values,'term');
        if($isView){
            return true;
        }
        ?>
        <?php if ($this->isRequired): ?>
            <div class="form-group"
                 id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                 style="border-bottom:none;">
                 <?php endif; ?>
            <div class="row">
                <div style='height: 150px;'
                     class='col-lg-12'
                     id='simple-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>'>
                </div>
            </div>
            <?php if ($this->isRequired): ?>
                <span style="display: none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                <span style="display: none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                <span id="input2Status" class="sr-only">(status)</span>
                <input type="hidden"
                <?php if ($property_id !== 0): ?>
                           compound="<?php echo $compound['id'] ?>"
                       <?php endif; ?>
                       property="<?php echo $property['id'] ?>"
                       class="validate-class validate-compound-<?php echo $compound['id'] ?>"
                       value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
            </div>
       <?php elseif($property_id !== 0): ?>
        <input  type="hidden"
                compound="<?php echo $compound['id'] ?>"
                property="<?php echo $property['id'] ?>"
                id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                class="compound-one-field-should-be-filled-<?php echo $compound['id'] ?>"
                value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
        <?php endif;
        if ($property['has_children'] && is_array($property['has_children']))
            $this->initScriptsSimpleTreeClass($compound_id, $property_id, $item_id, $index_id, $property['has_children']);
    }

    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsSimpleTreeClass($compound_id, $property_id, $item_id, $index_id, $children) {
        ?>
        <script>
            $(document).ready(function () {
                $("#simple-<?php echo $compound_id ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>").dynatree({
                    checkbox: true,
                    // Override class name for checkbox icon:
                    classNames: {checkbox: "dynatree-radio"},
                    selectMode: 1,
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).
                    children: <?php echo $this->generateJson($compound_id, $property_id, $item_id, $index_id,$children) ?>,
                    onLazyRead: function (node) {
                        node.appendAjax({
                            url: $('#src').val() + '/controllers/collection/collection_controller.php',
                            data: {
                                collection: $("#collection_id").val(),
                                key: node.data.key,
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onSelect: function (flag, node) {
                        if (node.bSelected) {
                            $.ajax({
                                url: $('#src').val() + '/controllers/object/form_item_controller.php',
                                type: 'POST',
                                data: {
                                    operation: 'saveValue',
                                    type: 'term',
                                    value: node.data.key,
                                    item_id: '<?php echo $item_id ?>',
                                    compound_id: '<?php echo $compound_id ?>',
                                    property_children_id: '<?php echo $property_id ?>',
                                    index: <?php echo $index_id ?>,
                                    indexCoumpound: 0
                                }
                            });
                            appendCategoryMetadata(node.data.key, <?php echo $item_id ?>, '#appendCategoryMetadata_<?php echo $compound_id; ?>_0_0');
                            <?php if ($this->isRequired): ?>
                                validateFieldsMetadataText(
                                  node.data.key, '<?php echo $compound_id ?>', '<?php echo $property_id ?>', '<?php echo $index_id ?>');
                            <?php endif; ?>
                        } else {
                             $('#appendCategoryMetadata_<?php echo $compound_id; ?>_0_0').html('');
                             $.ajax({
                                 url: $('#src').val() + '/controllers/object/form_item_controller.php',
                                 type: 'POST',
                                 data: {
                                     operation: 'saveValue',
                                     type: 'term',
                                     value: '',
                                     item_id: '<?php echo $item_id ?>',
                                     compound_id: '<?php echo $compound_id ?>',
                                     property_children_id: '<?php echo $property_id ?>',
                                     index: <?php echo $index_id ?>,
                                     indexCoumpound: 0
                                 }
                             });
                            <?php if ($this->isRequired): ?>
                                validateFieldsMetadataText('', '<?php echo $compound_id ?>', '<?php echo $property_id ?>', '<?php echo $index_id ?>')
                            <?php endif; ?>
                        }
                    }
                });
            });
        </script>
        <?php
    }

    public function generateJson($compound_id, $property_id, $item_id, $index_id,$array) {
        foreach ($array as $term) {
            $is_selected = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id])) && in_array($term->term_id,$this->getValues($this->value[$index_id][$property_id]))) ? true : false;
            $is_selected = (!$is_selected && $this->hasDefaultValue == $term->term_id ) ? true : $is_selected;
            if (mb_detect_encoding($term->name) == 'UTF-8' || mb_detect_encoding($term->name) == 'ASCII') {
                $dynatree[] = array('select'=>$is_selected,'title' => ucfirst(Words($term->name, 30)), 'key' => $term->term_id, 'isLazy' => true,'expand' => false, 'addClass' => 'color1');
            } else {
                $dynatree[] = array('select'=>$is_selected,'title' => ucfirst(Words(utf8_decode(utf8_encode($term->name)), 30)), 'key' => $term->term_id, 'isLazy' => true, 'expand' => false, 'addClass' => 'color1');
            }
        }
        return json_encode($dynatree);
    }

}
