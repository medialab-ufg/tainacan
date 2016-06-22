<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/create_js.php');
/**
 * View responsavel em inicializar o envio de arquivos locais
 */
?>
<style>
  #selectable .ui-selecting 
  { 
      //border: 3px solid rgb(122,167,207); 
  }
  #container_images,
  #container_pdfs,
  #container_videos,
  #container_audios, 
  #container_others {
      background-color: #c1d0dd;
      padding-right: 0px;
      padding-left: 15px;
      margin-top: 15px;
      padding-top: 5px;
  }
  
  .item-default{
      background: white;  
      margin-bottom: 15px;
      padding-bottom: 15px;
      padding-right: 15px;
      border: 3px solid #E8E8E8; 
      margin-right: 10px;
      width:19%;
  }
  .selected-border{
      border: 3px solid rgb(122,167,207); 
  }
  .input_title{
        text-align: center;
        width: 150px;
        padding:5px;
        border: none;
        background-color: rgb(209,211,212);
        font: 13px Arial;
        border-radius: 5px;
    }  
    .menu-left-size{
        width: 23%;
        padding-bottom: 15px;
    }
</style>
<div class="row" id='upload_container' >
    <div class="col-md-3 menu-left-size" id='container-menu-left' 
         style="background: white;border: 3px solid #E8E8E8;font: 13px Arial;min-height: 500px;padding-right: 15px;">
        <h3>
            <?php _e('You may send local files like:','tainacan') ?>
        </h3>
        <ul style="list-style: circle;">
                <li><?php _e('PDF','tainacan') ?>s;</li>
                <li><?php _e('Images (jpg, png, etc.);','tainacan') ?></li>
                <li><?php _e('Text (doc, docx, otf, etc.);','tainacan') ?></li>
        </ul>   
    </div> 
    <div class="col-md-9" style=" background: white;border: 3px solid #E8E8E8;margin-left: 15px;">
        <h3>
                <?php _e('Add new item - Send local file','tainacan') ?>
                <button onclick="back_main_list();" 
                        class="btn btn-default pull-right">
                            <?php _e('Cancel','tainacan') ?>
                </button>
        </h3>
        <hr>
        <div id='container-buttos-upload-files'>
            <button 
                onclick="$('#dropzone_multiple_items').trigger('click')" 
                    class="btn btn-primary  pull-left">
                <span class="glyphicon glyphicon-upload"></span>&nbsp;<?php _e('Add file(s)','tainacan') ?>
            </button>
            <button 
                style="display: none;"
                    id="click_editor_items_button" 
                    onclick="edit_items_uploaded()" 
                    class="btn btn-success  pull-right">
                </span>&nbsp;<?php _e('Next step','tainacan') ?>
                <span class="glyphicon glyphicon-arrow-right">
            </button>
        </div> <br><br>   
        <div  id="uploading">
            <!--h3><?php _e('Add Multiple Items', 'tainacan'); ?></h3 -->
            
            <!-- TAINACAN: UPLOAD DE ITEMS -->
             <div style="margin-bottom: 15px;min-height: 350px;padding-top: 0px;"  id="dropzone_multiple_items" class="dropzone">
                 <div class="dz-message" data-dz-message>
                         <span style="text-align: center;vertical-align: middle;">
                             <h2>
                                 <span class="glyphicon glyphicon-upload"></span>
                                 <b><?php _e('Drop Files','tainacan')  ?></b> 
                                     <?php _e('to upload','tainacan')  ?>
                             </h2>
                             <h4>(<?php _e('or click','tainacan')  ?>)</h4>
                         </span>
                </div>
            </div>
        </div>
    </div>    
</div>    
<!-- TAINACAN: MAPEAMENTO DOS ITEMS -->
<div style="margin-bottom: 50px;" id='editor_items'>
    <!-- MOSTRA O EDITOR DOS ITENS AO FINAL DO UPLOAD -->
</div>
