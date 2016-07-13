<?php

/* 
 * 
 * Arquivo de Configuracao do modo de operacao do Tainacan
 * 
 */

global $config;
$config['available_modules'] = [
# modo = 0; 0 ou nulo é o funcionamento atual padrão
'default',
# modo = 1; gestão arquivistica e museológica
'archival-management',
# modo = 2; gestão de ontologias 
'contest', 
# modo = 3; Modo de debates Tainacan
'tainacan-ontology'
# modo = 4; integração com a rede MinC
];

$operation = get_option('tainacan_module_activate');
if(!$operation||$operation==''){
   /************* ALTERAR SOMENTE AQUI SE NAO ESTIVER NENHUM MODULO JA ATIVADO ***********/
   $index = 0;
   $modules = []; //['archival-management']['contest']['tainacan-ontology']
   /**********************************************/
   if( $index != 0)
     update_option('tainacan_module_activate', implode(';', $modules));
}else{
   $array = explode(';', $operation);
   $index = array_search($array[0], $config['available_modules']); 
   $modules = $array;
}

/************* NAO ALTERAR DAQUI PARA BAIXO ***********/

$config['mode'] = $index;
// o nome deve ser o mesmo da pasta e tambem o nome do arquivo aonde esta 
// o carregamento do modulo
$config['active_modules'] = $modules; 
//importante para a retirada de metadados criados
$config['deactive_modules'] = [];
//// ['tainacan-ontology']; //['archival-management']
// os metadados a serem inseridos como default
$config['metadata_slugs'] = [
            'creation_date_repository_property',
            'status_repository_property'];

 