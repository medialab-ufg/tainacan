<?php

/* 
 * 
 * Arquivo de Configuracao do modo de operacao do Tainacan
 * 
 */

/*
# modo = 0; 0 ou nulo é o funcionamento atual padrão

# modo = 1; gestão arquivistica e museológica

# modo = 2; gestão de ontologias 
 
# modo = 3; Modo de debates Tainacan

# modo = 4; integração com a rede MinC
*/


global $config;
$config['mode'] = 0;
// o nome deve ser o mesmo da pasta e tambem o nome do arquivo aonde esta 
// o carregamento do modulo
$config['active_modules'] = ['tainacan-ontology']; //['archival-management']['contest']['tainacan-ontology']
//importante para a retirada de metadados criados
$config['deactive_modules'] = [];
//// ['tainacan-ontology']; //['archival-management']
// os metadados a serem inseridos como default
$config['metadata_slugs'] = [
            'creation_date_repository_property',
            'status_repository_property'];

 