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
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../helpers/object/object_helper.php');
$viewHelper = new ViewHelper();
$number_elements = [10,20,50,100];
?>  
<h3>
    <?php _e('Search Results', 'tainacan'); ?>
    <a class="btn btn-default pull-right"  onclick="back_to_search_form()">
        <?php _e('Do new search','tainacan')?>
    </a> 
</h3>
<hr>
<input type="hidden" id="keyword_pagination" name="keyword_pagination" value="<?php if (isset($keyword)) echo $keyword; ?>" >
<input type="hidden" id="sorted_form" name="sorted_form" value="<?php echo $sorted_by; ?>" >
<div role="tabpanel">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist" id="ul_menu_search">
        <?php if(isset($loop_collections) && $loop_collections->have_posts()): ?>
        <li class="active">
            <a id="click_ad_search_collection" 
               href="#ad_search_collection" 
               aria-controls="ad_search_collection" 
               role="tab" 
               data-toggle="tab">
                <span style="font-size: 18px"><?php _e('Collections','tainacan') ?></span>
                <span ><b>(<?php echo $loop_collections->found_posts; ?>)</b></span>
            </a>
            
        </li>
         <?php endif; ?>
         <?php if(isset($loop_objects) && $loop_objects->have_posts()): ?>
        <li <?php echo (!isset($loop_collections))? 'class="active"':'' ?>>
            <a id="click_ad_search_items" href="#ad_search_items" aria-controls="ad_search_items" role="tab" data-toggle="tab">
                <span style="font-size: 18px"><?php _e('Items','tainacan') ?></span>
                <span><b>(<?php echo $loop_objects->found_posts; ?>)</b></span>
            </a>
        </li>
         <?php endif; ?>
    </ul>
    <!-- Tab panes -->
     <div class="tab-content">


<!---------------------- LISTA DE COLECOES ------------------------------------->

<?php if (isset($loop_collections) && $loop_collections->have_posts()) : ?>
    <div style="padding: 0px;" role="tabpanel" class="tab-pane active" id="ad_search_collection" >
        <ul style="padding: 0px;" id="cards-viewMode" class="post">
            <?php
            while ($loop_collections->have_posts()) : $loop_collections->the_post();
                $countLine++;
                include_once(dirname(__FILE__)."/../../views/object/list_modes/modals.php");
                ?>  
                <li style="padding: 0px;" class="col-md-6" id="object_<?php echo get_the_ID() ?>">
                        <input type="hidden" id="add_classification_allowed_<?php echo get_the_ID() ?>" name="add_classification_allowed" value="<?php echo (string)verify_allowed_action($collection_id,'socialdb_collection_permission_add_classification',get_the_ID()); ?>" />
                    <!-- TAINACAN: coloca a class row DO ITEM, sao cinco colunas possiveis todas elas podendo ser escondidas pelo o usuario, mas seu tamanho eh fixo col-md-2  -->
                    <div class="item-colecao">
                        <div class="row droppableClassifications item-info">
                            <div class="col-md-4 colFoto">
                                <a href="<?php echo get_collection_item_href($collection_id); ?>"
                                   onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                                    <?php echo get_item_thumb_image(get_the_ID()); ?>
                                </a>
                            </div>

                            <div class="col-md-8 flex-box item-meta-box" style="flex-direction:column;">
                                <div class="item-meta col-md-12 no-padding">
                                    <h4 class="item-display-title">
                                        <a href="<?php echo get_collection_item_href($collection_id); ?>"
                                           onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                    <div class="item-description"><?php echo wp_trim_words(get_the_content(), 8); ?></div>
                                    <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . get_the_author(); ?></div>
                                    <div class="item-creation"><?php echo "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y'); ?></div>
                                        <div class="editing-item">
                                            <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                                            <div id="rankings_<?php echo get_the_ID() ?>" class="rankings-container"></div>

                                            <div id="popover_content_wrapper<?php echo get_the_ID(); ?>_search" class="hide flex-box">
                                                <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><div data-icon="&#xe005;"></div></a>
                                                <a onclick="redirect_facebook('<?php echo get_the_ID() ?>');" href="#"><div data-icon="&#xe021;"></div></a>
                                                <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>"><div data-icon="&#xe01b;"></div></a>
                                            </div>

                                            <ul class="item-funcs col-md-5 right">
                                                <!-- TAINACAN: hidden com id do item -->
                                                <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">
                                                <li class="item-redesocial">
                                                    <a id="modal_network<?php echo get_the_ID(); ?>" onclick="showModalShareNetwork(<?php echo get_the_ID(); ?>)">
                                                        <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                    </a>
                                                </li>

                                                    <!--li><a href=""><span class="glyphicon glyphicon-comment"></span></a></li-->
                                                
                                            </ul>

                                        </div> <!-- .editing-item -->

                                               <!-- TAINACAN: script para disparar o evento que mostra os rankings 
                                        <script>
                                            $('#show_rankings_<?php echo get_the_ID() ?>').hide();
                                            $('#show_rankings_<?php echo get_the_ID() ?>').trigger('click');
                                        </script>-->
                                    <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                                    <div id="classifications_<?php echo get_the_ID() ?>_search" class="class-meta-box"></div>

                                </div>

                                <div class="">
                                    <!-- CATEGORIES AND TAGS -->
                                    <input type="hidden" value="<?php echo get_the_ID() ?>" class="object_id">
                                    <button id="show_classificiations_<?php echo get_the_ID() ?>_search" 
                                            style="width:100%" 
                                            class="btn btn-default"
                                            type="button"
                                            onclick="show_classifications_search('<?php echo get_the_ID() ?>')">
                                        <?php _e('Metadata', 'tainacan'); ?>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </li>
            <?php endwhile; ?> 
        </ul>
        <!-- paginacao -->
         <?php 
            $numberItems = ceil($loop_collections->found_posts / $loop_collections->query_vars['posts_per_page']);
            if ($loop_collections->found_posts > $loop_collections->query_vars['posts_per_page']):
                ?>
                <!-- TAINACAN: div com a paginacao da listagem -->
                <div class="">
                    <div  class="center_pagination col-md-12 ">
                        <div class="col-md-4 pull-left pagination_collection">
                            <a href="#" class="btn btn-default btn-sm first" data-action="first"><span class="glyphicon glyphicon-backward"></span><!--&laquo;--></a>
                            <a href="#" class="btn btn-default btn-sm previous" data-action="previous"><span class="glyphicon glyphicon-step-backward"></span><!--&lsaquo;--></a>
                            <input type="text"  style="width: 90px;"  readonly="readonly"  data-current-page="<?php if (isset($pagid)) echo $pagid; ?>" data-max-page="0" />
                            <a href="#" class="btn btn-default btn-sm next" data-action="next"><span class="glyphicon glyphicon-step-forward"></span><!--&rsaquo;--></a>
                            <a href="#" class="btn btn-default btn-sm last" data-action="last"><span class="glyphicon glyphicon-forward"></span><!--   &raquo; --></a>
                        </div>
                        <input type="hidden"   id="actual_page_collection"  value="<?php if (isset($pagid)) echo $pagid; else echo 1 ?>" />
                        <div class="col-md-3">
                            <center> 
                                <?php echo __('Showing collections:','tainacan')."1 - " . $loop_collections->query_vars['posts_per_page'] . __(' of ', 'tainacan') . $loop_collections->found_posts ?>
                            </center>    
                        </div>

                        <div class="col-md-3 pull-right">
                            <?php _e('Items per page:', 'tainacan') ?>
                            <select onchange="change_qtd_elements_collection(this)" name="items-per-page" id="items-per-page-collection">
                              <?php foreach ($number_elements as $value): ?>  
                                <option value="<?php echo $value ?>"
                                        <?php echo ($loop_collections->query_vars['posts_per_page']==$value)?'selected':'' ?>>
                                        <?php echo $value ?>    
                                </option>
                             <?php endforeach; ?>   
                            </select>
                        </div>
                        

                        <input type="hidden" id="number_pages_collection" name="number_pages" value="<?= $numberItems; ?>">    
                    </div>
                </div>
        <?php endif; ?>
    </div>   
