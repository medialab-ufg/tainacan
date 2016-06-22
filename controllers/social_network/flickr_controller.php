<?php

require_once(dirname(__FILE__) . '../../../models/social_network/flickr_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');

class FlickrController extends Controller {

    public function operation($operation, $data) {
        switch ($operation) {
            //BEGIN New Code
            case "import_flickr_items":
                $config = get_option('socialdb_theme_options');
                $flickr = new FlickrModel($data['identifier'], $config);
                $object_model = new ObjectModel();
                $return = $flickr->insertFlickrItems($data, $object_model);
                if($return){
                     return json_encode($return);
                }else{
                    return json_encode([]);
                }
                break;
            
            
            //END New Code
            //*********************************************************************************************//
            case "getPhotosFlickr":
                $config = get_option('socialdb_theme_options');
                $flickr = new FlickrModel($data['identifier'], $config);
                $object_model = new ObjectModel();
                return $flickr->getAllPhotosFromUser($data, $object_model);
                break;

            case "updatePhotosFlickr":
                $config = get_option('socialdb_theme_options');
                $flickr = new FlickrModel($data['identifier'], $config);
                $object_model = new ObjectModel();
                return $flickr->peopleGetAllPublicPhotosUpdated($data, $object_model);
                break;

            case "insertIdentifierFlickr":
                return FlickrModel::insert_flickr_identifier($data['identifier'], $data['collectionId']);
                break;


            case "listIdentifiersFlickr":
                return FlickrModel::list_flickr_identifier($data['collectionId']);
                break;

            case "editIdentifierFlickr":
                return FlickrModel::edit_flickr_identifier($data['identifier'], $data['new_identifier']);
                break;

            case "deleteIdentifierFlickr":
                return FlickrModel::delete_flickr_identifier($data['identifier'], $data['collection_id']);
                break;
        }
    }

}

if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}
$flickr_controller = new FlickrController();
echo $flickr_controller->operation($operation, $data);
?>