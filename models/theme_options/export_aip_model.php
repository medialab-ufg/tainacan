<?php
/**
 * Model que realiza a exportacao do zip AIP do tainacan
 */
class ExportAIPModel extends ThemeOptionsModel {
    
    //Nome do folder onde sera gerado os arquivos
    public $name_folder = 'tainacan-aip';
    
    //nome do zip a ser gerado o mets do repositorio
    public $name_folder_repository = 'sitewide-aip';
    
    //caminho dos uploads do tainacan
    public $dir = TAINACAN_UPLOAD_FOLDER;
    
    //prefixo a ser utilizado na pastas
    public $prefix = 'ri';
    
    
    /**
     * @signature - download_send_headers($collection_id)
     * @param int 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
    function download_send_headers($filename) {
        $file_name = basename($filename);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize($filename));
        readfile($filename);
        unlink($filename);
        exit;
    }
    
    /**
     * metodo que retorna o id do meta dos moderadores de uma colecao
     * @param type $collection_id
     * @return type
     */
    public function get_moderators_collection_id($collection_id) {
        $meta = $this->sdb_get_post_meta_by_value($collection_id, 'socialdb_collection_moderator');
        if($meta){
            return (is_array($meta)) ? $meta[0]->meta_id : $meta->meta_id;
        }else{
            return $collection_id;
        }
    }
    
    /**
     * 
     * @global type $wpdb
     * @return array
     */
    public function  get_extendable_collections(){
        global $wpdb;
        $collection_parents = [];
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        $query = "
                    SELECT p.*,pm.meta_value FROM $wp_posts p
                    INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                    WHERE pm.meta_key LIKE 'socialdb_collection_parent' 
            ";
        $result = $wpdb->get_results($query);


        if ($result && is_array($result) && count($result) > 0) {
            foreach ($result as $value) {
                $category_root = get_term_by('id', trim($value->meta_value), 'socialdb_category_type');
                if($category_root && get_term_by('id', $category_root->parent,'socialdb_category_type')->name=='socialdb_category'){
                    $collection = $this->get_collection_by_category_root($category_root->term_id);
                    if (isset($collection[0]->post_title)) {
                        $collection_parents[$collection[0]->ID] = $collection[0];
                    }
                }
            }
            return $collection_parents;
        } else {
            return array();
        }
    }
    
    /**
     * 
     * @global type $wpdb
     * @return array
     */
    public function  get_children_collections($collection_id){
        global $wpdb;
        $collection_parents = [];
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        if($collection_id != get_option('collection_root_id')){
            $query = "
                        SELECT p.*,pm.meta_value FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE pm.meta_key LIKE 'socialdb_collection_parent' 
                ";
            $result = $wpdb->get_results($query);
            $category_root_id = $this->get_category_root_of($collection_id);

            if ($result && is_array($result) && count($result) > 0) {
                foreach ($result as $value) {
                    if(trim($value->meta_value) ==$category_root_id){
                        $collection_parents[] = $value;
                    }
                }
                return $collection_parents;
            } else {
                return array();
            }
        }else{
             $query = "
                        SELECT p.* FROM $wp_posts p  
                        WHERE p.post_type LIKE 'socialdb_collection' 
                ";
            $result = $wpdb->get_results($query);
            if ($result && is_array($result) && count($result) > 0) {
                foreach ($result as $value) {
                    $meta = get_post_meta($value->ID, 'socialdb_collection_parent' , true);
                    if((!$meta || $meta=='' || !is_numeric($meta)) && $value->ID != $collection_id  ){
                        $collection_parents[] = $value;
                    }
                }
                return $collection_parents;
            } else {
                return array();
            }
        }
    }
}
/**
 *  CLasse de execucao 
 */
class ExportAIP extends ThemeOptionsModel {
    
    public $model;
    public $repository_model;
    public $community_model;
    public $collection_model;
    public $item_model;
    public function __construct() {
        $this->model = new ExportAIPModel();
        $this->repository_model = new ExportAIPRepositoryModel();
        $this->community_model = new ExportAIPCommunityModel();
        $this->collection_model = new ExportAIPCollectionModel();
        $this->item_model = new ExportAIPItemModel();
    }
    /**
     * @signature - export_aip_zip($collection_id)
     * @description - funcao que exporta o repositorio para o formato AIP
     */
    public function export_aip_zip(){
        error_reporting(0);
        if(!is_dir($this->model->dir.'/'.$this->model->name_folder.'/')){
             mkdir($this->model->dir.'/'.$this->model->name_folder);
        }
        $this->repository_model->create_repository();
        $this->community_model->create_communities();
        $this->collection_model->create_collections();
        $this->item_model->create_items();
        $this->create_zip_by_folder($this->model->dir.'/', $this->model->name_folder.'/', $this->model->name_folder,true);
        $this->recursiveRemoveDirectory($this->model->dir.'/'.$this->model->name_folder);
        $this->model->download_send_headers($this->model->dir.'/'.$this->model->name_folder.'.zip');
    }
    
    /**
     * 
     * @return type
     */
    public function get_info_export_aip($data) {
        $return['total_community'] = (!isset($data['total_community'])) ? $this->community_model->get_count_communities() : $data['total_community']; 
        $return['total_collection'] =  (!isset($data['total_collection'])) ? $this->collection_model->get_count_collections() : $data['total_collection']; 
        $return['total_item'] =  (!isset($data['total_item'])) ? $this->item_model->get_count_items() : $data['total_item']; 
        $return['total'] = $return['total_community'] + $return['total_collection'] + $return['total_item'];
        $return['found_community'] = $this->search_files_name('COMMUNITY@');
        $return['found_collection'] = $this->search_files_name('COLLECTION@');
        $return['found_item'] = $this->search_files_name('ITEM@');
        $return['exported'] = $return['found_community'] + $return['found_collection'] + $return['found_item'];
        $return['percent'] = ($return['exported'] / $return['total']) * 100;
        if($return['exported'] >= $return['total'] ){
            $return['close'] = true;
        }
        return json_encode($return);
    }
    
    /**
     * 
     * @param type $name
     * @return int
     */
    public function search_files_name($name) {
        $index = 0;
        $dir = TAINACAN_UPLOAD_FOLDER.'/tainacan-aip';
        if (is_dir($dir)) {
            foreach (glob("{$dir}/*.zip") as $file) {
                if(strpos($file, $name)!==false){
                    $index++;
                }
            }
        }
        return $index;
    }
}

include_once dirname(__FILE__).'/export_aip_repository_model.php';
include_once dirname(__FILE__).'/export_aip_community_model.php';
include_once dirname(__FILE__).'/export_aip_collection_model.php';
include_once dirname(__FILE__).'/export_aip_item_model.php';