<?php
endif;
?>
<!---------------------- FIM:LISTA DE COLECOES ------------------------------------->    
<!---------------------- LISTA DE OBJETPS ------------------------------------->    
<?php if (isset($loop_objects) && $loop_objects->have_posts()) : ?>
    <div  style="padding: 0px;" role="tabpanel" class="tab-pane <?php echo (!isset($loop_collections))? 'active':'' ?>" id="ad_search_items" >
        <ul id="cards-viewMode" style="padding: 0px;">
            <?php
            while ($loop_objects->have_posts()) : $loop_objects->the_post();
                $countLine++;
                $collection = $viewHelper->helper_get_collection_by_object(get_the_ID())[0];
                //se a colecao for privada
                if(!isset($collection->ID) || !$viewHelper->check_privacity_collection($collection->ID))
                    continue;
               include(dirname(__FILE__)."/modals_adv.php");
                ?>  
                <?php $link =  get_the_permalink($collection->ID).'?item='.basename(get_permalink()); ?>
                 <li style="padding: 0px;" class="col-md-6" id="object_<?php echo get_the_ID() ?>">
                        <input type="hidden" id="add_classification_allowed_<?php echo get_the_ID() ?>" name="add_classification_allowed" value="<?php echo (string)verify_allowed_action($collection_id,'socialdb_collection_permission_add_classification',get_the_ID()); ?>" />
                    <!-- TAINACAN: coloca a class row DO ITEM, sao cinco colunas possiveis todas elas podendo ser escondidas pelo o usuario, mas seu tamanho eh fixo col-md-2  -->
                    <div class="item-colecao">
                        <div class="row droppableClassifications item-info">
                            <div class="col-md-4 colFoto">
                                <a href="<?php echo $link; ?>"
                                   target="_blank">
                                    <?php echo get_item_thumb_image(get_the_ID()); ?>
                                </a>
                            </div>

                            <div class="col-md-8 flex-box item-meta-box" style="flex-direction:column;">
                                <div class="item-meta col-md-12 no-padding">
                                    <h4 class="item-display-title">
                                        <a href="<?php echo $link; ?>"
                                           target="_blank">
                                            <?php the_title(); ?>
                                        </a>
                                    </h4>
                                    <div class="item-description"><?php echo wp_trim_words(get_the_content(), 8); ?></div>
                                    <div class="item-author"><?php echo "<strong>" . __('Created by: ', 'tainacan') . "</strong>" . get_the_author(); ?></div>
                                    <div class="item-creation"><?php echo "<strong>" . __('Created at: ', 'tainacan') . "</strong>" . get_the_date('d/m/Y'); ?></div>
                                        <div class="editing-item">
                                            <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                                            <div id="rankings_<?php echo get_the_ID() ?>" class="rankings-container"></div>

                                            <ul class="item-funcs col-md-5 right">
                                                <!-- TAINACAN: hidden com id do item -->
                                                <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">

                                                <li class="item-redesocial">
                                                    <a id="modal_network<?php echo get_the_ID(); ?>" onclick="showModalShareNetwork(<?php echo get_the_ID(); ?>)">
                                                        <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                                                    </a>
                                                </li>

                                                    <!--li><a href=""><span class="glyphicon glyphicon-comment"></span></a></li-->
                                                
                                            </ul>

                                        </div> <!-- .editing-item -->

                                               <!-- TAINACAN: script para disparar o evento que mostra os rankings 
                                        <script>
                                            $('#show_rankings_<?php echo get_the_ID() ?>').hide();
                                            $('#show_rankings_<?php echo get_the_ID() ?>').trigger('click');
                                        </script>-->
                                    <!-- TAINACAN: container(AJAX) que mostra o html com as classificacoes do objeto -->
                                    <div id="classifications_<?php echo get_the_ID() ?>_search" class="class-meta-box"></div>

                                </div>

                                <div class="">
                                    <!-- CATEGORIES AND TAGS -->
                                    <input type="hidden" value="<?php echo get_the_ID() ?>" class="object_id">
                                    <button id="show_classificiations_<?php echo get_the_ID() ?>_search" 
                                            style="width:100%" 
                                            class="btn btn-default"
                                            type="button"
                                            onclick="show_classifications_search('<?php echo get_the_ID() ?>')">
                                        <?php _e('Metadata', 'tainacan'); ?>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </li>
            <?php endwhile; ?> 
        </ul>
         <?php   
            $numberItems = ceil($loop_objects->found_posts / $loop_objects->query_vars['posts_per_page']);
            if ($loop_objects->found_posts > $loop_objects->query_vars['posts_per_page']):
                ?>
                <!-- TAINACAN: div com a paginacao da listagem -->
                <div class="">
                    <div  class="col-md-12 center_pagination">

                        <input type="hidden" id="number_pages_items" name="number_pages" value="<?= $numberItems; ?>">
                        <div class="pagination_items col-md-4 pull-left">
                            <a href="#" class="btn btn-default btn-sm first" data-action="first"><span class="glyphicon glyphicon-backward"></span><!--&laquo;--></a>
                            <a href="#" class="btn btn-default btn-sm previous" data-action="previous"><span class="glyphicon glyphicon-step-backward"></span><!--&lsaquo;--></a>
                            <input type="text"  style="width: 90px;" readonly="readonly"  data-current-page="<?php if (isset($pagid)) echo $pagid; ?>" data-max-page="0" />
                            <a href="#" class="btn btn-default btn-sm next" data-action="next"><span class="glyphicon glyphicon-step-forward"></span><!--&rsaquo;--></a>
                            <a href="#" class="btn btn-default btn-sm last" data-action="last"><span class="glyphicon glyphicon-forward"></span><!--   &raquo; --></a>
                        </div>
                        <input type="hidden"   id="actual_page_items"  value="<?php if (isset($pagid)) echo $pagid; else echo 1 ?>" />
                        <div class="col-md-3">
                            <center>
                            <?php echo __('Showing items:','tainacan')."1 - " . $loop_objects->query_vars['posts_per_page'] . __(' of ', 'tainacan') . $loop_objects->found_posts ?>
                            
                            </center>    
                        </div>

                        <div class="col-md-3 pull-right">
                            <?php _e('Items per page:', 'tainacan') ?>
                            <select onchange="change_qtd_elements_items(this)" name="items-per-page" id="items-per-page-items">
                              <?php foreach ($number_elements as $value): ?>  
                                <option value="<?php echo $value ?>"
                                        <?php echo ($loop_objects->query_vars['posts_per_page']==$value)?'selected':'' ?>>
                                        <?php echo $value ?>    
                                </option>
                             <?php endforeach; ?>      
                            </select>
                        </div>

                    </div>
                </div>
        <?php endif; ?>
    </div>   
    <?php
    endif;
    ?>
    </div>
</div>
<!---------------------- FIM:LISTA DE OBJETOS ------------------------------------->   


