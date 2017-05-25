<?php
/*
include_once (dirname(__FILE__) . '/../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../wp-includes/wp-db.php');
*/
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');
require_once(dirname(__FILE__) . '../../user/user_model.php');
require_once(dirname(__FILE__) . '../../ranking/ranking_model.php');

abstract class EventModel extends Model {

    var $parent;
    var $permission_name;
    
    /**
     * function list_events($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados a serem montados na view
     * 
     * @author: Eduardo Humberto 
     */
    public static function list_events_notification($data) {
        $collectionModel = new CollectionModel;

        $data['moderation_type'] = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_type', true);
        $data['moderation_type'] = (empty($data['moderation_type']) ? 'moderador' : $data['moderation_type']);
        if ($data['moderation_type'] == 'democratico') {
            $data['moderation_days'] = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_days', true);
            $collection_events = EventModel::list_all_events_terms($data);
        } else {
            if (current_user_can('manage_options') || $collectionModel->is_moderator($data['collection_id'], get_current_user_id())) {
                $collection_events = EventModel::list_all_events_terms($data);
            } else {
                $collection_events = EventModel::list_all_events_by_user($data);
            }
        }

        $ranking_model = new RankingModel;

        if (!empty($collection_events)) {
            foreach ($collection_events as $event) {
                $info['state'] = get_post_meta($event->ID, 'socialdb_event_confirmed', true);
                $info['name'] = $event->post_title;
                $info['date'] = get_post_meta($event->ID, 'socialdb_event_create_date', true);
                $info['type'] = EventModel::get_type($event);
                $info['id'] = $event->ID;
                $info['dump'] = $collectionModel->get_collection_by_user(get_current_user_id());
                if ($data['moderation_type'] == 'democratico') {
                    $info['democratic_vote_id'] = get_post_meta($event->ID, 'socialdb_event_democratic_vote_id', true);
                    $count = $ranking_model->count_votes_binary($info['democratic_vote_id'], $event->ID);
                    $info['count_up'] = $count['count_up'];
                    $info['count_down'] = $count['count_down'];
                }
                if ($info['state'] == '') {
                    $data['events_not_observed'][] = $info;
                } else {
                    $data['events_observed'][] = $info;
                }
            }
        }

        return $data;
    }
    /**
     * function list_events($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados a serem montados na view
     * 
     * @author: Eduardo Humberto 
     */
    public static function list_events($data) {
        $collectionModel = new CollectionModel;

        $data['moderation_type'] = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_type', true);
        $data['moderation_type'] = (empty($data['moderation_type']) ? 'moderador' : $data['moderation_type']);
        if ($data['moderation_type'] == 'democratico') {
            $data['moderation_days'] = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_days', true);
            $collection_events = EventModel::list_all_events_terms($data);
        } else {
            if (current_user_can('manage_options') || $collectionModel->is_moderator($data['collection_id'], get_current_user_id())) {
                $collection_events = EventModel::list_all_events_terms($data);
            } else {
                $collection_events = EventModel::list_all_events_by_user($data);
            }
        }

        $ranking_model = new RankingModel;

        if (!empty($collection_events)) {
            foreach ($collection_events as $event) {
                $info['state'] = get_post_meta($event->ID, 'socialdb_event_confirmed', true);
                $info['name'] = $event->post_title;
                $info['date'] = get_post_meta($event->ID, 'socialdb_event_create_date', true);
                $info['type'] = EventModel::get_type($event);
                $info['id'] = $event->ID;
                if ($data['moderation_type'] == 'democratico') {
                    $info['democratic_vote_id'] = get_post_meta($event->ID, 'socialdb_event_democratic_vote_id', true);
                    $count = $ranking_model->count_votes_binary($info['democratic_vote_id'], $event->ID);
                    $info['count_up'] = $count['count_up'];
                    $info['count_down'] = $count['count_down'];
                }
                if ($info['state'] == '') {
                    $data['events_not_observed'][] = $info;
                } else {
                    $data['events_observed'][] = $info;
                }
            }
        }

        return $data;
    }

