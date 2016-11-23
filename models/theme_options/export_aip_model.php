<?php
include_once dirname(__FILE__).'/export_aip_repository_model.php';
/**
 * Model que realiza a exportacao do zip AIP do tainacan
 */
class ExportAIPModel extends ThemeOptionsModel {
    public $name_folder = 'tainacan-aip';
    public $name_folder_repository = 'sitewide-aip';
    public $dir = TAINACAN_UPLOAD_FOLDER;
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
        $this->create_zip_by_folder(dirname(__FILE__).'/');
        $this->create_zip_by_folder($this->model->dir.'/', $this->model->$name_folder.'/', $this->model->$name_folder);
        $this->download_send_headers($this->model->dir.'/'.$this->model->name_folder.'.zip');
    }
}
