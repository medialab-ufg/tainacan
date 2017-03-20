<?php

$_GET['is_module_active'] = TRUE;
require_once(dirname(__FILE__) . '../../../models/item/item_model.php');
include_once(dirname(__FILE__) . '/../../../../controllers/general/general_controller.php');
include_once(dirname(__FILE__) . '/../../../../models/category/category_model.php');
include_once(dirname(__FILE__) . '/../../../../models/object/object_model.php');

class ItemController extends Controller {

    public function operation($operation, $data) {
        switch ($operation) {
            //adicionar um novo argumento 
            case 'show-item':
                $type = get_post_meta($data['object_id'], 'socialdb_object_contest_type', true);
                if ($type == 'argument'):
                    $data['object'] = get_post($data['object_id']);
                    return $this->render(dirname(__FILE__) . '../../../views/item/item.php', $data);
                else:
                    $data['object'] = get_post($data['object_id']);
                    return $this->render(dirname(__FILE__) . '../../../views/question/question.php', $data);
                endif;    
            case 'initDynatreeConfigurationContest':
                return $this->initDynatreeConfigurationContest($data);
            case 'list_properties_item':
                $object_model = new ObjectModel();
                $data = $object_model->list_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type',array('fields'=>'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/item/list_properties_item.php', $data);
        }
    }

    /* function initCategoriesDynatreeDynamic() */
    /* receive ((array) data) */
    /* O dynatree dinamico do formulario de submissao */
    /* Author: Eduardo */

    public function initDynatreeConfigurationContest($data) {
        $counter = 0;
        $category_model = new CategoryModel;
        $facets = get_post_meta($data['collection_id'],'socialdb_collection_facets');
        if(is_array($facets)){
            foreach ($facets as $facet) {
                $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $facet . '_color', true);
                $classCss = ($classCss) ? $classCss : 'color4';
                $children = $category_model->getChildren($facet);
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $children_of_child = $category_model->getChildren($child->term_id);
                        if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {// se tiver descendentes
                            $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
                        } else {// se nao tiver filhos
                            $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
                        }
                        $counter++;
                        if ($counter == 9) {
                            $dynatree[] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $category_root_id . '_moreoptions', 'isLazy' => true, 'addClass' => 'more');
                            break;
                        }
                    }
                }
            }
        }
        
        
//        $category_root_id = $category_model->get_category_root_of($data['collection_id']);
//        $initial_term = get_term_by('id', $category_root_id , 'socialdb_category_type');
//        $classCss = get_post_meta($data['collection_id'], 'socialdb_collection_facet_' . $initial_term->term_id . '_color', true);
//        $classCss = ($classCss) ? $classCss : 'color4';
//        $children = $category_model->getChildren($category_root_id);
//        
//        if (count($children) > 0) {
//            foreach ($children as $child) {
//                $children_of_child = $category_model->getChildren($child->term_id);
//                if (count($children_of_child) > 0 || (!empty($children_of_child) && $children_of_child)) {// se tiver descendentes
//                    $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'isLazy' => true, 'addClass' => $classCss);
//                } else {// se nao tiver filhos
//                    $dynatree[] = array('title' => $child->name, 'hideCheckbox' => $hide_checkbox, 'key' => $child->term_id, 'addClass' => $classCss);
//                }
//                $counter++;
//                if ($counter == 9) {
//                    $dynatree[] = array('title' => __('See more', 'tainacan'), 'hideCheckbox' => true, 'key' => $category_root_id . '_moreoptions', 'isLazy' => true, 'addClass' => 'more');
//                    break;
//                }
//            }
//        }
        return json_encode($dynatree);
    }

}

/*
 * Controller execution
 */

if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$controller = new ItemController();
echo $controller->operation($operation, $data);
