<?php

ini_set('max_input_vars', '10000');
error_reporting(0);
require_once(dirname(__FILE__) . '/collection_model.php');
require_once(dirname(__FILE__) . '/collection_import_model.php');
require_once(dirname(__FILE__) . '/../export/zip_model.php');

class CollectionTemplatesModel extends CollectionModel {

    /**
     * @signature get_collections_templates()
     * @return array Com os dados de cada template localizado dentro da pasta
     * data/templates
     */
    public function list_habilitate_collection_template(){
        $data = [];
        $user_template = get_option('socialdb_user_templates');
        if($user_template && is_array($user_template)){
            $all_user_template = $this->get_collections_templates();
            if(is_array($all_user_template) ){
                foreach ($all_user_template as $template) {
                    if(in_array($template['directory'], $user_template))
                            $data['user_templates'][] = $template;
                }
            }
        }
        //templates do repositorio
        $tainacan_template = get_option('socialdb_tainacan_templates');
        if($tainacan_template && is_array($tainacan_template)){
            $all_tainacan_template = $this->get_tainacan_templates();
            if(is_array($all_tainacan_template) ){
                foreach ($all_tainacan_template as $template) {
                    if(in_array($template['directory'], $user_template))
                            $data['tainacan_templates'][] = $template;
                }
            }
        }
        return $data;
    }
    
    /**
     * @signature get_collections_templates()
     * @return array Com os dados de cada template localizado dentro da pasta
     * data/templates
     */
    public function get_collections_templates() {
        $data = [];
        $dir = TAINACAN_UPLOAD_FOLDER . "/data/templates";
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot())
                continue;
            
            if($fileInfo->getFilename()){
                if(!is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/administrative_settings.xml')){
                    continue;
                } 
                
               $xml = simplexml_load_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/administrative_settings.xml'); 
               if(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.png')){
                    $thumbnail_id = get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.png';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpg')){
                    $thumbnail_id =  get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpg';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.gif')){
                    $thumbnail_id =  get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.gif';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpeg')){
                    $thumbnail_id =  get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpeg';
                }else{
                    $thumbnail_id = '';
                }
               $data[] = array (
                   'directory'=>$fileInfo->getFilename(),
                   'title'=>(string)$xml->post_title,
                   'description'=>(string)$xml->post_content,
                   'thumbnail'=> $thumbnail_id   ); 
            }
            
            //$xml = simplexml_load_file($fileInfo->getPath() . '/' . $fileInfo->getFilename());
            //$data = $this->add_hierarchy_importing_collection($xml, 0, $this->get_category_root_id());
            //$categories_id[] = $data['ids'];
        }
        return $data;
    }
    /**
     * @signature get_collections_templates()
     * @return array Com os dados de cada template localizado dentro da pasta
     * data/templates
     */
    public function get_tainacan_templates() {
        $data = [];
        $dir = dirname(__FILE__). "/../../data/templates";
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if ($fileInfo->isDot())
                continue;
            
            if($fileInfo->getFilename()){
                if(!is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/administrative_settings.xml')){
                    continue;
                } 
                
               $xml = simplexml_load_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/administrative_settings.xml'); 
               if(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.png')){
                    $thumbnail_id = get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.png';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpg')){
                    $thumbnail_id =  get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpg';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.gif')){
                    $thumbnail_id =  get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.gif';
                }elseif(is_file($dir.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpeg')){
                    $thumbnail_id =  get_template_directory_uri().'/../../uploads/tainacan/data/templates'.'/'.$fileInfo->getFilename().'/package/metadata/thumbnail.jpeg';
                }else{
                    $thumbnail_id = '';
                }
               $data[] = array (
                   'directory'=>$fileInfo->getFilename(),
                   'title'=>(string)$xml->post_title,
                   'description'=>(string)$xml->post_content,
                   'thumbnail'=> $thumbnail_id   ); 
            }
            
            //$xml = simplexml_load_file($fileInfo->getPath() . '/' . $fileInfo->getFilename());
            //$data = $this->add_hierarchy_importing_collection($xml, 0, $this->get_category_root_id());
            //$categories_id[] = $data['ids'];
        }
        return $data;
    }
    
    /**
     * metodo responsavel em criar o template selecionado pelo usuario
     * @param type $data array vindo da requisicao ajaz
     * @return uma strin json caso o template seja criado corretamente
     */
    public function add_collection_template($data) {
        $collection = get_post($data['collection_id']);
        $dir = TAINACAN_UPLOAD_FOLDER . "/data/templates";
        if(!is_dir($dir.'/'.$collection->post_name)){
             
            $r['d'] = mkdir($dir.'/'.$collection->post_name);
             
             $zipModel = new ZipModel;
             if($zipModel->generate_collection_template($dir.'/'.$collection->post_name, $collection->ID)) {
                 $r['r'] = ['result' => true ];
                
             }
             return json_encode( $r );
        }
    }
    
    /**
     * metodo responsavel em remover o template selecionado pelo usuario
     * @param type $data array vindo da requisicao ajaz
     * @return uma strin json caso o template seja criado corretamente
     */
    public function delete_collection_template($data) {
        $dir = TAINACAN_UPLOAD_FOLDER . "/data/templates";
        if(is_dir($dir.'/'.$data['collection_id'])){
             $zipModel = new ZipModel;
             if($zipModel->remove_template($dir.'/'.$data['collection_id'])){
                if(self::is_dir_empty($dir)){
                     update_option('disable_empty_collection', 'false');
                }
                return json_encode(['result'=>true]);
             }
        }
    }
    
    public static function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL; 
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            return FALSE;
          }
        }
        return TRUE;
    }
    
    /**
     * metodo responsavel em criar o json para o dynatree que lista os templates do repositorio
     * @param type $data array vindo da requisicao ajaz
     * @return uma strin json caso o template seja criado corretamente
     */
    public function dynatreeCollectionTemplate($data) {
        $dynatree = array(
            'title' => __('Templates','tainacan'), 
            'key' => $key, 
            'expand' => true, 
            'hideCheckbox' => true, 
            'addClass' => 'color1');
        $dynatree['children'][] =  $this->dynatreeUserTemplate();
        $dynatree['children'][] =  $this->dynatreeTainacanTemplate();
        return json_encode($dynatree);
    }
    
    /**
     * 
     * @param type $param
     */
    public function dynatreeUserTemplate() {
        $dynatree = array(
            'title' => __('My Templates','tainacan'), 
            'key' => 'false', 
            'expand' => true, 
            'addClass' => 'color1');
        $user_templates = $this->get_collections_templates();
        if($user_templates && is_array($user_templates)){
            foreach ($user_templates as $user_template) {
                 $dynatree['children'][]  = array(
                    'title' => $user_template['title'], 
                    'key' => $user_template['directory'], 
                    'type' => 'user', 
                    'expand' => true, 
                    'addClass' => 'color1');
            }
        }
        return $dynatree;
    }
    
    /**
     * 
     * @param type $dynatree
     */
    public function dynatreeTainacanTemplate() {
        $dynatree = array(
            'title' => __('Tainacan Templates','tainacan'), 
            'key' => 'false', 
            'expand' => true, 
            'addClass' => 'color1');
        return $dynatree;
    }

}
