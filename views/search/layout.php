<?php
include_once ('js/layout_js.php');
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');

$selected_view_mode = $ordenation['collection_metas']['socialdb_collection_list_mode'];
$view_helper = new ViewHelper();
?>

<?php $view_helper->render_header_config_steps('layout') ?>

<div class="categories_menu row col-md-12 customize-layout" id="properties_tabs">
    <div class="col-md-2" style="padding-left: 0; background: white;">

        <?php
        $default_color_schemes = [
                'blue'   => ['#7AA7CF', '#0C698B'],
                'brown'  => ['#874A1D', '#4D311F'],
                'green'  => ['#3D8B55', '#242D11'],
                'violet' => ['#7852B2', '#31185C'],
                'grey'   => ['#58595B', '#231F20'],
            ];
        $cores = ['blue','brown','green','violet','grey'];
        $i=0;
        $collection_ordenation = $ordenation['collection_metas']['socialdb_collection_ordenation_form'];
        ?>
        <div id="layout-accordion" style="margin-top: 20px; padding-right: 0; font-size: 12px;">
            <h3 style="border-left: 4px solid #7AA7CF; border-radius: 0 2px 2px 0;"> <?php _e('Colors','tainacan'); ?> </h3>
            <div style="padding-left: 10px">
                <?php foreach($default_color_schemes as $color_scheme) { ?>
                    <div class="<?php echo $cores[$i] ?> color-container" style="margin-left: 5px; margin-bottom: 8px; width: 71px; display: inline-block; border: 1px solid darkgrey"
                         onclick="colorize('<?php echo $cores[$i] ?>')">
                        <input class="color1" style="border:none;color: transparent; width: 35px; height: 25px; background:<?php echo $color_scheme[0] ?>" value="<?php echo $color_scheme[0] ?>" />
                        <input class="color2" style="margin-left: -5px; border:none;color: transparent; width: 35px; height: 25px; background:<?php echo $color_scheme[1] ?>" value="<?php echo $color_scheme[1] ?>" />
                    </div>
                    <?php $i++; } ?>

                <form id="collection-colorset" class="layout-colorpicker" style="margin-top: 10px; margin-left: 3.5px;">
                    <label for="primary-custom-color" style="display: block; color: #0C698B;"> <?php _e('More options', 'tainacan'); ?> </label>
                    <div class="input" style="margin: 5px 0 5px 0">
                        <input type="text" id="primary-custom-color" value="#7AA7CF" name="color_scheme[primary_color]">
                        <input type="text" id="second-custom-color" value="#0C698B" name="color_scheme[secondary_color]">
                    </div>

                    <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
                    <input type="hidden" name="operation" value="update_color_scheme">

                    <input type="submit" value="<?php _e('Add','tainacan'); ?>" class="btn btn-primary">
                </form>
            </div>
            <h3 style="border-left: 4px solid #7AA7CF; border-radius: 0 2px 2px 0; background: white;"><?php _e('Layout','tainacan'); ?></h3>
            <div style="padding-left: 15px">
                <form method="POST" name="form_ordenation_search" id="form_ordenation_search" style="">
                    <input type="hidden" name="property_category_id"  value="<?php echo $category_root_id; ?>">
                    <input type="hidden" name="selected_view_mode" class="selected_view_mode" value="<?php echo $selected_view_mode ?>"/>

                    <!------------------- Modo de exibição dos itens -------------------------->
                    <div class="form-group">
                        <label for="collection_list_mode"><?php _e('Default list mode','tainacan'); ?></label>
                        <select name="collection_list_mode" id="collection_list_mode" class="form-control">
                            <option value="cards"><?php _e('Cards', 'tainacan'); ?></option>
                            <option value="gallery"><?php _e('Gallery', 'tainacan'); ?></option>
                            <option value="list"><?php _e('List', 'tainacan'); ?></option>
                        </select>
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
                    <input type="hidden" id="collection_id_order_form" name="collection_id" value="<?php echo $collection_id; ?>">
                    <input type="hidden" id="operation" name="operation" value="update_ordenation">
                    <button type="submit" id="submit_ordenation_form" class="btn btn-primary"><?php _e('Save','tainacan') ?></button>
                </form>
            </div>
        </div>

    </div>

    <?php $bg_url = get_stylesheet_directory_uri() . '/libraries/images/header-share-bg.png'; ?>

    <div class="col-md-10 preset-filters no-padding" style="background: #414042; padding-bottom: 20px;">
        <div class="categories_menu" id="personalize_search">
            <div id="tainacan-mini" class="col-md-11" style="float: none; margin: 0 auto; padding-top: 20px;">
                <header>
                    <div class="topo" style="background: #231F20; height: 30px; width: 100%;"></div>
                    <div style="background: url('<?php echo $bg_url ?>') no-repeat; background-color: #58595B;
                        background-position: right 2.8% top 10px; height: 120px; width: 100%;" class="capa-col"></div>
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