    public static function get_user_notifications() {
        $collectionModel = new CollectionModel();
        $_sum = 0;
        $_root_id = get_option('collection_root_id');
        foreach($collectionModel->get_collection_by_user(get_current_user_id()) as $col) {
            $d['collection_id'] = $col->ID;
            $info['colecao'] = $col->post_title;
            if($col->ID == $_root_id) {
                $info['is_root'] = true;
                $info['colecao'] = _t('Repository\'s events');
            }
            $info['counting'] = count(EventModel::list_events($d)['events_not_observed']);
            $info['path'] = $col->post_name;
            if($info['counting'] > 0) {
                $_sum += $info['counting'];
                $_collections_evts['evts'][] = $info;
            }
        }
        $_collections_evts['total_evts'] = $_sum;

        return $_collections_evts;
    }

    /**
     * function list_events($data)
     * @param array $data Os dados vindos do formulario
     * @return array com os dados a serem montados na view
     * 
     * @author: Eduardo Humberto 
     */
    public static function get_event($data) {
        $userModel = new UserModel();
        $ranking_model = new RankingModel;
        $event = get_post($data['event_id']);
        $terms = wp_get_object_terms($event->ID, 'socialdb_event_type');
        $info['state'] = get_post_meta($event->ID, 'socialdb_event_confirmed', true);
        $info['observation'] = get_post_meta($event->ID, 'socialdb_event_observation', true);
        $info['operation'] = get_term_by('id', $terms[0]->term_id, 'socialdb_event_type')->name;
        $info['name'] = $event->post_title;
        $info['date'] = date("d/m/Y", get_post_meta($event->ID, 'socialdb_event_create_date', true));
        $info['type'] = EventModel::get_type($event);
        $info['id'] = $event->ID;
        $author = get_post_meta($event->ID, 'socialdb_event_user_id', true);
        $info['author'] = $userModel->get_user($author)['name'];
        $info['author_id'] = $userModel->get_user($author)['id'];
        //se for democratico
        $moderation_type = get_post_meta($data['collection_id'], 'socialdb_collection_moderation_type', true);
        $moderation_type= (empty($moderation_type) ? 'moderador' : $moderation_type);
        if ($moderation_type == 'democratico') {
            $info['democratic_vote_id'] = get_post_meta($event->ID, 'socialdb_event_democratic_vote_id', true);
            $count = $ranking_model->count_votes_binary($info['democratic_vote_id'], $event->ID);
            $info['count_up'] = $count['count_up'];
            $info['count_down'] = $count['count_down'];
        }
        if(in_array($info['operation'], ['socialdb_event_object_create','socialdb_event_object_edit','socialdb_event_object_delete'])){
            $collection_id  = get_post_meta($event->ID, 'socialdb_event_collection_id', true);
            $link = get_the_permalink($collection_id). get_post(get_post_meta($event->ID, 'socialdb_event_object_item_id', true))->post_name;
            $info['link'] = '<br><a target="_blank" href="'.$link.'">'.__('See item','tainacan').'</a>';
        }
        return json_encode($info);
    }

