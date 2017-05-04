<?php
include_once ('js/layout_js.php');
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');

$selected_view_mode = $ordenation['collection_metas']['socialdb_collection_list_mode'];
$view_helper = new ViewHelper();
?>

<?php $view_helper->render_header_config_steps('layout') ?>

<div class="categories_menu row col-md-12 customize-layout" id="properties_tabs">
    <?php if( isset($collection_table_metas) && $collection_table_metas > 0 ) {
        foreach ($collection_table_metas as $_table_meta) {
            echo "<input type='hidden' name='_tb_meta_' value='$_table_meta'>";
        }
    } ?>
    <div class="col-md-3 holder">
        <?php
        $cores = ['blue','brown','green','violet','grey'];
        $collection_ordenation = $ordenation['collection_metas']['socialdb_collection_ordenation_form'];
        $submission_visualization = $ordenation['collection_metas']['socialdb_collection_submission_visualization'];
        $item_visualization = (isset($ordenation['collection_metas']['socialdb_collection_item_visualization'])) ? $ordenation['collection_metas']['socialdb_collection_item_visualization']:'';
        $habilitate_media = (isset($ordenation['collection_metas']['socialdb_collection_habilitate_media'])) ? $ordenation['collection_metas']['socialdb_collection_habilitate_media']:'';
        $habilitate_item_media = (isset($ordenation['collection_metas']['socialdb_collection_item_habilitate_media'])) ? $ordenation['collection_metas']['socialdb_collection_item_habilitate_media']:'';
        $visualization_page_category = $ordenation['collection_metas']['socialdb_collection_visualization_page_category'];
        ?>
        <form method="POST" name="form_ordenation_search" style="padding-left: 15px;margin-top: 10px" id="form_ordenation_search">
        <div id="layout-accordion" class="menu_left" style="margin-top: 20px; padding-right: 0; font-size: 12px;border: none;">
            <h3 class="title-pipe">&nbsp;<?php _e('Collection style','tainacan'); ?> </h3>
            <div class="l-a-container">
                <?php $i=0; foreach(ViewHelper::$default_color_schemes as $color_scheme) { ?>
                    <div class="<?php echo $cores[$i] ?> color-container project-color-schemes" onclick="colorize('<?php echo $cores[$i] ?>')">
                        <input class="color-input color1" style="background:<?php echo $color_scheme[0] ?>" value="<?php echo $color_scheme[0] ?>" />
                        <input class="color-input color2" style="background:<?php echo $color_scheme[1] ?>" value="<?php echo $color_scheme[1] ?>" />
                    </div>
                    <?php $i++; } ?>

                <div id="collection-colorset" class="layout-colorpicker">
                    <label for="primary-custom-color"> <?php _e('More options', 'tainacan'); ?> </label>
                    <div class="input">
                        <input type="text" id="primary-custom-color" value="#7AA7CF" name="color_scheme[0][primary_color]">
                        <input type="text" id="second-custom-color" value="#0C698B" name="color_scheme[0][secondary_color]">
                    </div>

                    <input type="button" value="<?php _e('Add','tainacan'); ?>" class="btn btn-primary" onclick="appendColorScheme();">
                </div>

                <div class="custom_color_schemes" style="display: none">
                    <label for="custom_options"><?php _e('Your colors', 'tainacan'); ?></label>
                    <div class="custom_color_schemes here"></div>
                    <div class="defaults">
                        <input type="hidden" class="default-c1" name="default_color[primary]" value="">
                        <input type="hidden" class="default-c2" name="default_color[secondary]" value="">
                    </div>
                    <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
                    <input type="hidden" name="operation" value="update_color_schemes">
                    <!--button type="submit" class="btn btn-primary btn-sm"><?php _e('Save', 'tainacan'); ?></button-->
                </div>
                <input type="hidden" name="property_category_id"  value="<?php echo $category_root_id; ?>">
                <input type="hidden" name="selected_view_mode" class="selected_view_mode" value="<?php echo $selected_view_mode ?>"/>

                    <!------------------- Modo de exibição dos itens -------------------------->
                    <div class="form-group" style="border-top: 1px solid #e8e8e8; margin-top: 10px; padding-top: 10px;">
                        <label for="collection_list_mode"><?php _e('Default list mode','tainacan'); ?></label>
                        <select name="collection_list_mode" id="collection_list_mode" class="form-control">
                            <option value="cards"><?php _e('Cards', 'tainacan'); ?></option>
                            <option value="gallery"><?php _e('Gallery', 'tainacan'); ?></option>
                            <option value="list"><?php _e('List', 'tainacan'); ?></option>
                            <option value="slideshow"><?php _e('Slideshow', 'tainacan'); ?></option>
                            <option value="geolocation"><?php _e('Geolocation', 'tainacan'); ?></option>
                            <option value="table"><?php _e('Table', 'tainacan'); ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group sl-time" style='display: none'>
                        <label for="slideshow_time"><?php _e('Slideshow time (seconds)', 'tainacan'); ?></label>
                        <select name="slideshow_time" class="form-control">
                            <?php foreach( range(1, 20) as $num ): ?>
                                <option value="st-<?php echo $num ?>-secs" <?php if($num===4) { echo "selected"; } ?> >
                                    <?php echo $num; ?>
                                </option>                            
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="prox-container">
                        <div class="form-group">
                            <label for="aprox-mode"><?php _e('Approximate mode', 'tainacan'); ?> </label>
                            <input type="checkbox" name="prox_mode" class='prox_mode' value="use_approx_mode">
                        </div>

                        <div class="form-group location" style='display: none'>
                            <label for="location"> <?php _e('Location', 'tainacan'); ?> </label>
                            <select name="location" class="form-control"></select>
                        </div>
                    </div>
                    
                    <div class="form-group geo-lat coordinate" style='display: none'>
                        <label for="latitude"> <?php _e('Latitude', 'tainacan'); ?> </label>
                        <select name="latitude" id="latitude_select" class="form-control"></select>
                    </div>
                    
                    <div class="form-group geo-long coordinate" style='display: none'>
                        <label for="longitude"> <?php _e('Longitude', 'tainacan'); ?> </label>
                        <select name="longitude" id="longitude_select" class="form-control"></select>
                    </div>

                    <div class="form-group table-meta-config" style="display: none">
                        <label for="table-metas"><?php _e('Select metadata and the order that will show up at table', 'tainacan'); ?></label>
                        <ul id="sort-metas"></ul>
                    </div>

                    <!------------------- Ordenacao-------------------------->
                    <div class="form-group">
                        <label for="collection_order"><?php _e('Select the default ordination','tainacan'); ?></label>
                        <select id="collection_order" name="collection_order" class="form-control">
                        </select>
                    </div>

                    <!------------------- Forma de ordenacao -------------------------->
                    <div class="form-group">
                        <label for="collection_ordenation_form"><?php _e('Select the ordination form','tainacan'); ?></label>
                        <select name="socialdb_collection_ordenation_form" class="form-control">
                            <option value="desc" <?php ( $collection_ordenation == 'desc' || empty($collection_ordenation) ) ? "selected = 'selected'" : ''; ?> >
                                <?php _e('DESC','tainacan'); ?>
                            </option>
                            <option value="asc" <?php if ($collection_ordenation == 'asc') { echo 'selected = "selected"'; } ?> >
                                <?php _e('ASC','tainacan'); ?>
                            </option>
                        </select>
                    </div>
                    
                    <!------------------- Forma de visualizacao formulario da pagina de categoria -------------------------->
                    <div class="form-group">
                        <label for="socialdb_collection_visualization_page_category"><?php _e('Select the mode to open category page','tainacan'); ?></label>
                        <select name="socialdb_collection_visualization_page_category" class="form-control">
                            <option value="click" <?php ( $visualization_page_category == 'click' ) ? "selected = 'selected'" : ''; ?> >
                                <?php _e('Click at the category','tainacan'); ?>
                            </option>
                            <option value="right_button" <?php if ($visualization_page_category == 'right_button'|| empty($visualization_page_category)) { echo 'selected = "selected"'; } ?> >
                                <?php _e('Right button at the category','tainacan'); ?>
                            </option>
                        </select>
                    </div>


                <?php
                $_check_all = false;
                if($collection_add_item === false) {
                    $_check_all = true;
                }
                $_add_item_types = [ 'write_text' => _t('Enable Write text'), 'send_file' => _t('Enable Send file(s)'),
                    'send_file_zip' => _t('Enable Send file(s) via zip'), 'insert_url' => _t('Enable Insert URL') ];
                ?>
                <div class="form-group">
                    <label for="socialdb_collection_add_item"> <?php _t('Select the options to add a new item', 1); ?> </label> <br>
                    <div class="add-item-checkboxes" style="padding: 10px;">
                        <?php foreach($_add_item_types as $type => $txt):
                            if($_check_all) {
                                $val_ck = "checked";
                            } else {
                                if( !is_null($collection_add_item) ) {
                                    if( in_array($type, $collection_add_item) ) {
                                        $val_ck = "checked";
                                    } else {
                                        $val_ck = "";
                                    }
                                } else {
                                    $val_ck = "";
                                }
                            }
                            ?>
                            <input type="checkbox" <?php echo $val_ck; ?> name="col_add_item[]" id="" value="<?php echo $type; ?>" /> <?php echo $txt; ?> <br>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <h3 class="title-pipe">&nbsp;<?php _e('Item view style','tainacan'); ?> </h3>
            <div style="padding-left: 15px">
                <!------------------- Forma de visualizacao do item -------------------------->
                <div class="form-group">
                    <label for="collection_ordenation_form"><?php _e('Select the layout for the item visualization','tainacan'); ?></label><br>
                    <input type="radio" 
                           <?php echo ( $item_visualization == 'two' || empty($item_visualization)) ? "checked = 'checked'" : ''; ?>
                           name="socialdb_collection_item_visualization" 
                            onchange="showHabilitateItemMedia(this,false)" 
                           value="two">&nbsp;<?php _e('Focus in media','tainacan'); ?><br>
                    <input type="radio" 
                           name="socialdb_collection_item_visualization" 
                           onchange="showHabilitateItemMedia(this,true)" 
                           <?php echo ( $item_visualization == 'one' ) ? "checked = 'checked'" : ''; ?>
                           value="one">&nbsp;<?php _e('Focus in metadata','tainacan'); ?><br>
                    <div id="habilitateItemMedia" <?php echo ($item_visualization == 'two' || empty($item_visualization))?'style="display:none;"' : '' ?>>
                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="habilitateItemMedia" value="true" <?php echo ( $habilitate_item_media == 'true' ) ? "checked = 'checked'" : ''; ?>>
                        <?php _e('Habilitate visualization of image and attachments','tainacan') ?>
                    </div>
                </div>
            </div>
             <h3 class="title-pipe" >&nbsp;<?php _e('Item insert style','tainacan'); ?> </h3>
              <div style="padding-left: 15px">
                <!------------------- Forma de visualizacao formulario de submissao -------------------------->
                <div class="form-group">
                    <label for="collection_ordenation_form"><?php _e('Select the form layout to create a new text item','tainacan'); ?></label><br>
                    <input type="radio" 
                           <?php echo ( $submission_visualization == 'two' || empty($submission_visualization) ) ? "checked = 'checked'" : ''; ?>
                           name="socialdb_collection_submission_visualization" 
                           onchange="showHabilitateMedia(this,false)" 
                           value="two">&nbsp;<?php _e('Focus in media','tainacan'); ?><br>
                    <input type="radio" 
                           name="socialdb_collection_submission_visualization" 
                           onchange="showHabilitateMedia(this,true)" 
                           <?php echo ( $submission_visualization == 'one' ) ? "checked = 'checked'" : ''; ?>
                           value="one">&nbsp;<?php _e('Focus in metadata','tainacan'); ?><br>
                    
                            <!--select name="socialdb_collection_submission_visualization" id="socialdb_collection_submission_visualization" onchange="showHabilitateMedia(this)" class="form-control">
                                <option value="one" <?php ( $submission_visualization == 'one' ) ? "selected = 'selected'" : ''; ?> >
                                    <?php _e('1 column','tainacan'); ?>
                                </option>
                                <option value="two" <?php if ($submission_visualization == 'two'|| empty($submission_visualization)) { echo 'selected = "selected"'; } ?> >
                                    <?php _e('2 columns','tainacan'); ?>
                                </option>
                            </select-->
                    <div id="habilitateMedia" <?php echo ($submission_visualization == 'two' || empty($submission_visualization))?'style="display:none;"' : '' ?> >
                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="habilitateMedia" value="true"    <?php echo ( $habilitate_media == 'true' ) ? "checked = 'checked'" : ''; ?>>
                        <?php _e('Habilitate visualization of image and attachments','tainacan') ?>
                    </div>
                </div>
              </div>
        </div>
            <input type="hidden" id="collection_id_order_form" name="collection_id" value="<?php echo $collection_id; ?>">
            <input type="hidden" id="operation" name="operation" value="update_ordenation">
            
            <button type="submit" style="margin-top:15px;margin-bottom: 15px;"id="submit_ordenation_form" class="btn btn-success pull-right"><?php _e('Save','tainacan') ?></button>
            <button type="button" style="margin-top:15px;margin-right: 5px;margin-bottom: 15px;" onclick="history.back()" class="btn btn-default pull-right"><?php _e('Cancel','tainacan') ?></button>
        </form>
    </div>

    <div class="col-md-9 preset-filters no-padding" style="background: #414042; padding-bottom: 20px;">
        <div class="categories_menu" id="personalize_search">
            <div id="tainacan-mini" class="col-md-11" style="float: none; margin: 0 auto; padding-top: 20px;">
                <header>
                    <div class="topo" style="background: #231F20; height: 30px; width: 100%;"></div>
                    <div style="background-color: #58595B; background-position: right 2.8% top 10px; height: 120px; width: 100%;" class="capa-col"></div>
                </header>
                <div class="row">

                </div>
                <div class="body col-md-12 no-padding" style="background: white; font-weight: bolder">
                    <div class="col-md-2">

                        <div class="meta-header color1" style="width: 100%; height: 25px; float: left; margin-top: 15px"></div>

                        <?php for($c = 0; $c < 7; $c++): ?>
                            <div class="col-md-12 no-padding" style="border: 1px solid #e8e8e8; margin-top: 10px;">
                                <div class="col-md-1 no-padding" style="height: 35px;">
                                    <div style="width: 50%; height: 100%;" class="color2"></div>
                                    <div></div>
                                </div>
                                <div class="col-md-1 no-padding">
                                    <div class="color2" style="width: 10px; height: 10px; margin-top: 12px;"></div>
                                </div>
                                <div class="col-md-10" style="padding-left: 5px; padding-right: 3px">
                                    <div class="" style="height: 7px; margin-top: 13px; background: #e3e3e3"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="col-md-10 no-padding" style="background: #e8e8e8; padding: 15px;">
                        <div class="col-header col-md-12" style="">
                            <div class="search" style="width: 100%; height: 35px; background: white; border: 1px solid #aaa;"></div>
                            <div class="search-result color1" style="width: 100%; height: 20px;margin-bottom: 5px;margin-top: 5px;"></div>
                            <div class="">
                                <div class="col-md-1 color2" style="height: 20px; margin-right: 5px"> </div>
                                <div class="col-md-2" style="border: 1px solid #aaa; height: 20px;">
                                    <div class="color2" style="height: 8px; margin-top: 5px; width: 80%"></div>
                                </div>
                                <div class="col-md-1"></div>
                                <div class="col-md-2 no-padding boxes">
                                    <div class="color2" style="height: 15px; width: 20%"></div>
                                    <div class="" style="background: #aaa; height: 15px; margin-top: 5px; width: 70%"></div>
                                </div>
                                <div class="col-md-2 boxes">
                                    <div class="color2" style="height: 15px; width: 60%"></div>
                                    <div class="" style="background: #aaa; height: 15px; margin-top: 5px; width: 30%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="items-container col-md-12"  style="margin-top: 10px">
                            <?php for($i=0; $i < 8; $i++): ?>
                                <div class="item-box" style="background: white; width: 50%; height: 120px; float: left; padding: 10px">
                                    <div style="height: 100%; width: 30%; background: #aaa; margin-right: 5px"></div>
                                    <div style="width: 65%; height: 100%; position: relative">
                                        <div style="width: 100%; height: 20px; background: #aaa"></div>
                                        <div class="color1" style="position:absolute; top: 40%; right: 0; width: 20%; height: 20px;"></div>
                                        <div style="position:absolute; bottom: 0; width: 100%; height: 20px; border: 1px solid #aaa;"></div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <style>
                            .items-container .item-box > div, .col-header .boxes > div {
                                display: inline-block;
                            }
                            .color-container input:hover {
                                cursor: pointer;
                            }
                            .color-container:hover {
                                cursor: pointer;
                                border: 1px solid black;
                            }
                        </style>
                    </div>
                </div>
                <footer class="col-md-12" style="height: 100px; background: #CECECE;"></footer>
            </div>
        </div>

    </div>
</div> <!-- #properties-tabs -->