<?php
require_once(dirname(__FILE__) . '../../general/general_model.php');
include_once (dirname(__FILE__) . '../../collection/collection_model.php');
include_once (dirname(__FILE__) . '../../property/property_model.php');
include_once (dirname(__FILE__) . '../../user/user_model.php');

class CommentModel extends Model {

    /**
     * function add($data)
     * @param mix $data  O id do colecao
     * @return json  
     * 
     * Autor: Eduardo Humberto 
     */
    public function add($object_id, $content, $parent = 0, $author_name = '', $author_email = '', $author_site = '', $user_id = 0,$term_id = '') {
        $commentdata = array(
            'comment_post_ID' => $object_id, // to which post the comment will show up
            'comment_author' => $author_name, //fixed value - can be dynamic 
            'comment_author_email' => $author_email, //fixed value - can be dynamic 
            'comment_author_url' => $author_site, //fixed value - can be dynamic 
            'comment_content' => $content, //fixed value - can be dynamic 
            'comment_type' => '', //empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
            'comment_parent' => $parent, //0 if it's not a reply to another comment; if it's a reply, mention the parent comment ID here
            'user_id' => $user_id, //passing current user ID or any predefined as per the demand
        );
        //Insert new comment and get the comment ID
        $comment_id = wp_new_comment($commentdata);
        if($term_id!=''){
            add_comment_meta( $comment_id, 'socialdb_is_comment_from', $term_id);
        }else{
            add_comment_meta( $comment_id, 'socialdb_is_comment_from', 'object');
        }
        if (!is_wp_error($comment_id) && $comment_id) {// se a tag foi inserida com sucesso
            $data['success'] = 'true';
            $data['comment_id'] = $comment_id;
        } else {
            $data['success'] = 'false';
            $data['msg'] = __('An unexpected error ocurred','tainacan');
        }
        return $data;
    }

    /**
     * function update($data)
     * @param mix $data  Os dados que serao utilizados para atualizar a colecao
     * @return json com os dados atualizados 
     * metodo que atualiza os dados da colecao
     * Autor: Eduardo Humberto 
     */
    public function update($comment_id, $comment_content) {
        $commentarr = array();
        $commentarr['comment_ID'] = trim($comment_id);
        $commentarr['comment_content'] = $comment_content;
        $result = wp_update_comment( $commentarr );
        if ($result==1) {// se a tag foi atualizada com sucesso
            $commentarr['success'] = 'true';
        } else {
            $commentarr['success'] = 'false';
            unset($commentarr['comment_ID']);
        }
        return $commentarr;
    }

    /* function delete() */
    /* @param array $data
      /* @return json com os dados da tag excluida.
      /* exclui a tag */
    /* @author Eduardo */

    public function delete($comment_id) {
        if (wp_delete_comment( $comment_id)) {
            $data['success'] = 'true';
        } else {
            $data['success'] = 'false';
        }
        return $data;
    }
      /**
     * function get_comment_json($data)
     * @param mix $data  Os dados que serao utilizados para buscar o comentario
     * @return json com os dados do comentario
     * metodo que atualiza os dados da colecao
     * Autor: Eduardo Humberto 
     */
    public function get_comment_json($comment_id) {
        $data['comment'] = get_comment($comment_id);
        return $data;
    }
    

}
