<?php

ini_set('max_input_vars', '10000');
error_reporting(0);
/**
 * Eduardo Humberto
 */
require_once(dirname(__FILE__) . '../../../models/export/oaipmh_listrecords_model.php');
require_once(dirname(__FILE__) . '../../../models/export/oaipmh_listsets_model.php');
require_once(dirname(__FILE__) . '../../../models/export/oaipmh_listidentifiers_model.php');
require_once(dirname(__FILE__) . '../../../models/export/oaipmh_identify_model.php');
require_once(dirname(__FILE__) . '../../../models/export/oaipmh_listmetadataformat_model.php');
require_once(dirname(__FILE__) . '../../../models/export/oaipmh_getrecord_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');
class OAIPMHController extends Controller {

    

    public function operation($operation, $data) {
        $oaipmh_listrecords_model = new OAIPMHListRecordsModel();
        $oaipmh_listsets_model = new OAIPMHListSetsModel();
        $oaipmh_listidentifiers_model = new OAIPMHListIdentifiersModel();
        $oaipmh_identify_model = new OAIPMHIdentifyModel();
        $oaipmh_listmetadataformats_model = new OAIPMHListMetaDataFormatsModel();
        $oaipmh_getrecord_model = new OAIPMHGetRecordModel();
        unset($data['by_function']);
        switch ($operation) {
            case "index":
                return $this->render(dirname(__FILE__) . '../../../views/export/oaipmh/index.php');
                break;
            case 'ListRecords':
                if (!isset($data['metadataPrefix'])&&!isset($data['resumptionToken'])) {
                    $oaipmh_listrecords_model->config();
                    $oaipmh_listrecords_model->errors[] = $oaipmh_listrecords_model->oai_error('missingArgument','metadataPrefix');
                    $oaipmh_listrecords_model->oai_exit($data,$oaipmh_listrecords_model->errors);
                }else{
                   $oaipmh_listrecords_model->list_records($data);
                }
                break;
            case 'ListSets':
                $allowed_arguments = array('verb','resumptionToken');
                foreach ($data as $key => $value) {
                    if(!in_array($key, $allowed_arguments)){
                        $oaipmh_listsets_model->config();
                        $oaipmh_listsets_model->errors[] = $oaipmh_listrecords_model->oai_error('badArgument');
                        $oaipmh_listsets_model->oai_exit($data,$oaipmh_listsets_model->errors);
                    }
                }
                $oaipmh_listsets_model->list_sets($data);
                break;
            case 'ListIdentifiers':
                if (!isset($data['metadataPrefix'])&&!isset($data['resumptionToken'])) {
                    $oaipmh_listidentifiers_model->config();
                    $oaipmh_listidentifiers_model->errors[] = $oaipmh_listidentifiers_model->oai_error('missingArgument','metadataPrefix');
                    $oaipmh_listidentifiers_model->oai_exit($data,$oaipmh_listidentifiers_model->errors);
                }else{
                   $oaipmh_listidentifiers_model->list_identifiers($data);
                }
                break;
            case 'Identify':
                $oaipmh_identify_model->identify($data);
                break;
            case 'ListMetadataFormats':
                $oaipmh_listmetadataformats_model->list_metadata_formats($data);
                break;
            case 'GetRecord':
                if (!isset($data['metadataPrefix'])) {
                    $oaipmh_getrecord_model->config();
                    $oaipmh_getrecord_model->errors[] = $oaipmh_listrecords_model->oai_error('missingArgument','metadataPrefix');
                    $oaipmh_getrecord_model->oai_exit($data,$oaipmh_listrecords_model->errors);
                }
                if(!isset($data['identifier'])){
                    $oaipmh_getrecord_model->config();
                    $oaipmh_getrecord_model->errors[] = $oaipmh_listrecords_model->oai_error('missingArgument','identifier');
                    $oaipmh_getrecord_model->oai_exit($data,$oaipmh_listrecords_model->errors);
                }
                 $oaipmh_getrecord_model->get_record($data);
                
                break;
            default:
                $oaipmh_listidentifiers_model->config();
                $oaipmh_listidentifiers_model->errors[] = $oaipmh_listidentifiers_model->oai_error('badArgument',$data['verb']);
                $oaipmh_listidentifiers_model->oai_exit($data,$oaipmh_listidentifiers_model->errors);
                break;

            // case 'saving_data':
            // return json_encode($oaipmh_model->saving_data($data));
        }
    }

   

}

/*
 * Controller execution
 */
if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
}elseif($_GET['verb']){
    $operation = $_GET['verb'];
    $data = $_GET;
}
else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$oaipmh_controller = new OAIPMHController();
echo $oaipmh_controller->operation($operation, $data);
