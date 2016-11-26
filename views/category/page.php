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
$fixed_slugs = [
    'socialdb_property_fixed_title',
    'socialdb_property_fixed_description',
    'socialdb_property_fixed_content',
    'socialdb_property_fixed_source',
    'socialdb_property_fixed_license',
    'socialdb_property_fixed_thumbnail',
    'socialdb_property_fixed_attachments',
    'socialdb_property_fixed_tags',
    'socialdb_property_fixed_type'
];

?>
    <style>
        .right_column{
            background: white;
            border: 3px solid #E8E8E8;
            min-height: 260px;
            padding: 15px;
            border-top: none;
        }
    </style>
    <div class='right_column' >
        <h3>
            <?php echo $term->name ?>
           <?php if($term->slug!='socialdb_category'): ?>
            <button type="button" onclick="show_modal_edit_category('<?php echo $term->name ?>','<?php echo $term->term_id ?>')" class="btn btn-default btn-xs" >
               <span class="glyphicon glyphicon-edit"></span>
            </button>
           <?php endif; ?>
            <!-- Links -->
            <button id="category_button_links" style="position:relative;" type="button"  data-toggle="dropdown" role="button" aria-expanded="false" class="dropdown-toggle btn btn-default btn-xs" >
               <div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
            </button>
            <ul id="category_ul_links" style=" z-index: 999999;top:7%;" class="dropdown-menu" role="menu">
                 <li>
                     <a target="_blank" href="<?php echo get_the_permalink($collection_id) . '?category=' . $term->slug; ?>.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
                     </a>
                 </li>
                 <?php if(is_restful_active()): ?>
                 <li>
                     <a href="<?php echo site_url() . '/wp-json/taxonomies/socialdb_category_type/terms/' . $term->term_id ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp;
                     </a>
                 </li>
                 <?php endif; ?>
                  <li>
                    <a onclick="showGraph('<?php echo get_the_permalink($collection_id). '?category=' . $term->slug; ?>.rdf')"  style="cursor: pointer;"   >
                        <span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>&nbsp;
                    </a>
                </li>
             </ul>  
            <!-- End:Links -->
            <small>&nbsp;
                <?php echo ($parent->name!='socialdb_category')? __('Subclass of ','tainacan').' '.$parent->name:__('Subclass of Category','tainacan'); ?>
            </small> 
           
            <button onclick="back_and_clean_url()" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
            
        </h3>
        
        <hr>
        <p>
           <b><?php _e('Description','tainacan') ?> </b> 
        </p>
        <p>
           <?php echo $term->description ?>   
        </p>   
        <!-- Slug da categoria -->
        <p>
           <b><?php _e('Slug','tainacan') ?> </b> 
        </p>
        <p>
           <?php echo $term->slug ?>   
        </p>   
        <br>
        <!-- Propriedades da categoria -->
        <p>
           <b><?php _e('Properties','tainacan') ?> </b> 
           <?php if($term->slug!='socialdb_category'): ?>
            <button type="button" onclick="list_category_property_single('<?php echo $term->term_id ?>')" class="btn btn-default btn-xs" >
                 <span class="glyphicon glyphicon-edit"></span> <?php _e('Edit properties','tainacan')  ?> 
            </button>
            <?php endif; ?>
        </p>
        <p>
      <?php if($metadata&&is_array($metadata)): 
                foreach ($metadata as $meta): 
                    if($term->slug!=='socialdb_category' && in_array($meta['data']['slug'], $fixed_slugs)){
                        continue;
                    }
                    echo '<p>';             
                            if($meta['type']=='socialdb_property_data'):
                                echo "<a href='javascript:showPageProperties(\"".$meta['data']['slug']."\", \"".get_template_directory_uri()."\")'>";
                                echo $meta['data']['name'].'</a> - '.__('Type ','tainacan').$meta['data']['type'];                            
                            elseif($meta['type']=='socialdb_property_term'):
                                 $range = get_term_by('id', $meta['data']['metas']['socialdb_property_term_root'], 'socialdb_category_type');
                                 echo "<a href='javascript:showPageProperties(\"".$meta['data']['slug']."\", \"".get_template_directory_uri()."\")'>";
                                 echo $meta['data']['name'].'</a> - ';    
                                 echo "<a href='javascript:showPageCategories(\"".$range->slug."\", \"".get_template_directory_uri()."\")'>";
                                 echo $range->name.'</a>'; 
                            elseif($meta['type']=='socialdb_property_object'):    
                                $range = get_term_by('id', $meta['data']['metas']['socialdb_property_object_category_id'], 'socialdb_category_type');
                                echo "<a href='javascript:showPageProperties(\"".$meta['data']['slug']."\", \"".get_template_directory_uri()."\")'>";
                                echo $meta['data']['name'].'</a> - ';  
                                if(isset($range->name)){
                                    echo "<a href='javascript:showPageCategories(\"".$range->slug."\", \"".get_template_directory_uri()."\")'>";
                                    echo $range->name.'</a>'; 
                                }else{
                                   echo __('Multiple','tainacan');     
                                }
                            endif;
                     echo '</p>';               
                endforeach; ?>
      <?php endif; ?>
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
        <hr>
        <div class="row">
               <div id="comments_term"></div>
         </div>
    </div>