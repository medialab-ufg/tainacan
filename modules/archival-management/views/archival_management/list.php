<?php
include_once (dirname(__FILE__) . '/../../../../../../../wp-config.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-load.php');
include_once (dirname(__FILE__) . '/../../../../../../../wp-includes/wp-db.php');
include_once ('js/list_js.php');
global $config;
$subject_id = get_post_meta($collection_id, 'socialdb_collection_subject_category', true);
$category_root_id = ($subject_id) ? $subject_id : $category_root_id;
?>  
<div id="categories_title" class="row"> 
    <div class="col-md-10 col-md-offset-1">
        <h3><?php _e('Archival Management','tainacan') ?><small>&nbsp;&nbsp;&nbsp;<!--a href="#MyWizard" onclick="show_modal_import_taxonomy()"><?php _e('Import','tainacan') ?></a--></small>
        <button onclick="backToMainPage();" 
                class="btn btn-default pull-right">
                    <?php _e('Back to collection','tainacan') ?>
        </button>
        </h3> 
       <hr> 
    </div>
    <div id="menu_archival"  class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-body">
            <h4>&nbsp;&nbsp;
                <a id="showDynatreeField" onclick="showDynatreeField()" style="cursor: pointer;">
                <?php _e('Alter Category root','tainacan') ?>
                <span class="glyphicon glyphicon-menu-down pull-right"></span>
                </a>
                <a id="hideDynatreeField" onclick="hideDynatreeField()" style="display: none;cursor: pointer;">
                    <?php _e('Hide Category root','tainacan') ?>
                    <span  class="glyphicon glyphicon-menu-up pull-right"></span>
                </a>    
            </h4>
            
            <input type="hidden" 
                   id="category_id_archival_management" 
                   name="category_id_archival_management" 
                   value="<?php echo $category_root_id ?>">
            <div style="display: none;height: 400px;overflow: scroll;" id="dynatree_export_plan">
            </div>
            </div >
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
                <span class="col-md-6">
                    <button class="btn btn-primary btn-lg btn-block"
                            onclick="export_classification_plan()">
                        <?php _e('Export Classification Plan','tainacan') ?>
                    </button>
                </span>
                <span class="col-md-6">
                    <button class="btn btn-primary btn-lg btn-block"
                            onclick="export_table_of_temporality()">
                    <?php _e('Export Table of Temporality','tainacan') ?>
                    </button>
                </span>
                <span class="col-md-12"><br></span>
                
                <span class="col-md-6">
                    <button class="btn btn-primary btn-lg btn-block"
                            onclick="list_export_items()">
                    <?php _e('List items to transfer','tainacan') ?>
                    </button>
                </span>
                 <span class="col-md-6">
                    <button class="btn btn-primary btn-lg btn-block"
                            onclick="list_export_items_elimination()">
                    <?php _e('List items to gathering or elimination','tainacan') ?>
                    </button>
                </span>
            </div>
          </div>
    </div>
    <br>
    <div style="display: none;" id="list_items_to_transfer" class="col-md-10 col-md-offset-1">
        <h3><?php _e('Items to transfer','tainacan') ?></h3>
        <table style="display: none;" id="table_items_to_transfer" class="table table-striped table-bordered" cellspacing="0" width="100%">  <!--class="table table-bordered" style="background-color: #d9edf7;"-->
             <thead>
               <tr>  
                <th><?php _e('Item name','tainacan','tainacan'); ?></th>
                <th><?php _e('Creation Date','tainacan'); ?></th>
                <th><?php _e('Current Phase Time','tainacan'); ?></th>
                <th><?php _e('Expired in','tainacan'); ?></th>
                <th><?php _e('Edit','tainacan'); ?></th>
               </tr> 
             </thead>     
            <tbody id="tbody_items_to_transfer" >
            </tbody>    
        </table>
        <div style="display: none;" id="alert_items_to_transfer" class="alert alert-info">
            <?php _e('No items to list','tainacan') ?>
        </div>    
    </div>
    <div style="display: none;" id="list_items_to_eliminate" class="col-md-10 col-md-offset-1">
         <h3><?php _e('Items to gathering or elimination','tainacan') ?></h3>
         <table style="display: none;" id="table_items_to_eliminate" class="table table-striped table-bordered" cellspacing="0" width="100%">  <!--class="table table-bordered" style="background-color: #d9edf7;"-->
             <thead>
               <tr>  
                <th><?php _e('Item name','tainacan','tainacan'); ?></th>
                <th><?php _e('Creation Date','tainacan'); ?></th>
                <th><?php _e('Intermediate Phase Time','tainacan'); ?></th>
                <th><?php _e('Expired in','tainacan'); ?></th>
                <th><?php _e('Edit','tainacan'); ?></th>
               </tr> 
             </thead>     
            <tbody id="tbody_items_to_elimination" >
            </tbody>    
        </table>
        <div style="display: none;" id="alert_items_to_eliminate" class="alert alert-info">
            <?php _e('No items to list','tainacan') ?>
        </div>
    </div>
</div>
  