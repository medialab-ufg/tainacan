<?php

require_once(dirname(__FILE__).'../../../models/channel/channel_model.php');
require_once(dirname(__FILE__).'../../../controllers/general/general_controller.php');

class ChannelController extends Controller {



	}

 $operation = $_POST['operation'];
 $object_controller = new ObjectController();
 echo $object_controller->operation($operation);