<?php


abstract class CollectionsFiltersApi {
    
    /**
     * metodo que traz os filtros e demais informacoes importantes de uma colecao
     * 
     */
    public function getCollectionFilters($collection_data) {
        $response['collection'] = CollectionsApi::getItem($collection_data['collection_post']->ID);
        $response['info']['permissions'] = CollectionsFiltersApi::getPermissions($collection_data);
    } 
    
    /**
     * trabalhando no retorno dos valores das permissoes
     */
    public function getPermissions($collection_data) {
        $return = [];
        foreach ($collection_data['collection_metas'] as $index => $value) {
            if(strpos($index, 'socialdb_collection_permission_')!==false){
                $return[str_replace('socialdb_collection_permission_', '', $index)] = $value;
            }
        }
        return $return;
    }
}