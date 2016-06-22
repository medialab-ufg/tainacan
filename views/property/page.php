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
        <?php if(has_action('add_button_edit_property')): 
                do_action('add_button_edit_property', $term->term_id);
            endif; ?>
        <!-- Links -->
        <button id="button_links_dropdown" type="button"  data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle btn btn-default btn-xs" >
           <div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
        </button>
        <ul id="ul_links_dropdown" style=" z-index: 9999;top:18%;" class="dropdown-menu" role="menu">
             <li>
                 <a target="_blank" href="<?php echo get_the_permalink($collection_id) . '?property=' . $term->slug; ?>.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
                 </a>
             </li>
             <?php if(is_restful_active()): ?>
             <li>
                 <a href="<?php echo site_url() . '/wp-json/taxonomies/socialdb_property_type/terms/' . $term->term_id ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp;
                 </a>
             </li>
             <?php endif; ?>
             <li>
                <a onclick="showGraph('<?php echo get_the_permalink($collection_id). '?property=' . $term->slug; ?>.rdf')"  style="cursor: pointer;"   >
                    <span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>&nbsp;
                </a>
             </li>
         </ul>  
        <!-- End:Links -->
        <small>&nbsp;
            <?php echo ($parent->name!='socialdb_property')? __('Subclass of ','tainacan').' '.$parent->name:__('Subclass of Property','tainacan'); ?>
        </small> 
        
        <button onclick="back_and_clean_url()" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
    </h3>
    <hr>
    <!--p>
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
    <!-- Propriedades de origin -->
    <p>
       <b><?php _e('Property Origin','tainacan') ?> </b> 
    </p>
    <p>
  <?php 
                echo '<p>';           
                $domain = get_term_by('id', $metadata['data']['metas']['socialdb_property_created_category'], 'socialdb_category_type');
                echo "<a href='javascript:showPageCategories(\"".$domain->slug."\", \"".get_template_directory_uri()."\")'>";
                echo $domain->name.'</a>';   
                echo '</p>';  ?>
    </p>   
    <br>
    <!-- Propriedades de destino -->
    <p>
       <b><?php _e('Property Destiny','tainacan') ?> </b> 
    </p>
    <p>
    <?php 
    if($metadata['type']=='socialdb_property_data'):
        echo __('Type ','tainacan').$metadata['data']['type'];                            
    elseif($metadata['type']=='socialdb_property_term'):
         $range = get_term_by('id', $metadata['data']['metas']['socialdb_property_term_root'], 'socialdb_category_type');
         echo "<a href='javascript:showPageCategories(\"".$range->slug."\", \"".get_template_directory_uri()."\")'>";
         echo $range->name.'</a>'; 
    elseif($metadata['type']=='socialdb_property_object'):    
        $range = get_term_by('id', $metadata['data']['metas']['socialdb_property_object_category_id'], 'socialdb_category_type');
        echo "<a href='javascript:showPageCategories(\"".$range->slug."\", \"".get_template_directory_uri()."\")'>";
        echo $range->name.'</a>'; 
    endif;
    
    ?>
   </p>
    <hr>
    <div class="row">
           <div id="comments_term"></div>
     </div>
</div>
  
