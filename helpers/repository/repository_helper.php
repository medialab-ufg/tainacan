<?php
/*
 *  Repository Controller's view helper 
 * */
class RepositoryHelper extends ViewHelper {
     public $operation;
     
     public function __construct($collection_id = 0) {
         $this->operation = get_option('tainacan_module_activate');
     }
    /**
     * metodo que retorna os modules que estao disponiveis para este repositorio
     * 
     * @return array Um array com o nome da pasta com todos os modulos disponiveis
     * ou entao um array vazio
     */
    public function get_available_modules() {
        $result = [];
        $dir = dirname(__FILE__) .'/../../modules';
        foreach (new DirectoryIterator($dir) as $dirInfo) {
                if($dirInfo->isDot()) 
                    continue;
                //adciono no array
                $result[] = $dirInfo->getFilename();
        }
        return $result;
    }
}