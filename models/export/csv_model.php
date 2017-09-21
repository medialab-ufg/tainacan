<?php

/**
 * Author: Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../general/general_model.php');
require_once(dirname(__FILE__) . '../../property/property_model.php');
require_once(dirname(__FILE__) . '../../category/category_model.php');
require_once(dirname(__FILE__) . '../../object/object_model.php');
require_once(dirname(__FILE__) . '../../collection/collection_model.php');
require_once(dirname(__FILE__) . '../../export/export_model.php');

class CSVExportModel extends ExportModel {

    
    /**
     * @signature - export_collection($collection_id)
     * @param int $collection_id O id da colecao
     * @return string O identifier do item a ser utilizado no form para o mapeamento
     * @description - funcao que exporta toda a colecao para um arquivo zip
     * @author: Eduardo 
     */
    public function generate_zip($collection_id,$data){
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/csv-package/items');
        if(!is_dir(dirname(__FILE__).'/csv-package/')){
             mkdir(dirname(__FILE__).'/csv-package');
        }
        mkdir(dirname(__FILE__).'/csv-package/items');
        $this->generate_csv_collection_file($data);
        //$this->get_collection_images($collection_id);
        $this->export_items_csv($collection_id);
        $this->create_zip_by_folder(dirname(__FILE__).'/','/csv-package/','csv-package');
        $this->download_send_headers_csv(dirname(__FILE__).'/csv-package.zip');
    }
     /**
     * @signature - get_collection_images($collection_id)
     * @param int $collection_id O id da colecao
     * @param string (Optional) O diretorio aonde sera criado
     * @return void copia o thumbnail e capa para as pastas qe serao zipadas
     * @author: Eduardo 
     */
    public function get_collection_images($collection_id,$dir = '') {
        if($dir==''){
           $dir =  dirname(__FILE__);
        }
        $thumbnail_id = get_post_thumbnail_id($collection_id);
        if($thumbnail_id){
          $fullsize_path = get_attached_file( $thumbnail_id ); // Full path
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          copy($fullsize_path, $dir.'/csv-package/thumbnail.'.$ext);
        }
        if(get_post_meta($collection_id, 'socialdb_collection_cover_id',true)){
          $fullsize_path = get_attached_file(get_post_meta($collection_id, 'socialdb_collection_cover_id',true)); // Full path
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          copy($fullsize_path, $dir.'/csv-package/cover.'.$ext);
        }
    }
    /**
     * @signature - export_collection($collection_id)
     * @param int $collection_id O id da colecao
     * @param string (Optional) O diretorio aonde sera criado
     * @return string O identifier do item a ser utilizado no form para o mapeamento
     * @description - funcao que exporta toda a colecao para um arquivo zip
     * @author: Eduardo 
     */
    public function generate_csv_collection_file($data,$dir = '') {
        if($dir==''){
           $dir =  dirname(__FILE__);
        }
        ob_end_clean();
        $csv_data = $this->generate_csv_data($data);
        $df = fopen($dir.'/csv-package/administrative-settings.csv', 'w');
        fputcsv($df, [], $data['socialdb_delimiter_csv']);
        fputcsv($df, array_keys(reset($csv_data)), $data['socialdb_delimiter_csv']);
        foreach ($csv_data as $row) {
            fputcsv($df, $row, $data['socialdb_delimiter_csv']);
        }
        fclose($df);
    }
    
    /**
     * @signature - download_send_headers($collection_id)
     * @param int 
     * @return 
     * @description - 
     * @author: Eduardo 
     */
    function download_send_headers_csv($filename) {
        $file_name = basename($filename);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=$file_name");
        header("Content-Length: " . filesize($filename));
        readfile($filename);
        unlink($filename);
        $this->recursiveRemoveDirectory(dirname(__FILE__).'/csv-package/items');
        unlink(dirname(__FILE__).'/csv-package/administrative-settings.csv');
        exit;
    }
    
   
     
     /**
     * @signature - export_items($collection_id)
     * @param int collection_id
      * * @param string (Optional) O diretorio aonde sera criado
     * @return 
     * @description - 
     * @author: Eduardo 
     */  
     public function export_items_csv($collection_id,$dir = '') {
        ob_end_clean(); 
        if($dir==''){
            $dir = dirname(__FILE__);
        } 
        $items = $this->get_collection_posts($collection_id);
        if($items&&is_array($items)){
            foreach ($items as $index => $item) {
                mkdir($dir.'/csv-package/items/'.$item->ID.'/');
                $this->export_item_thumbnail_csv($item->ID, $item->ID,$dir);
                $this->export_files($item->ID, $item->ID,$dir);
                $this->export_content($item->ID, $item->ID,$dir);
            }
        }
     }
     /**
      * 
      */
     /**
      * 
      */
     public function export_files($item_id,$index,$dir = '') {
        if($dir == ''){
             $dir = dirname(__FILE__);
         } 
        $post = get_post($item_id);
        $result = array();
        if (!is_object(get_post_thumbnail_id())) {
            $args = array(
                'post_type' => 'attachment',
                'numberposts' => -1,
                'post_status' => null,
                'post_parent' => $post->ID,
                'exclude' => get_post_thumbnail_id()
            );
            //  var_dump($args);
            $attachments = get_posts($args);
            $arquivos = get_post_meta($post->ID, '_file_id');
            if ($attachments) {
                if(!file_exists (dirname(__FILE__).'/csv-package/items/'.$index.'/files')){
                    mkdir(dirname(__FILE__).'/csv-package/items/'.$index.'/files');
                }
                foreach ($attachments as $attachment) {
                    if (in_array($attachment->ID, $arquivos)) {
                        $object_content = get_post_meta($item_id,'socialdb_object_content',true);
                        if($object_content!=$attachment->ID){
                            $fullsize_path = get_attached_file($attachment->ID); // Full path
                            $filename_only = basename($fullsize_path); // Just the file name
                            $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
                              ob_end_clean(); 
                            copy($fullsize_path, dirname(__FILE__).'/csv-package/items/'.$index.'/files/'.$filename_only.'.'.$ext);
                        }
                    }
                }
            }
        }
     }
     /**
      * 
      */
     public function export_item_thumbnail_csv($item_id,$index,$dir = '') {
         if($dir == ''){
             $dir = dirname(__FILE__);
         }
         $thumbnail_id = get_post_thumbnail_id($item_id);
        if($thumbnail_id&&  is_numeric($thumbnail_id)){
          $fullsize_path = get_attached_file( $thumbnail_id ); // Full path
          $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
          $filename_only = basename($fullsize_path); // Just the file name
            ob_end_clean(); 
          copy($fullsize_path,$dir.'/csv-package/items/'.$index.'/thumbnail.'.$ext);
        }
     }
     /**
      * 
      */
     public function export_content($item_id,$index,$dir = '') {
        if($dir == ''){
             $dir = dirname(__FILE__);
         } 
        $object_content = get_post_meta($item_id,'socialdb_object_content',true);
        $fullsize_path = get_attached_file($object_content);
        if($fullsize_path){
             mkdir(dirname(__FILE__).'/csv-package/items/'.$index.'/content');
             $ext = pathinfo($fullsize_path, PATHINFO_EXTENSION);
             $filename_only = basename($fullsize_path); // Just the file name
               ob_end_clean(); 
             copy($fullsize_path, dirname(__FILE__).'/csv-package/items/'.$index.'/content/'.$filename_only.'.'.$ext);
        }
     }
    
}
