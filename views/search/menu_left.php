<?php
    include_once ('js/menu_left_js.php');
    $not_showed = false;
?>
<div class="expand-all btn white tainacan-default-tags prime-color-bg" style="background-color: #79a6ce">
    <div class="action-text" style="display: inline-block"><?php _e('Collapse all', 'tainacan') ?></div>
    &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
</div>

<div id="accordion">
<?php
do_action('before_facets',$facets,$collection_id);

// TAINACAN: widgets do menu esquerdo
foreach ($facets as $facet):
    if ($facet['widget'] == 'tree' && !$not_showed): $not_showed = true ?>
        <?php do_action('before_tree') ?>  
        <div class="form-group tainacan-default-tags">
            <!-- TAINACAN: panel para adicao de categorias e tags -->
            <label id="categories_main_tree" class="title-pipe">
                <?php _e('Categories','tainacan'); ?>
            </label>
            <div>
                <?php /* <ul class="dropdown-menu pull-right" role="menu" aria-labelledby="btnGroupVerticalDrop1">
                    <!-- TAINACAN: abre modal para adicao de categorias  -
                    <li></li>
                    <!-- TAINACAN: abre modal para adicao de tags  --
                    <li><a onclick="showModalFilters('add_tag');" href="#submit_filters_add_tag"><span class="glyphicon glyphicon-tag"></span>&nbsp;<?php _e('Add Tag','tainacan'); ?></a></li>
                </ul> */ ?>
                <!-- TAINACAN: os filtros do dynatree eram mostrados neste local -- desativado -->
                <div id="dynatree_filters"></div>

                <!-- TAINACAN: arvore montado nesta div pela biblioteca dynatree, html e css neste local totamente gerado pela biblioteca -->
                <div id="dynatree"></div>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'range'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group">
            <label for="object_tags" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div class="date-range-filter">
                <?php foreach ($facet['options'] as $range): ?>
                    <a href="#" onclick="wpquery_range('<?php echo $facet['id'] ?>', '<?php echo $facet['type'] ?>', '<?php echo $range['value_1'] ?>', '<?php echo $range['value_2'] ?>')">
                       <strong> <?php echo $range['value_1']; ?> </strong> <?php _e('until','tainacan') ?> <strong> <?php echo $range['value_2']; ?> </strong>
                    </a><br>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'from_to'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group from_to_filter">
            <label for="object_tags" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div>
                <?php if ($facet['type'] == 'date') { ?>
                    <span> <?php _e('From','tainacan') ?> </span>
                    <input size="7" type="text" class="input_date form-control" value="" placeholder="dd/mm/aaaa"
                           id="facet_<?php echo $facet['id']; ?>_1" name="facet_<?php echo $facet['id']; ?>_1">
                    <span> <?php _e('until','tainacan') ?> </span>
                    <input type="text" class="input_date form-control" size="7" value="" placeholder="dd/mm/aaaa"
                           id="facet_<?php echo $facet['id']; ?>_2" name="facet_<?php echo $facet['id']; ?>_2"> <br />
                    <button class="tainacan-filter-range" onclick="wpquery_fromto('<?php echo $facet['id']; ?>', 'date');" >
                        <?php _e('Filter', 'tainacan') ?> <span class="glyphicon glyphicon-arrow-right"></span>
                    </button>
                <?php } elseif ($facet['type'] == 'numeric'||in_array($facet['type'], ['like','binary','stars'])) { ?>
                    <input style="width: 30%" size="7" type="number"  value="" id="facet_<?php echo $facet['id']; ?>_1" name="facet_<?php echo $facet['id']; ?>_1"> <?php _e('until','tainacan') ?> <input style="width: 30%" type="number" size="7" value="" id="facet_<?php echo $facet['id']; ?>_2" name="facet_<?php echo $facet['id']; ?>_2">&nbsp;<button onclick="wpquery_fromto('<?php echo $facet['id']; ?>', 'numeric');" ><span class="glyphicon glyphicon-arrow-right"></span></button>
                <?php } else {  ?>
                    <input style="width: 30%" size="7" type="text" class="form-control" value="" id="facet_<?php echo $facet['id']; ?>_1" name="facet_<?php echo $facet['id']; ?>_1"> <?php _e('until','tainacan') ?> <input style="width: 30%" size="7" type="text" value="" id="facet_<?php echo $facet['id']; ?>_2" name="facet_<?php echo $facet['id']; ?>_2"><button onclick="wpquery_fromto('<?php echo $facet['id']; ?>', 'text');" ><span class="glyphicon glyphicon-arrow-right"></span></button>
                <?php } ?>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'multipleselect' || $facet['widget'] == 'searchbox'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group">
            <label for="object_tags" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div>
                <input type="text"
                       onkeyup="autocomplete_menu_left('<?php echo $facet['id']; ?>');" id="autocomplete_multipleselect_<?php echo $facet['id']; ?>" placeholder="<?php _e('Type the three first letters of the object of this collection ','tainacan'); ?>"  class="chosen-selected form-control"  />
                <select style="display: none;"  id="multipleselect_value_<?php echo $facet['id']; ?>" multiple class="chosen-selected2 form-control" style="height: auto;" name="multipleselect_value_<?php echo $facet['id']; ?>[]"  >
                </select>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'radio'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group">
            <label for="object_tags" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div>
                <?php foreach ($facet['categories'] as $category): ?>
                    <input type="radio" onchange="wpquery_filter_by_facet($(this).val(), '<?php echo $facet['id']; ?>', 'wpquery_radio');"
                           value="<?php echo $category->term_id; ?>" name="facet_<?php echo $facet['id']; ?>"> <?php echo $category->name; ?><br>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'checkbox'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group">
            <label for="object_tags" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div>
                <?php foreach ($facet['categories'] as $category): ?>
                    <input type="checkbox" id="checkbox_<?php echo $facet['id']; ?>_<?php echo $category->term_id; ?>" value="<?php echo $category->term_id; ?>" onchange="wpquery_checkbox(this, '<?php echo $facet['id']; ?>');" name="facet_<?php echo $facet['id']; ?>[]">&nbsp; <?php echo $category->name; ?><br>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif ($facet['widget'] == 'selectbox'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <div class="form-group">
            <label for="object_tags" class="title-pipe"><?php echo $facet['name']; ?></label>
            <div>
                <select class="form-control" onchange="wpquery_select(this, '<?php echo $facet['id']; ?>');" id="facet_<?php echo $facet['id']; ?>" name="facet_<?php echo $facet['id']; ?>">
                    <option value=""> <?php _e('Select...','tainacan'); ?> </option>
                <?php foreach ($facet['categories'] as $category): ?>
                    <option value="<?php echo $category->term_id; ?>" >  <?php echo $category->name; ?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'menu'): ?>
        <!-- TAINACAN: widget para realizacao de busca nos items  -->
        <?php
        $facet_menu_style =  get_post_meta( $collection_id, "socialdb_collection_facet_" .  $facet['id'] . "_menu_style", true );
        $f_menu = str_replace( "menu_style_", "", $facet_menu_style);
        $json_url = $this->get_menu_style_json( $f_menu );
        ?>
        <script type="text/javascript">
            var url = '<?php echo $json_url ?>';
            $.getJSON( url, function(data) {
                data.id = '<?php echo $f_menu ?>';
                var images_path = '<?php echo get_template_directory_uri() ?>' + '/extras/cssmenumaker/menus/' + data.id + '/images/';
                var formatted_css = data.css.replace(/#menu_class#/g, "#appended-" + data.id );
                formatted_css = formatted_css.replace(/#cssmenu/g, "#appended-" + data.id);
                formatted_css = formatted_css.replace(/#include_path#/g, images_path);
                var css_tags = formatted_css.match(/^@[a-z_]*/igm);
                var tags_values = findCSSTags(formatted_css);

                $(css_tags).each( function(idx, el) {
                    if( el != "@charset" ) {
                        var css_tag = el.replace('@', '');
                        var css_value = tags_values[css_tag];
                        var regex = new RegExp( el, "gim" );
                        formatted_css = formatted_css.replace( regex , css_value );
                    }
                });
                // Remove [[ e nome da var, deixa apenas HEX e ]]
                formatted_css = formatted_css.replace(/(\[\[[a-z_: ]+)/gi, "");
                // Remove ]]
                formatted_css = formatted_css.replace(/]/gi, "");

                var target_id = 'appended-' + data.id;
                $('head').append("<style type=\"text/css\">" + formatted_css + "</style>");
                var target_menu = "#menu_selected_result-" + data.id + " .cssmenumaker-menu";
                $(target_menu).attr('id', target_id );
            });
        </script>

        <!-- TAINACAN: widget para realizacao de busca nos items -->
        <div id="menu_selected_result-<?php echo $f_menu ?>" class="form-group">
            <label class="title-pipe"> <?php echo $facet['name']; ?> </label>
            <div id="tainacan-cssmenu-<?php echo $f_menu ?>" class="cssmenumaker-menu align-left">
                <ul> <?php echo $facet['html'];  ?> </ul>
            </div>
        </div>

    <?php elseif ($facet['widget'] == 'cloud'): ?>
        <div id="cloud_click_<?php echo $facet['id']; ?>" class="form-group">
            <label  class="title-pipe cloud_label"> <?php echo $facet['name']; ?></label>
            <div id="cloud_<?php echo $facet['id']; ?>" style="height: 150px;overflow: scroll;"></div>
        </div>

        <script type="text/javascript">
           // $('#cloud_click_<?php echo $facet['id']; ?>').click(function() {
                var words = [];
                var array = JSON.parse('<?php echo str_replace('\\','',Utf8_ansi($facet['json'])); ?>');
                $.each(array,function(index,value) {
                    words.push({ text: value.text, weight: value.weight,
                        handlers: {
                            click: function() { wpquery_filter_by_facet(value.value, value.facet_id, "wpquery_cloud"); }
                        }
                    });
                });
               $('#cloud_<?php echo $facet['id']; ?>').jQCloud( words, { height: 120 } );
          //  });
        </script>

    <?php 
    //lista rankings do tipo estrela
    elseif ($facet['widget'] == 'stars'):  ?>
         <div id="stars_widget_<?php echo $facet['id']; ?>" class="form-group">
            <label class="title-pipe"> <?php echo $facet['name']; ?> </label>
             <div style="padding-left: 30px;">
                 <a onclick="wpquery_range('<?php echo $facet['id'] ?>', '<?php echo $facet['type'] ?>', 4.1, 5)" style="cursor: pointer;"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star5.png' ?>"></a><br>
                 <a onclick="wpquery_range('<?php echo $facet['id'] ?>', '<?php echo $facet['type'] ?>', 3.1, 4)" style="cursor: pointer;"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star4.png' ?>"></a><br>
                 <a onclick="wpquery_range('<?php echo $facet['id'] ?>', '<?php echo $facet['type'] ?>', 2.1, 3)" style="cursor: pointer;"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star3.png' ?>"></a><br>
                 <a onclick="wpquery_range('<?php echo $facet['id'] ?>', '<?php echo $facet['type'] ?>', 1.1, 2)" style="cursor: pointer;"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star2.png' ?>"></a><br>
                 <a onclick="wpquery_range('<?php echo $facet['id'] ?>', '<?php echo $facet['type'] ?>', 0, 1)" style="cursor: pointer;"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star1.png' ?>"></a><br>
             </div>
        </div>
        
     <?php 
     //para listagem de autores mais colaborativos
     elseif ($facet['widget'] == 'ranking_colaborations'):  ?>    
        <!--div class="form-group" >
            <label for="ranking_users_colaborators" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div id="ranking_users_colaborators">
                <?php //  echo get_view('collection',['operation'=>'get_most_colaborators_authors','collection_id'=>$collection_id]) ?>
            </div>
        </div-->
     <?php 
     //para listagem de autores mais colaborativos
     elseif ($facet['widget'] == 'notifications'): $has_event_notification = true; ?>    
        <div class="form-group">
            <label for="notifications" class="title-pipe"> <?php echo $facet['name']; ?></label>
            <div id="notifications_filter">
            </div>
        </div>   
     <?php endif; ?>
    
     <?php 
     //acoes para novos widgets dos modulos
     do_action('add_widget',$facet);
     ?>

<?php endforeach; ?>
        <input type="hidden" id="filters_has_event_notification" value="<?php echo (isset($has_event_notification))? 'true':'false' ?>">
</div> <!-- // Closes #accordion -->