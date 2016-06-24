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
      /* border: 3px solid rgb(122,167,207) ;*/ 
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
           <?php _e('Through this feature you can enter:', 'tainacan'); ?>
        </h3>
        <ul style="list-style: circle;">
                <li<?php _e('Files (ex. pdf, jpg, png, etc)', 'tainacan'); ?></li>
                <li><?php _e('Sites', 'tainacan'); ?></li>
                <li><?php _e('Video from Youtube or Vimeo', 'tainacan'); ?></li>
                <li><?php _e('Multiple videos from a Youtube Channel or Vimeo Channel', 'tainacan'); ?></li>
                <li><?php _e('Images from Flickr, Facebook or Instagram', 'tainacan'); ?></li>
        </ul>   
    </div> 
    <div class="col-md-9" style=" background: white;border: 3px solid #E8E8E8;margin-left: 15px;">
        <h3>
                <?php _e('Add new item - Insert URL','tainacan') ?>
                <button onclick="back_main_list();" 
                        class="btn btn-default pull-right">
                            <?php _e('Cancel','tainacan') ?>
                </button>
        </h3>
        <hr>
        <div id='container-input' class="col-md-12" style="padding-left: 0px;">
            <div class="form-group">
                <label  class="col-sm-1" for="item_url_import_all"><?php _e('URL', 'tainacan'); ?>:</label>
                <div class="col-sm-9">
                    <input type="text" 
                       onkeyup="verify_import_type()"  
                       name="item_url_import_all" 
                       id="item_url_import_all" 
                       placeholder="<?php _e('Type here', 'tainacan'); ?>" 
                       class="form-control"
                       /> 
                </div>
                <button type="button" onclick="importAll_verify()" class="col-sm-2 btn btn-primary"><?php _e('Submit', 'tainacan'); ?></button>
            </div>
            
        </div> 
        <div class="col-md-12" style="margin-top: 15px;">
            <center>
                <p>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/facebook.png' ?>" id="facebook_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/youtube.png' ?>" id="youtube_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/flickr.png' ?>" id="flickr_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/vimeo.png' ?>" id="vimeo_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/instagram.png' ?>" id="instagram_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/files.png' ?>" id="files_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/sites.png' ?>" id="sites_import_icon"/>
               </p>
            </center>
        </div>
    </div>    
</div>    
<!-- TAINACAN: MAPEAMENTO DOS ITEMS -->
<div style="margin-bottom: 50px;" id='editor_items'>
    <!-- MOSTRA O EDITOR DOS ITENS AO FINAL DO UPLOAD -->
</div>
