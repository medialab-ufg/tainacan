<?php
include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');

$url = get_current_url();
if(isset($_GET['url'])){
  $url = $_GET['url'];
} else {
  $indexless = str_replace("index.php", "", $url);
  header("Location: $indexless?url=$indexless");
}

$_is_single_ = (isset($_GET['is_single']) && ('true' === $_GET['is_single']));

function get_current_url() {
    $protocol = 'http';
    if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
        $protocol .= 's';
        $protocol_port = $_SERVER['SERVER_PORT'];
    } else {
        $protocol_port = 80;
    }
    $host = $_SERVER['HTTP_HOST'];
    $port = $_SERVER['SERVER_PORT'];
    $request = $_SERVER['PHP_SELF'];
    $query = '';
   // $query = substr($_SERVER['argv'][0], strpos($_SERVER['argv'][0], ';') + 1);
    $toret = $protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request . (empty($query) ? '' : '?' . $query);
    return $toret;
}
?>
<!DOCTYPE html>
<html
xmlns:foaf="http://xmlns.com/foaf/0.1/"
xmlns:dc="http://purl.org/dc/elements/1.1/">
<head>
<meta rel="dc:creator" href="http://alvaro.graves.cl" /> 
<meta rel="dc:source" href="http://github.com/alangrafu/visualRDF" /> 
<meta property="dc:modified" content="2012-05-18" /> 
<meta charset='utf-8'> 
<!--link href='css/bootstrap-responsive.min.css' rel='stylesheet' type='text/css' />
<link href='css/bootstrap.min.css' rel='stylesheet' type='text/css' /-->
<link href='css/bootstrap.css' rel='stylesheet' type='text/css' />
<link href='css/jquery_ui/jquery-ui.css' rel='stylesheet' type='text/css' />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script   src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"   integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="   crossorigin="anonymous"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<script type="text/javascript" src="js/d3/d3.js"></script>
<script type="text/javascript" src="js/d3/d3.layout.js"></script>
<script type="text/javascript" src="js/d3/d3.geom.js"></script>
<script type="text/javascript" src="js/d3/d3.geo.js"></script>
<script type="text/javascript">
var url = '<?php echo $url?>',
    thisUrl = document.URL;
</script>
<title>RDF</title>
</head>
<body>
    <div>
        <div class="row" style="margin-right: 15px;margin-left: 15px;margin-top:5px;">
            <h3>
                <?php
                _t('Graph',1);

                if($_is_single_):
                    echo '<button onclick="parent.close_graph_item_page()" id="btn_back_collection" class="btn btn-default pull-right">';
                else:
                    echo '<button onclick="parent.back_and_clean_url()" id="btn_back_collection" class="btn btn-default pull-right">';
                endif;

                _t('Back to collection',1);
                ?>
               </button> 
            </h3>
            <hr>
        </div>    
    </div>
 <div class="col-md-12">
  <div class="col-md-6">
   <form method="get" action="." class="form-inline">
    <input type="checkbox" checked id="properties"/>
      <label><?php _e('Hide properties','tainacan') ?></label>
    <input type="checkbox" id="hidePredicates"/>
      <label><?php _e('Hide predicates','tainacan') ?></label>
   <div id="preds" style="border: 1px solid black; position:absolute; display:none; color: white; background: rgba(0, 0, 0, 0.6);;"></div>
   </form>
  </div>
     <!--div class="col-md-6">
        <div id="lon">origin.longitude: <span>-98</span></div>
        <div id="lat">origin.latitude: <span>38</span></div><p>
        <div id="parallels">parallels: <span>29.5,45.5</span></div><p>
        <div id="scale">scale: <span>1000</span></div><p>
        <div id="translate-x">translate.x: <span>480</span></div>
        <div id="translate-y">translate.y: <span>250</span></div>
     </div-->
 </div>
</div>
<center>  
    <img id="waiting" alt="waiting icon" src="img/waiting.gif"/>
    <br>
<div class="row">
    <div class="col-md-12" 
         style="
         overflow: hidden;
         cursor:move;
         border-width: 2px; 
         border-style: solid;
         min-height:500px;
         min-width: <?php echo $_GET['width'].'px;' ?>

         " id='chart'></div>
</div>  
</center>  
<div class="row container">
    <div class="col-md-11">
     <strong style="color: grey;"> 
         <?php _e('Usage','tainacan') ?>:
     </strong> 
     <strong>
         
         <?php _e('Scroll','tainacan') ?>
     </strong> &#8594; Zoom. 
     <strong>
         <?php _e('Drag node','tainacan') ?>
     </strong> &#8594; <?php _e('Move node','tainacan') ?>. 
     <strong><?php _e('Drag background','tainacan') ?></strong> 
     &#8594; <?php _e('Move graph','tainacan') ?>.
 </div>
<script type="text/javascript" src='js/main.js'>
</script>


<div class="modal hide" id="embedDialog">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h3>Embed this code</h3>
  </div>
  <div class="modal-body">
    <pre id="embedableCode"></pre>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn close" data-dismiss="modal">Close</a>
  </div>
</div>
<script type="text/javascript">
//Embed dialog
$("#dialogButton").on('click', function(){
  var newUrl = thisUrl.replace("index.php?url", "?url").replace(/\/\?/, "/embed.php?");
  $("#embedableCode").text("<div style='width:600px;height:460px'><iframe style='overflow-x: hidden;overflow-y: hidden;' frameborder='0'  width='100%' height='99%' src='"+newUrl+"'></iframe></div>")
  $("#embedDialog").show();
});
$(".close").on('click', function(){
  $("#embedDialog").hide();
});
</script>
<div style="border: 1px solid black; background: white;display:none;position: absolute;" id="literals">
  <h3 style="padding:5px;" id="literalsubject"></h3>
  <div style="padding:5px;" id="literalmsg"></div>
  <table class="table table-hover" id="literaltable">
    <thead>
      <tr>
        <th><?php _e('Property','tainacan') ?></th><th><?php _e('Value','tainacan') ?></th>
      </tr>
    </thead>
    <tbody id="literalbody">
    </tbody>
  </table>
</div>
</body>
</html>


