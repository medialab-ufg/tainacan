<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '/oaipmh_model.php');

class OAIPMHRepositoryModel extends OAIPMHModel {
    
    /**
     *
     * Metodo que importa todos os SETSPECS para o banco de dados
     *
     * @param string $url_base A url base do repositorio de onde serao importadas os set specs
     * @param int $collection_id O id da colecao a qual sera vinculada
     * @return void
     */
    public function import_list_set_repository($data) {
        $url_base = $data['url'];
        session_write_close();
        ini_set('max_execution_time', '0');
        $collection_id = get_option('collection_root_id');
        $xml_list_set = $this->read_list_set($url_base);
        if ($xml_list_set) {
            $array_list_set = $this->parse_xml_set_to_array($xml_list_set);
            if(is_array($array_list_set)){
                foreach ($array_list_set as $slug => $name) {
                    //se o set estiver vazio ou se for igual ao slug ou nome de um dos sets
                    if($data['sets']==''||($data['sets']==trim($slug)||$data['sets']==trim($name))):
                        $this->insert_collection($slug, $name);
                    endif;
                }
            }
        }
    }
    
    /**
     * 
     * @param string $slug
     * @param string $name O nome da colecao
     */
    public function insert_collection($slug,$name){
        if(get_term_by('slug', $slug, 'socialdb_category_type'))
            wp_delete_term(get_term_by('slug', $slug, 'socialdb_category_type')->term_id, 'socialdb_category_type');
        //if(!get_term_by('slug', $slug, 'socialdb_category_type')){
            $collection_model = new CollectionModel;
            $collection = array(
                'post_type' => 'socialdb_collection',
                'post_title' => $name,
                'post_status' => 'publish',
                'post_author' => get_current_user_id(),
            );
           $collection_id = wp_insert_post($collection);
            $result = socialdb_insert_term($name, 'socialdb_category_type', $this->get_category_root(), $slug); 
            $type = get_term_by('name', 'socialdb_collection_public', 'socialdb_collection_type');
            wp_set_post_terms($collection_id, array($type->term_id), 'socialdb_collection_type');
            update_post_meta($collection_id, 'socialdb_collection_object_type',$result['term_id']); 
            $collection_model->insert_permissions_default_values($collection_id);
            //pegando a licaenca padrao do repositorio
            if (get_option('socialdb_pattern_licenses')) {
                update_post_meta($collection_id, 'socialdb_collection_license_pattern', get_option('socialdb_pattern_licenses'));
            }
        //}
    }
}

