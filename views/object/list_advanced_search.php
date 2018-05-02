<?php
/*
 * 
 * View responsavel em mostrar o menu mais opcoes com as votacoes, propriedades e arquivos anexos
 * 
 * 
 */

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/list_advanced_search_js.php');

?>  
<hr>
<input type="hidden" id="keyword_pagination" name="keyword_pagination" value="<?php if(isset($keyword)) echo $keyword; ?>" >
<input type="hidden" id="sorted_form" name="sorted_form" value="<?php  echo $sorted_by; ?>" >
<div class="clear" style="margin-top: 5px;">
    <span class="pull-left"><b><?php  _e('Number of items found: ','tainacan'); ?><span id="object_count"><b><?php echo  $loop->found_posts; ?></b></span></b></span><br>   
</div><br>
 <?php if ($loop->have_posts()) : ?>
<div class="row" >
    <div class="post">
        <?php
        while ($loop->have_posts()) : $loop->the_post(); 
            $countLine++;
        ?>  
         <!-- Container geral do objeto-->
             <div id="object_<?php echo get_the_ID() ?>">
                <div class="media">
                    <div class="media-left">
                          <div>
                                      <?php
                                      //verifica se tem thumbnail
                                      if (get_the_post_thumbnail(get_the_ID())) {
                                          $url = get_post_meta(get_the_ID(), 'socialdb_thumbnail_url', true);
                                          $url_image = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
                                          if ($url) {
                                              ?>
                                              <!-- onclick="showSingleObject('< ?php echo get_the_ID() ?>', '< ?php echo get_template_directory_uri() ?>');" -->
                                              <a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'],[''],['']); return false"><?php echo get_the_post_thumbnail(get_the_ID(),'thumbnail'); ?></a>
                                          <?php } else {
                                              ?>
                                              <a href="#" onclick="$.prettyPhoto.open(['<?php echo $url_image; ?>'],[''],['']); return false">
                                                  <?php
                                                  echo get_the_post_thumbnail(get_the_ID(),'thumbnail');
                                                  ?>
                                              </a>
                                              <?php
                                          }
                                      } else {// pega a foto padrao
                                          ?>
                                              <img height="150" src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png">
                                  <?php } ?>
                        </div>
                    </div>
                <div class="media-body">
                    <h4 class="media-heading"><a target="_blank" href="<?php echo $data[get_the_ID()]['link'] ?>"><?php the_title() ?></a></h4>
                    <?php echo substr(get_the_content(), 0, 450) ; ?>
                </div>
              </div> 
                    <!-- Classifications   
                    <div class="col-md-2 droppableClassifications">
                         <input type="hidden" value="<?php echo get_the_ID() ?>" class="object_id">
                        <center><button id="show_classificiations_<?php echo get_the_ID() ?>" onclick="show_classifications('<?php echo get_the_ID() ?>')" class="btn btn-default btn-lg"><?php _e('Show classifications'); ?></button></center>
                        <div id="classifications_<?php echo get_the_ID() ?>">
                        </div>
                    </div>
                    <!-- end more info   
                    <!-- comments --
                    <div class="col-md-12" id="more_info">
                    </div-->
             </div>
         <hr>
        <?php endwhile; ?> 
        <hr>
        </div>
    </div> 
</div>
<?php else: ?> 
    <div id="items_not_found" class="alert alert-danger">
        <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('No objects found!','tainacan'); ?>
    </div>
    <div id="collection_empty" style="display:none" >
        <div class="jumbotron">
            <h2 style="text-align: center;"><?php _e('This collection is empty, create the first item!','tainacan') ?></h2>
            <p style="text-align: center;"><a onclick="show_form_item()" class="btn btn-primary btn-lg" href="#" role="button"><span class="glyphicon glyphicon-plus"></span>&nbsp;<?php _e('Click here to add a new item','tainacan') ?></a>
        </p>
</div>
    </div>
<?php endif; 
$numberItems = ceil($loop->found_posts / 10);
if ($loop->found_posts > 10):
  ?>
 <div id="center_pagination" class="well well-sm" style="height: 40px;">  
            <input type="hidden" id="number_pages_advanced" name="number_pages" value="<?php echo $numberItems;  ?>">
            <div id="teste" class="pagination_items" style="position: relative;right: 50%;left: 50%;">
                <a href="#" class="first" data-action="first">&laquo;</a>
                <a href="#" class="previous" data-action="previous">&lsaquo;</a>
                <input type="text" style="width: 90px;" readonly="readonly"  data-current-page="<?php if(isset($pagid)) echo $pagid;  ?>" data-max-page="0" />
                <a href="#" class="next" data-action="next">&rsaquo;</a>
                <a href="#" class="last" data-action="last">&raquo;</a>                                       
            </div> 
  </div>  
<?php endif; ?>


            