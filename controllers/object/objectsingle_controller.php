<?php

require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../../models/collection/collection_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/user/user_model.php');

require_once(dirname(__FILE__) . '../../../models/object/objectfile_model.php');

class ObjectSingleController extends Controller {

    public function operation($operation, $data) {
        $object_model = new ObjectModel();
        $objectfile_model = new ObjectFileModel;
        switch ($operation) {
            case 'verifyPublishedItem':
                $item = get_post($data['item_id']);
                $array['is_removed'] = ($item->post_status=='publish')? false : true;
                $array['url'] = get_the_permalink($data['collection_id']);
                return json_encode($array);
            case "show_classifications":
                $data = $object_model->show_classifications($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_object_classifications.php', $data);
                break;
            case "increment_collection_count":
                $collection_id = $data['collection_id'];
                $view_count = get_post_meta($collection_id, 'collection_view_count', true);
                if ( empty( $view_count ) ):
                    add_post_meta( $collection_id, 'collection_view_count', 1, true );
                else:
                    $updated = $view_count + 1;
                    update_post_meta( $collection_id, 'collection_view_count', $updated, $view_count );
                endif;
                break;
           // mostra propriedades preparando para um evento
            case 'list_properties':// mostra todas as propriedades com seus respectivos valores (aparece por default)
                $data = $object_model->list_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type',array('fields'=>'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_show_list_event_properties.php', $data);
                break;
            case 'list_properties_renew':
                $data = $object_model->list_properties($data);
                $data['categories_id'] = wp_get_object_terms($data['object_id'], 'socialdb_category_type',array('fields'=>'ids'));
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_show_list_event_properties_renew.php', $data);
            case 'list_properties_edit_remove':// pega todas as propriedade para serem mostradas no formulario de edicao e remocao
                $data = $object_model->list_properties($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_show_list_event_properties_edit_remove.php', $data);
                break;
            case "get_objects_by_property_json":// pega todos os objetos relacionado de uma propriedade e coloca em um array json
                return $object_model->get_objects_by_property_json($data);
            case "get_property_object_value":// retorna os valores para uma propriedade de objeto especificao
                return $object_model->get_property_object_value($data);
            case 'show_form_data_property':// mostra o formulario para insercao de propriedade de dados
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_data_property_form.php', $data);
            case 'show_form_object_property':// mostra o formulario para insercao de propriedade de objecto
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_object_property_form.php', $data);
            case 'show_form_term_property':
                $property_model = new PropertyModel();
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_term_property_form.php', $data);
            case 'show_edit_data_property_form':// mostra o formulario para EDICAO de propriedade de dados
                $property_model = new PropertyModel();
                $data['value'] = json_decode($property_model->edit_property($data));
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_edit_data_property_form.php', $data);
            case 'show_edit_object_property_form':// mostra o formulario para EDICAO de propriedade de OBJETOS
                $property_model = new PropertyModel();
                $data['value'] = json_decode($property_model->edit_property($data));
                $data = $property_model->list_data($data);
                return $this->render(dirname(__FILE__) . '../../../views/object/single_object/single_edit_object_property_form.php', $data);
            case 'insert_attachment':
                return $object_model->insert_attachment_event($data);
                break;
        }
    }

    /**
     * function get_author_name($author_id)
     * @param int $author_id o id do author
     * @return string Retorna o nome do usuario
     * 
     * @author: Eduardo Humberto 
     */
    public function get_author_name($author_id) {
        $object_model = new ObjectModel();
        return $object_model->get_object_author($author_id, 'name');
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

$object_controller = new ObjectSingleController();
echo $object_controller->operation($operation, $data);
?>