    /**
     * function list_all_events_terms($data)
     * @param array $data Os dados vindos do formulario
     * @return array com todas os eventos da colecao em questao
     * 
     * @author: Eduardo Humberto 
     */
    public static function list_all_events_terms($data) {
        global $wpdb;
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                SELECT p.* FROM $wp_posts p
                INNER JOIN $wp_postmeta pm on pm.post_id = p.ID
                WHERE pm.meta_key LIKE 'socialdb_event_collection_id' AND pm.meta_value LIKE '{$data['collection_id']}'
                ORDER BY pm.meta_id DESC
        ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count($result) > 0) {
            return $result;
        } else {
            return array();
        }
    }

    /**
     * function list_all_events_terms($data)
     * @param array $data Os dados vindos do formulario
     * @return array com todas os eventos da colecao em questao
     * 
     * @author: Eduardo Humberto 
     */
    public static function list_all_events_by_user($data) {
        $collection_events_mine = array();
        $collection_events = EventModel::list_all_events_terms($data);
        if (!empty($collection_events)) {
            foreach ($collection_events as $event) {
                $terms = wp_get_object_terms($event->ID, 'socialdb_event_type');
                $parent = get_term_by('id', $terms[0]->term_taxonomy_id, 'socialdb_event_type');
                $classifications = array('socialdb_event_classification_create', 'socialdb_event_classification_delete');
                $prop_data = array('socialdb_event_property_data_edit_value');
                $prop_object = array('socialdb_event_property_object_edit_value');
                $comment_create = array('socialdb_event_comment_create');
                $comment_edit = array('socialdb_event_comment_edit');
                $comment_delete = array('socialdb_event_comment_delete');
                if (in_array($parent->name, $classifications)) {
                    $object = get_post_meta($event->ID, 'socialdb_event_classification_object_id', true);
                    if (get_post($object)->post_author == get_current_user_id()) {
                        $collection_events_mine[] = $event;
                    }
                } elseif (in_array($parent->name, $prop_data)) {
                    $object = get_post_meta($event->ID, 'socialdb_event_property_data_edit_value_object_id', true);
                    if (get_post($object)->post_author == get_current_user_id()) {
                        $collection_events_mine[] = $event;
                    }
                } elseif (in_array($parent->name, $prop_object)) {
                    $object = get_post_meta($event->ID, 'socialdb_event_property_object_edit_object_id', true);
                    if (get_post($object)->post_author == get_current_user_id()) {
                        $collection_events_mine[] = $event;
                    }
                } elseif (in_array($parent->name, $comment_create)) {
                    $object = get_post_meta($event->ID, 'socialdb_event_comment_create_object_id', true);
                    if (get_post($object)->post_author == get_current_user_id()) {
                        $collection_events_mine[] = $event;
                    }
                } elseif (in_array($parent->name, $comment_edit)) {
                    $comment_raw = get_post_meta($event->ID, 'socialdb_event_comment_edit_id', true);
                    $comment = get_comment($comment_raw);
                    if (get_post($comment->comment_post_ID)->post_author == get_current_user_id()) {
                        $collection_events_mine[] = $event;
                    }
                } elseif (in_array($parent->name, $comment_delete)) {
                    $comment_raw = get_post_meta($event->ID, 'socialdb_event_comment_delete_id', true);
                    $comment = get_comment($comment_raw);
                    if (get_post($comment->comment_post_ID)->post_author == get_current_user_id()) {
                        $collection_events_mine[] = $event;
                    }
                }
            }
        }
        return $collection_events_mine;
    }

    /**
     * function get_type($data)
     * @param wp_term $event O evento que queremos pegar o evento
     * @return string com todas os eventos da colecao em questao
     * 
     * @author: Eduardo Humberto 
     */
    public static function get_type($event) {
        $terms = socialdb_relations_item($event->ID, 'socialdb_event_type');
        if(isset($terms[0]) && isset($terms[0]->term_id) ){
            $parent = get_term_by('id', $terms[0]->term_id, 'socialdb_event_type');
            switch ($parent->name) {
                case 'socialdb_event_object_create':
                    return __('Create Object', 'tainacan');
                case 'socialdb_event_object_delete':
                    return __('Delete Object', 'tainacan');
                case 'socialdb_event_classification_create':
                    return __('Add Classification', 'tainacan');
                case 'socialdb_event_classification_delete':
                    return __('Delete Classification', 'tainacan');
                case 'socialdb_event_term_delete':
                    return __('Delete Category', 'tainacan');
                case 'socialdb_event_term_create':
                    return __('Create Category', 'tainacan');
                case 'socialdb_event_term_edit':
                    return __('Edit Category', 'tainacan');
                case 'socialdb_event_tag_delete':
                    return __('Delete Tag', 'tainacan');
                case 'socialdb_event_tag_create':
                    return __('Create Tag', 'tainacan');
                case 'socialdb_event_tag_edit':
                    return __('Edit Tag', 'tainacan');
                case 'socialdb_event_property_data_delete':
                    return __('Delete Data Property', 'tainacan');
                case 'socialdb_event_property_data_create':
                    return __('Create Data Property', 'tainacan');
                case 'socialdb_event_property_data_edit':
                    return __('Edit Data Property', 'tainacan');
                case 'socialdb_event_property_data_edit_value':
                    return __('Edit Data Property value', 'tainacan');
                case 'socialdb_event_property_object_delete':
                    return __('Delete Object Property', 'tainacan');
                case 'socialdb_event_property_object_create':
                    return __('Create Object Property', 'tainacan');
                case 'socialdb_event_property_object_edit':
                    return __('Edit Object Property', 'tainacan');
                case 'socialdb_event_property_object_edit_value':
                    return __('Edit Object Property value', 'tainacan');
                 case 'socialdb_event_property_compound_create':
                    return __('Create Compound Property', 'tainacan');
                case 'socialdb_event_property_compound_edit':
                    return __('Edit Compound Property', 'tainacan'); 
                    
                    
                case 'socialdb_event_property_term_delete':
                    return __('Delete Property', 'tainacan');
                case 'socialdb_event_property_term_create':
                    return __('Create Term Property', 'tainacan');
                case 'socialdb_event_property_term_edit':
                    return __('Edit Term Property', 'tainacan');
                case 'socialdb_event_collection_delete':
                    return __('Delete Collection', 'tainacan');
                case 'socialdb_event_collection_create':
                    return __('Create Collection', 'tainacan');
                case 'socialdb_event_comment_create':
                    return __('Create Comment', 'tainacan');
                case 'socialdb_event_comment_edit':
                    return __('Edit Comment', 'tainacan');
                case 'socialdb_event_comment_delete':
                    return __('Delete Comment', 'tainacan');
            }
        }else{
            return  __('Operation', 'tainacan');
        }
    }

    /**
     * function generate_title($data)
     * @param array $data Os dados vindos do formulario
     * @return string O titulo do evento em questao 
     * 
     * Autor: Eduardo Humberto 
     */
    abstract public function generate_title($data);

    /**
     * function  verify_event($data)
     * @param array $data Os dados vindos do formulario
     * @param array $automatically_verified Se foi automaticamente verificada
     * @return json com os dados 
     * 
     * Autor: Eduardo Humberto 
     */
    abstract public function verify_event($data, $automatically_verified = false);

    /**
     * function insert_term($data)
     * @param string $name  O nome do evento
     * @return ara  
     * 
     * Autor: Eduardo Humberto 
     */
    public function insert_event($name) {
        $name = str_replace('?', '', $name);
        $name = str_replace('.', '', $name);
//$name = remove_accent($name);
// $name = cut_string($name, 190);
        $post = array(
            'post_title' => $name,
            'post_status' => 'publish',
            'post_type' => 'socialdb_event'
        );
        $new_event['ID'] = wp_insert_post($post);
        wp_set_object_terms($new_event['ID'], array((int) $this->parent->term_id), 'socialdb_event_type');
//$new_event = wp_insert_term($name, 'socialdb_event_type', array('parent' => $this->parent->term_id, 'slug' => $this->generate_slug($name, rand(0, 50))));
        return $new_event;
    }

    /**
     * function instantiate_metas_event($data)
     * @param string $id  O id do evento que foi criado
     * @param string $name  O nome do pai do evento
     * @param array $data  Array com os dados vindos do formulario com os nomes iguais aos campos no banco de dados
     * @return void  funcao apenas para insercao dos meta dados dos eventos
     * 
     * Autor: Eduardo Humberto 
     */
    public function instantiate_metas_event($id, $name, $data) {
        global $wpdb;
        $term = get_term_by('name', $name, 'socialdb_event_type');
        if ($term) {
            $metas = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}termmeta"
                    . " WHERE meta_key like '{$name}_metas' ");
            if (is_array($metas)) {
                foreach ($metas as $meta) {
                    update_post_meta($id, $meta->meta_value, $data[$meta->meta_value]);
//create_metas($id, $meta->meta_value, $data[$meta->meta_value], $data[$meta->meta_value]);
                }
            }
            $parent = get_term_by('id', $term->parent, 'socialdb_event_type');
            $this->instantiate_metas_event($id, $parent->name, $data);
        } else {
            return;
        }
    }

    /**
     * function create_event($data)
     * @param array $data Os dados vindos do formulario com os nomes iguais aos campos do evento no banco de dados
     * @return json para a view  
     * 
     * Autor: Eduardo Humberto 
     */
    public function create_event($data) {
        $title = $this->generate_title($data); // gera o titulo para o evento 
        $this->notificate_moderators_email($data['socialdb_event_collection_id'], $title);
        $event_created = $this->insert_event($title); // insere o termo do evento no banco
        $this->create_democratic_vote(array('event_id' => $event_created['ID'], 'collection_id' => $data['socialdb_event_collection_id']));
        if ($event_created && $event_created['ID']) { // se criou com sucesso
            $this->instantiate_metas_event($event_created['ID'], $this->parent->name, $data); // instancia e coloca os valores nos meta dados do evento
            if ($this->is_automatically_verify_event($data['socialdb_event_collection_id'], $this->permission_name, $data['socialdb_event_user_id'], $event_created['ID'])) {
                $data['event_id'] = $event_created['ID'];
                $data = $this->verify_event($data, true);
                return $data;
            } elseif (isset($data['socialdb_event_delete_collection_id']) && get_post($data['socialdb_event_delete_collection_id'])->post_author == get_current_user_id()) {
                $data['event_id'] = $event_created['ID'];
                $data = $this->verify_event($data, true);
                return $data;
            } else {
                if (isset($data['socialdb_event_observation']) && $data['socialdb_event_observation'] !== '') {
                    update_post_meta($event_created['ID'], 'socialdb_event_observation', $data['socialdb_event_observation']);
                } elseif ($this->parent->name == 'socialdb_event_object_create') {
                    $object = get_post($data['socialdb_event_object_item_id']);
                    $url = '<a target="_blanck" href="' . get_the_permalink($data['socialdb_event_collection_id']) . '?item=' . $object->post_name . '">' . __('See item (Open a new Tab)') . '</a>';
                    update_post_meta($event_created['ID'], 'socialdb_event_observation', $url);
                }
                $data['msg'] = __('The event was sent for approval', 'tainacan');
                $data['type'] = 'info';
                $data['title'] = __('Attention', 'tainacan');
            }
        } else {
            $data['msg'] = __('An error happened, please try again', 'tainacan');
            $data['type'] = 'error';
            $data['title'] = 'Erro';
        }
        return json_encode($data);
    }

    public function create_democratic_vote($data) {
        $type = get_term_by('name', 'socialdb_property_ranking_binary', 'socialdb_property_type');
        $data['ranking_name'] = 'event_democratic_' . time();

        if (!empty($data['ranking_name'])) {
            $new_ranking = wp_insert_term($data['ranking_name'], 'socialdb_property_type', array('parent' => $type->term_id,
                'slug' => $this->generate_slug($data['ranking_name'], $data['collection_id'])));
            $ranking_id = $new_ranking['term_id'];
            $data['rankind_id'] = $ranking_id;
        }

        if (!is_wp_error($new_ranking) && $ranking_id) {
            instantiate_metas($ranking_id, $type->name, 'socialdb_property_type', true);
//possivelmente um problema
            add_post_meta($data['event_id'], 'socialdb_property_' . $ranking_id, '');
            add_post_meta($data['event_id'], 'socialdb_event_democratic_vote_id', $ranking_id);
        }
    }

    /**
     * metodo que verifica se acao pode ser executada
     *
     * funcao que verifica se o evento deve ser verificado automaticamente
     * @param int $collection_id O id da colecao que sera feito o evento
     * @param string $action A acao que sera executada
     * @param int $user_id O id do usuario, caso seja anonimo, sera 0
     * @return boolean true se deve confirmar o evento e false caso nao deva 
     * 
     * @author Eduardo Humberto 
     */
    public function is_automatically_verify_event($collection_id, $action, $user_id, $event_id = 0) {
        if (has_filter('tainacan_alter_permission_actions')) {
            return apply_filters('tainacan_alter_permission_actions',$action, $collection_id, $event_id);
        }

        $is_repository_action = in_array($action, ['socialdb_collection_permission_create_collection', 'socialdb_collection_permission_delete_collection']);
        if ($collection_id == get_option('collection_root_id') && $is_repository_action) {
            $options = get_option('socialdb_repository_permissions');
            $permission = $options[$action];
        } else {
            $permission = get_post_meta($collection_id, $action, true);
        }

        // se for permissoes do repositorio
        if ($is_repository_action) {
            if (!isset($permission) || $permission == 'approval') {
                if (CollectionModel::is_moderator($collection_id, $user_id) || current_user_can('manage_options')) {
                    return true;
                } else {
                    return false;
                }
            } elseif ($permission == 'members') {
                if ($user_id > 0) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
//demais acoes para colecoes
        if ($permission == 'approval') {
            if (CollectionModel::is_moderator($collection_id, $user_id) || current_user_can('manage_options')) {
                return true;
            } else {
                $metadata_actions = [
                    'socialdb_collection_permission_create_property_data',
                    'socialdb_collection_permission_edit_property_data',
                    'socialdb_collection_permission_delete_property_data',
                    'socialdb_collection_permission_create_property_object',
                    'socialdb_collection_permission_edit_property_object',
                    'socialdb_collection_permission_delete_property_object',
                    'socialdb_collection_permission_create_property_term',
                    'socialdb_collection_permission_edit_property_term',
                    'socialdb_collection_permission_delete_property_term'
                ];
                if (in_array($action, ['socialdb_collection_permission_edit_category', 'socialdb_collection_permission_delete_category'])) {
                    $category_id = get_post_meta($event_id, 'socialdb_event_term_id', true);
                    if (strpos($category_id, '_facet_category') !== false) {
                        $category_id = str_replace('_facet_category', '', $category_id);
                    }
                    $moderators = get_term_meta($category_id, 'socialdb_category_moderators');
                    if (is_array($moderators) && in_array($user_id, $moderators)) {
                        return true;
                    } else {
                        return false;
                    }
                } else if (in_array($action, $metadata_actions)) {
                    $category_id = $this->get_category_of($event_id);
                    $moderators = get_term_meta($category_id, 'socialdb_category_moderators');
                    $owner = get_term_meta($category_id, 'socialdb_category_owner', true);
                    if ((is_array($moderators) && in_array($user_id, $moderators)) || ($owner && $owner == $user_id)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    $is_item = get_post_meta($event_id,'socialdb_event_object_item_id',true);
                    if( get_post($is_item) && get_post($is_item)->post_author == get_current_user_id()){
                        return true;
                    }
                    return false;
                }
            }
        } elseif ($permission == 'members') {
            if ($user_id > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * funcao que busca a categoria dos eventos de criacao de metadados
     * @param int O id do evento
     * @return void
     * 
     * @author Eduardo Humberto 
     */
    public function get_category_of($event_id) {
        if (get_post_meta($event_id, 'socialdb_event_property_term_create_category_root_id', true)) {
            return get_post_meta($event_id, 'socialdb_event_property_term_create_category_root_id', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_data_create_category_root_id', true)) {
            return get_post_meta($event_id, 'socialdb_event_property_data_create_category_root_id', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_object_create_category_root_id', true)) {
            return get_post_meta($event_id, 'socialdb_event_property_object_create_category_root_id', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_data_edit_id', true)) {
            $id = get_post_meta($event_id, 'socialdb_event_property_data_edit_id', true);
            return get_term_meta($id, 'socialdb_property_created_category', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_data_delete_id', true)) {
            $id = get_post_meta($event_id, 'socialdb_event_property_data_delete_id', true);
            return get_term_meta($id, 'socialdb_property_created_category', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_object_edit_id', true)) {
            $id = get_post_meta($event_id, 'socialdb_event_property_object_edit_id', true);
            return get_term_meta($id, 'socialdb_property_created_category', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_object_delete_id', true)) {
            $id = get_post_meta($event_id, 'socialdb_event_property_object_delete_id', true);
            return get_term_meta($id, 'socialdb_property_created_category', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_term_edit_id', true)) {
            $id = get_post_meta($event_id, 'socialdb_event_property_term_edit_id', true);
            return get_term_meta($id, 'socialdb_property_created_category', true);
        } else if (get_post_meta($event_id, 'socialdb_event_property_term_delete_id', true)) {
            $id = get_post_meta($event_id, 'socialdb_event_property_term_delete_id', true);
            return get_term_meta($id, 'socialdb_property_created_category', true);
        } else {
            return false;
        }
    }

    /**
     * funcao que atualiza o estado do evento
     * @param string O estado que deseja colocar
     * @param int O id do evento
     * @return void
     * 
     * @author Eduardo Humberto 
     */
    public function update_event_state($state, $event_id) {
        update_post_meta($event_id, 'socialdb_event_confirmed', $state);
    }

    /**
     * funcao que atualiza os metas necessarias para a aprovacao do evento
     * @param int O id do evento
     * @param string A observacao do evento
     * @param boolean Se e verificada automaticamente
     * @return void
     * 
     * @author Eduardo Humberto 
     */
    public function set_approval_metas($event_id, $observation, $automatically_verified) {
        update_post_meta($event_id, 'socialdb_event_approval_date', mktime());
        update_post_meta($event_id, 'socialdb_event_approval_by', get_current_user_id());
        if ($automatically_verified) {
            update_post_meta($event_id, 'socialdb_event_observation', __('Event automatically verified', 'tainacan'));
        } else {
            update_post_meta($event_id, 'socialdb_event_observation', $observation);
        }
    }

    /**
     * funcao que atualiza os metas necessarias para a aprovacao do evento
     * @param int O id do evento
     * @param string A observacao do evento
     * @param boolean Se e verificada automaticamente
     * @return void
     * 
     * @author Eduardo Humberto 
     */
    public function notificate_user_email($collection_id, $user_id, $event_id) {
        $user = get_user_by('id', 1);
        if ($user_id == 0)
            $user2 = false;
        else {
            $user2 = get_user_by('id', $user_id);
            $user2 = $user2->data->user_email;
            $emails = $user2;
        }
        $collection = get_post($collection_id);
        $title = get_post($event_id)->post_title;
        if ($user2) {
            $meta_event = get_post_meta($event_id);
            $matter = get_bloginfo('name') . ' ' . __("Messages: Result of your event", 'tainacan');
            $body = '
					<html>
					<head>
					   <title>' . __("Event result", 'tainacan') . '</title>
					</head>
					<body>
					<h3>' . __("Event in the collection ", 'tainacan') . ' <a href="' . get_the_permalink($collection->ID) . '">' . $collection->post_title . '</a></h3>                    <br>
					<p>					';
            if ($meta_event['socialdb_event_confirmed'][0] == 'confirmed')
                $body.='<b>' . __("The event ", 'tainacan') . ': (' . $title . ')	' . __(" was confirmed ", 'tainacan') . '';
            else {
                $body.='<b>' . __("The event ", 'tainacan') . ' (' . $title . ') ' . __("was not confirmed ", 'tainacan') . '</br>';
                if (isset($meta_event['socialdb_event_observation'][0]))
                    $body.='<br><b>' . __("Obervations by collection's moderators ", 'tainacan') . ': ' . $meta_event['socialdb_event_observation'][0] . '</br>';
            }

            $body.=
                    '</p>
					</body>
					</html>
					';
//para o envio em formato HTML
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html;
			charset=UTF-8\r\n";
//endereço do remitente
            $headers .= "From: Admin <" . $user->data->user_email . ">\r\n";
//endereço de resposta, se queremos que seja diferente a do remitente
//$headers .= "Reply-To: mariano@desarrolloweb.com\r\n";
//endereços que receberão uma copia $headers .= "Cc: manel@desarrolloweb.com\r\n"; 
//endereços que receberão uma copia oculta
//$headers .= "Bcc: vinnie@criarweb.com,joao@criarweb.com\r\n";
// wp_mail($emails, $matter, $body, $headers);
        }
    }

    /**
     * funcao que ENVIA OS EMAILS PARA OS administradores
     * @param int O id da colecao
     * @param string O titulo do evento
     * @param boolean Se e verificada automaticamente
     * @return void
     * 
     * @author Eduardo Humberto 
     */
    public function notificate_moderators_email($collection_id, $title) {
        $user = get_user_by('id', 1);
        $emails = $this->owner_emails($collection_id);
        $user_id = get_current_user_id();
        if ($user_id == 0)
            $user2 = __('Anonimous', 'tainacan');
        else {
            $user2 = get_user_by('id', $user_id);
            $user2 = $user2->user_nicename;
        }

        $collection = get_post($collection_id);
        $permission = get_post_meta($collection_id, $this->permission_name, true);

        /** ################################################################################################## * */
        if (($permission == "member" && is_user_logged_in()) || $permission == "anonimous")
            $matter = get_bloginfo('name') . " " . __('Warning: Event Approved', 'tainacan');
        else
            $matter = get_bloginfo('name') . " " . __('Warning: Event sent to Approvation', 'tainacan');
//
        $body = '
					<html>
					<head>
					   <title>' . __('Events', 'tainacan') . '</title>
					</head>
					<body>
					<h3>' . __('Event in collection ', 'tainacan') . ' <a href="' . get_the_permalink($collection->ID) . '">' . $collection->post_title . '</a></h3>                   '
                . ' <br> ' . __('Author User', 'tainacan') . ' : ' . $user2 . '					<br>
					<p>					';


        $body.='<b>' . __("Description: ", 'tainacan') . ': (' . $title . ')	';
        $body.=
                '</p>
					</body>
					</html>
					';
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Admin <" . $user->data->user_email . ">\r\n";
// wp_mail($emails, $matter, $body, $headers);
    }

    /**
     * funcao que busca os emails de todos os moderadores
     * @param int O id da collecao
     * @param array Se e verificada automaticamente
     * @return void
     * 
     * @author Eduardo Humberto 
     */
    public function owner_emails($collection_id) {
        $emails = [];
        $moderators = CollectionModel::get_moderators($collection_id);
        foreach ($moderators as $moderator) {
            $user = get_user_by('id', $moderator);
            $emails[] = $user->data->user_email;
        }
        return $emails;
    }

}
