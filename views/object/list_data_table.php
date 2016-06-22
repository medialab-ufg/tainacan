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
include_once ('js/list_js.php');
?>  
<div class="post">
        <div class="row">
                <div class="col-md-2"><strong><?php _e('Object Thumbnail'); ?></strong></b></div>
                <div class="col-md-2"><strong><?php _e('Object Name'); ?></strong></div>
                <div class="col-md-4"><strong><?php _e('Object Description'); ?></strong></div>
                <div class="col-md-2"><strong><?php _e('Classifications'); ?></strong></div>
                <div class="col-md-2"><strong><?php _e('Actions'); ?></strong></div>
        </div>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>  
        <!-- Container geral do objeto-->
         <div class="row" id="object_<?php echo get_the_ID() ?>" >
                   <!-- Thumbnail -->
                   <div class="col-md-2">
                    <?php if(get_the_post_thumbnail(get_the_ID(),'thumbnail')){
                          echo  get_the_post_thumbnail(get_the_ID(),'thumbnail');
                    }else{ ?>
                       <img src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png">
                    <?php } ?>
                   </div>
                    <!-- Title --> 
                    <?php if(get_option( 'collection_root_id' )==$collection_id): ?>
                    <div class="col-md-2"><a href="<?php echo get_the_permalink(); ?>"><?php the_title(); ?></a></div>
                    <?php else: ?>
                     <div class="col-md-2"><?php the_title(); ?></div>
                    <?php endif; ?>
                     <!-- Description -->  
                     <div class="col-md-3"><?php echo get_the_content();//get_the_excerpt(); ?></div>
                    <!-- Classifications -->  
                    <div class="col-md-3 droppableClassifications">
                         <input type="hidden" value="<?php echo get_the_ID() ?>" class="object_id">
                        <center><button id="show_classificiations_<?php echo get_the_ID() ?>" onclick="show_classifications('<?php echo get_the_ID() ?>')" class="btn btn-default btn-lg"><?php _e('Show classifications'); ?></button></center>
                        <div id="classifications_<?php echo get_the_ID() ?>">
                        </div>
                    </div>
                    <!-- Actions -->  
                    <div class="col-md-2">
                        <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">
                        <a href="#" class="more_info"> 
                            <span class="glyphicon glyphicon-user"></span>&nbsp;<?php echo __('Author: ').get_the_author(); ?>
                        </a><br> 
                        <?php if($is_moderator||get_the_author()===get_current_user_id()): ?>
                        <a onclick="delete_object('<?= __('Delete Object') ?>','<?= __('Are you sure to remove the object: '). get_the_title() ?>','<?php echo get_the_ID() ?>','<?= mktime() ?>')" href="#" class="remove"> 
                            <span class="glyphicon glyphicon-remove"></span>&nbsp;<?php _e('Delete'); ?>
                        </a><br>
                        <a href="#" class="edit">
                            <span class="glyphicon glyphicon-edit"></span>&nbsp;<?php _e('Edit'); ?>
                        </a><br>
                         <?php else: ?>
                        <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                            <span class="glyphicon glyphicon-warning-sign"></span>&nbsp;<?php _e('Report Abuse'); ?>
                        </a>
                        <!-- modal exluir -->
                        <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">  
                                    <div class="modal-header">
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                      <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                      <?php echo __('Describe why the object: '). get_the_title().__(' is abusive: '); ?>
                                        <textarea id="observation_delete_object<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close'); ?></button>
                                      <button onclick="report_abuse_object('<?= __('Delete Object') ?>','<?= __('Are you sure to remove the object: '). get_the_title() ?>','<?php echo get_the_ID() ?>','<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete'); ?></button>
                                    </div>
                                </form>  
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- more info -->  
                    <div class="col-md-12" >
                        <br>
                        <div class="row" id="all_info_<?php echo get_the_ID() ?>" style="display:none;" >
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" id="panel-title"><?php _e('Rankings'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                                    </div>
                                    <div class="panel-body">
                                        Panel content
                                    </div>
                                </div>      
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" id="panel-title"><?php _e('Properties'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                                        <div class="btn-group">
                                            <button data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop1" style="font-size:11px;">
                                                <span class="glyphicon glyphicon-plus grayleft" ></span>
                                                <span class="caret"></span>
                                            </button>
                                            <ul  aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu" style="width: 200px;">
                                                <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_data" onclick="show_form_data_property('<?php echo get_the_ID() ?>')" href="#property_form_<?php echo get_the_ID() ?>"><?php _e('Add new data property'); ?></a></span></li>
                                                <li>&nbsp;<span class="glyphicon glyphicon-th-list graydrop"></span>&nbsp;<span><a class="add_property_object" onclick="show_form_object_property('<?php echo get_the_ID() ?>')" href="#property_form_<?php echo get_the_ID() ?>"><?php _e('Add new object property'); ?></a></span></li>
                                            </ul>   
                                        </div>
                                        <div class="btn-group">
                                                            <button  data-toggle="dropdown" class="btn btn-default dropdown-toggle" type="button" id="btnGroupVerticalDrop2" style="font-size:11px;">
                                                                <span class="glyphicon glyphicon-pencil grayleft"></span>
                                                                <span class="caret"></span>
                                                            </button>
                                                            <ul id="list_properties_edit_remove" style="width:225px;" aria-labelledby="btnGroupVerticalDrop1" role="menu" class="dropdown-menu">
                                                            </ul>   
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <div id="list_all_properties_<?php echo get_the_ID() ?>">
                                        </div> 
                                        <div id="data_property_form_<?php echo get_the_ID() ?>">
                                        </div>
                                        <div id="object_property_form_<?php echo get_the_ID() ?>">
                                        </div> 
                                        <div id="edit_data_property_form_<?php echo get_the_ID() ?>">
                                        </div>
                                        <div id="edit_object_property_form_<?php echo get_the_ID() ?>">
                                        </div> 
                                    </div>
                                </div>      
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title" id="panel-title"><?php _e('Attachments'); ?><a class="anchorjs-link" href="#panel-title"><span class="anchorjs-icon"></span></a></h3>
                                    </div>
                                    <div class="panel-body">
                                        Panel content
                                    </div>
                                </div>      
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-12" >
                        <center id="more_info_show_<?php echo get_the_ID() ?>">
                            <a onclick="show_info('<?php echo get_the_ID() ?>')" href="#object_<?php echo get_the_ID() ?>" class="more_info"> 
                                 <span class="glyphicon glyphicon-chevron-down"></span>&nbsp;<?php _e('More Info'); ?>
                            </a>
                        </center>
                        <center id="less_info_show_<?php echo get_the_ID() ?>" style="display:none;">
                            <a onclick="show_info('<?php echo get_the_ID() ?>')" href="#object_<?php echo get_the_ID() ?>" class="more_info"> 
                                 <span class="glyphicon glyphicon-chevron-up"></span>&nbsp;<?php _e('Less Info'); ?>
                            </a>
                        </center>
                    </div>
                    <!-- end more info -->  
                    <!-- comments -->  
                    <div class="col-md-12" id="more_info">
                    </div>  
         </div>
        <hr>
        <?php endwhile;
    endif; ?> 
</div> 
	<!--	
 <div class="col-md-2"><input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>"><a href="#" class="edit"><span class="glyphicon glyphicon-edit"></span></a></div>
                  
                    <div class="col-md-2"><input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>"><a href="#" class="remove"> <span class="glyphicon glyphicon-remove"></span></a></div>
             
    <script>
               $(function(){
						console.log('here');
               });
    </script> -->
            