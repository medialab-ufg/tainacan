<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
require_once(dirname(__FILE__) . '../../../models/tag/tag_model.php');
require_once(dirname(__FILE__) . '../../../controllers/general/general_controller.php');

class TagController extends Controller {

    public function operation($operation, $data) {
        $tag_model = new TagModel();
        switch ($operation) {
            case 'get_tag':
                $hash = get_term_meta( str_replace('_tag', '', $data['tag_id']) , 'socialdb_term_synonyms', true);
                return json_encode(['term'=>  get_term_by('id', $data['tag_id'],'socialdb_tag_type'),'socialdb_term_synonyms'=> ($hash&&$hash!=='') ? $tag_model->get_categories_hash($hash):[]]);
            case 'page':
                        $slug = ($data['slug_tag']=='tag_facet_tag')?'socialdb_tag':$data['slug_tag'];
                        $term = get_term_by('slug', $slug, 'socialdb_tag_type') ;
                        if ($term) {
                            $data['term'] = $term;
                            $data['parent'] = get_term_by('id', $term->parent, 'socialdb_tag_type') ;
                            $array_json['html'] = $this->render(dirname(__FILE__) . '../../../views/tag/page.php', $data);
                            return json_encode($array_json);
                        }else{
                            $array_json['title'] = __('Attention!','tainacan');
                            $array_json['error'] = __('Tag removed!','tainacan');
                            return json_encode($array_json);
                        }
                        break;
            case 'get_link_individuals':
                        return $tag_model->get_link_individuals($data['term_id'],$data['collection_id'],'tag');
        }
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

$comment_controller = new TagController();
echo $comment_controller->operation($operation, $data);



