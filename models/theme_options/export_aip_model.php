<?php
include_once dirname(__FILE__).'/export_aip_repository_model.php';
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
        return $meta->meta_id;
    }
}
/**
 *  CLasse de execucao 
 */
class ExportAIP extends ThemeOptionsModel {
    
    public $model;
    public $repository_model;
    public function __construct() {
        $this->model = new ExportAIPModel();
        $this->repository_model = new ExportAIPRepositoryModel();
    }
    /**
     * @signature - export_aip_zip($collection_id)
     * @description - funcao que exporta o repositorio para o formato AIP
     */
    public function export_aip_zip(){
        if(!is_dir($this->model->dir.'/'.$this->model->name_folder.'/')){
             mkdir($this->model->dir.'/'.$this->model->name_folder);
        }
        $this->repository_model->create_repository();
        $this->create_zip_by_folder($this->model->dir.'/', $this->model->name_folder.'/', $this->model->name_folder);
        $this->recursiveRemoveDirectory($this->model->dir.'/'.$this->model->name_folder);
        $this->model->download_send_headers($this->model->dir.'/'.$this->model->name_folder.'.zip');
    }
}
