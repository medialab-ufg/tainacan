<?php

ini_set('max_input_vars', '10000');

require_once(dirname(__FILE__) . '../../../models/import/import_model.php');
require_once(dirname(__FILE__) . '../../../models/import/eur_model.php');
require_once(dirname(__FILE__) . '../../../models/import/eur_write_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class EurController extends Controller {

    public function operation($operation, $data) {
        $eur_model = new EurModel();

        switch ($operation) {
################################################################################            
            case "import_eur":
                //error_reporting(E_ALL);
                $eur_write_model = new EurWriteModel($data['collection_id']);
                if ($data['search']==''):
                    $jSon['error'] = "<b>OPPSSS:</b> Preencha o campo de busca!";
                else:
                    $collection_id = $eur_write_model->getCollection();
                    if (!$collection_id) {
                        $jSon['error'] = "<b>OPPSSS:</b> A coleção não foi encontrada ou não existe!";
                    } else {
                        $root_category = $eur_write_model->getRootCategory($collection_id);
                        if (!$root_category) {
                            $jSon['error'] = "<b>OPPSSS:</b> Erro ao encontrar a Root Collection. Entre em contato com o administrador do sistema!";
                        } else {
                            $eur_model->Search($data['search']);
                            $result_itens = $eur_model->getResult();
                            if (!empty($result_itens)) {
                                    if ($data["metadados"] == 'standart') {
                                        $eur_write_model->insert_items($result_itens);
                                        $jSon['success'] = "Busca e Inserções realizadas com sucesso!";
                                    } else {
                                        $metadados = array();
                                        foreach ($result_itens as $item) {
                                            foreach ($item as $key => $value) {
                                                if (!in_array($key, $metadados)) {
                                                    $metadados[] = $key;
                                                }
                                            }
                                        }
                                        if (!empty($metadados)) {
                                            //$Read->ExeRead('metadados', "WHERE collection_id = :id", "id={$collection_id}");
                                            $socialdb_europeana_metadata = get_post_meta($collection_id, 'socialdb_europeana_metadata', true);
                                            $metadatas_europeana = ($socialdb_europeana_metadata && !empty($socialdb_europeana_metadata)) ? unserialize($socialdb_europeana_metadata) : [];
                                            if (count($metadatas_europeana) > 0) {
                                                //achou metadados na tabela do banco para esta coleção
                                                $old_metadados = $metadatas_europeana;
                                                $new_metadados = array();
                                                foreach ($metadados as $metadado) {
                                                    if (!in_array($metadado, $old_metadados)) {
                                                        $new_metadados[] = $metadado;
                                                    }
                                                }

                                                if (!empty($new_metadados)) {
                                                    $new_metadados_bd = array_merge($old_metadados, $new_metadados);
//                                                    $Dados = array(
//                                                        'metadados' => serialize($new_metadados_bd)
//                                                    );
                                                    //$Update->ExeUpdate('metadados', $Dados, "WHERE id = :id", "id={$Read->getResult()[0]['id']}");
                                                    $result = update_post_meta($collection_id,  'socialdb_europeana_metadata',  serialize($new_metadados_bd));
                                                    if ($result) {
                                                        //Cria no tainacan os novos metadados
                                                        foreach ($new_metadados as $metadado) {
                                                            $created_metadata = $eur_write_model->createMetadata($metadado);
                                                            $Dados = array(
                                                                'metadado' => $metadado,
                                                                'metadata_id' => $created_metadata,
                                                                'collection_id' => $collection_id
                                                            );
                                                            add_post_meta($collection_id,  'socialdb_europeana_tainacan_mapping',  serialize($Dados));
                                                            
                                                            //$Create->ExeCreate('metadados_tainacan', $Dados);
                                                        }
                                                    }
                                                }

                                            } else {
                                               // $Dados = array(
                                                 //   'metadados' => serialize($metadados),
                                                 //   'collection_id' => $collection_id
                                               /// );
                                                $result = update_post_meta($collection_id,  'socialdb_europeana_metadata',  serialize($metadados));
                                                //$Create->ExeCreate('metadados', $Dados);

                                                if ($result) {
                                                    //Cria no tainacan os metadados
                                                    foreach ($metadados as $metadado) {
                                                        $created_metadata = $eur_write_model->createMetadata($metadado);
                                                        $Dados = array(
                                                            'metadado' => $metadado,
                                                            'metadata_id' => $created_metadata,
                                                            'collection_id' => $collection_id
                                                        );
                                                        add_post_meta($collection_id,  'socialdb_europeana_tainacan_mapping',  serialize($Dados));
                                                    }
                                                }
                                            }

                                            //Agora busca os IDS dos metadados e é só fazer a inserção dos itens
                                            //$Read->ExeRead('metadados_tainacan', "WHERE collection_id = :id", "id={$collection_id}");
                                            $all_properties = get_post_meta($collection_id,  'socialdb_europeana_tainacan_mapping');
                                            if($all_properties && is_array($all_properties)){
                                                $all_properties = array_map("unserialize",$all_properties);
                                                //tem o array de metadados
                                                $eur_write_model->insert_items_full($result_itens, $all_properties);
                                                $jSon['success'] = "Busca e Inserções realizadas com sucesso! Foram inseridos {$eur_write_model->getInsertedItems()} itens";

                                            }else{
                                                $jSon['error'] = "<b>OPPSSS:</b> Erro ao encontrar metadados da coleção. Entre em contato com o administrador do sistema!";
                                            }

                                        } else {
                                            $jSon['error'] = "<b>OPPSSS:</b> Erro ao encontrar metadados dos itens. Entre em contato com o administrador do sistema!";
                                        }
                                    }
                                } else {
                                    $jSon['error'] = "<b>OPPSSS:</b> Nenhum item encontrado na busca!";
                                }
                            }
                             return json_encode($jSon);
                        }
        //var_dump($url_colecao);
        //exit();
//            $Eur_Model->Search($Post['search']);
//            $result_itens = $Eur_Model->getResult();
////            var_dump($result_itens[0]);
////            exit();
//            if (!empty($result_itens)) {
//                $Tai_Model->insert_items($result_itens);
////            exit();
//                $jSon['success'] = "Busca e Inserções realizadas com sucesso!";
//            } else {
//                $jSon['error'] = "<b>OPPSSS:</b> Nenhum item encontrado na busca!";
//            }
                endif;
                break;
################################################################################
            case "search_eur":
                if ($data['search']==''):
                    $jSon['error'] = "<b>OPPSSS:</b> Preencha o campo de busca!";
                else:
                $eur_model->Search($data['search']);
                $result_itens = $eur_model->getResult();
                $result_totalitens = $eur_model->getTotalResults();
                if (!empty($result_itens)) {
                    $jSon['success'] = "Busca realizada com sucesso!";
                    $jSon['result'] = $result_itens[0];
                    $jSon['totalresult'] = "<b>Itens encontrados</b> (aprox.): {$result_totalitens}<br><br>";
                } else {
                    $jSon['error'] = "<b>OPPSSS:</b> Nenhum item encontrado na busca!";
                }
                return json_encode($jSon);
                endif;
            break;
        }
    }

}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
    $data['file'] = $_FILES;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
    $data['file'] = $_FILES;
}

$eur_controller = new EurController();
echo $eur_controller->operation($operation, $data);
?>