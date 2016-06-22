<?php
/*
 *
 * View responsavel em mostrar uma categoria especifica
 *
 *
 */

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/page_js.php');
?>
    <style>
      .right_column{
        background: white;
        border: 3px solid #E8E8E8;
        padding: 15px;
        min-height: 260px;
        border-top: none;
    }
    </style>
    <div class='right_column'>
        <h3><?php echo $term->name ?>
             <!-- Links -->
            <button id="button_links_dropdown" type="button"  data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle btn btn-default btn-xs" >
               <div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
            </button>
            <ul id="ul_links_dropdown" style=" z-index: 9999;top:18%;" class="dropdown-menu" role="menu">
                 <li>
                     <a href="<?php echo get_the_permalink($collection_id) . '?tag=' . $term->slug; ?>.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
                     </a>
                 </li>
                 <?php if(is_restful_active()): ?>
                 <li>
                     <a href="<?php echo site_url() . '/wp-json/taxonomies/socialdb_tag_type/terms/' . $term->term_id ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp;
                     </a>
                 </li>
                 <?php endif; ?>
             </ul>  
            <!-- End:Links -->
            <?php if($term->slug!='socialdb_tag'): ?>
            <button type="button" onclick="show_modal_edit_tag('<?php echo $term->name ?>','<?php echo $term->term_id ?>')" class="btn btn-default btn-xs" >
                <span class="glyphicon glyphicon-edit"></span>
            </button>
            <?php endif; ?>
            <?php if($parent->name): ?>
            <small>&nbsp;
                <?php echo ($parent->name!='socialdb_tag')? __('Subclass of ','tainacan').$parent->name:__('Subclass of Tag','tainacan'); ?>
            </small>    
             <?php endif; ?>
            
            <button onclick="back_and_clean_url()" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
            
        </h3>
        <hr>
        <p>
           <b><?php _e('Description','tainacan') ?> </b> 
        </p>
        <p>
           <?php echo $term->description ?>   
        </p>   
        <br>
        <!-- Slug da categoria -->
        <p>
           <b><?php _e('Slug','tainacan') ?> </b> 
        </p>
        <p>
           <?php echo $term->slug ?>   
        </p>   
        <br>
        <!-- Individuos -->
        <p>
           <h4>
               <a id='link-individuals' href="">
                   <b><?php _e('Individuals','tainacan') ?> </b> 
               </a>
            <h4>
        </p>
    </div>