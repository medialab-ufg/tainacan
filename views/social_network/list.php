<?php
include_once('js/update_js.php');
include_once('js/import_js.php');
include_once('js/edit_js.php');
include_once('js/delete_js.php');
include_once('js/insert_js.php');
include_once('js/list_js.php');
include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
require_once(dirname(__FILE__) . '../../../models/social_network/Facebook/autoload.php');
session_start();
?>
<div class="col-md-12">
    <div class="col-md-12 no-padding" id="edit_mapping" style="display: none;"></div>
    <div class="col-md-12 config_default_style" id="list_social_network">
        <div class="">
            <div id="loader_videos" style="margin-left: 35%;margin-top:3%; display: none;"><img src="<?php echo get_template_directory_uri(), '/libraries/images/ajaxLoader.gif'; ?>" /></div>
            <h3 class="topo">
                <?php _e("Social Networks Conected to the Colection", 'tainacan'); ?>
                <?php ViewHelper::buttonVoltar() ?>
            </h3>
            <hr>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#aba-youtube-mapping" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Youtube', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-flickr-mapping" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Flickr', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-facebook-mapping" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Facebook', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-instagram-mapping" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Instagram', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-vimeo-mapping" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Vimeo', 'tainacan') ?></a></li>
            </ul>
            <div class="tab-content">
                <!-- Aba do youtube-->
                <div id="aba-youtube-mapping" class="tab-pane fade in active">

                    <div class="highlight">
                        <h3><?php _e("Youtube Channels", 'tainacan'); ?></h3>
                        <div id="list_youtube_channels">
                            <table  class="table table-bordered">
                                <th><?php _e('Identifier', 'tainacan'); ?></th>
                                <th><?php _e('Playlist', 'tainacan'); ?></th>
                                <th><?php _e('Edit', 'tainacan'); ?></th>
                                <th><?php _e('Delete', 'tainacan'); ?></th>
                                <th><?php _e('Import', 'tainacan'); ?></th>
                                <th><?php _e('Update', 'tainacan'); ?></th>
                                <tbody id="table_youtube_identifiers" >
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <input type="button" onclick="edit_mapping('youtube')" id="btn_mapping_youtube" name="btn_mapping_youtube" class="btn btn-primary social-networks-btn" value="<?php _e('Edit Youtube Mapping', 'tainacan'); ?>" />

                    <!--label for="channel_identifier"><?php _e('Entry channel youtube identifeir', 'tainacan'); ?></label>
                    <input type="text"  name="channel_identifier" id="youtube_identifier_input" style="width: 33.333%" placeholder="<?php _e('Type here', 'tainacan'); ?>" class="form-control" required /><br/>
                    <label for="youtube_playlist_identifier_input"><?php _e('Entry playlist youtube identifeir', 'tainacan'); ?></label>
                    <input type="text"  name="youtube_playlist_identifier_input" id="youtube_playlist_identifier_input" style="width: 33.333%" placeholder="<?php _e('Type here to get a specific playlist or leave blank to get all', 'tainacan'); ?>" class="form-control"/><br/>
                    <input type="button" id="btn_identifiers_youtube" name="addChannel" class="btn btn-default pull-left" value="Adicionar"  />
                    <input type="button" id="btn_identifiers_youtube_update" name="updateChannel" class="btn btn-default pull-left" value="Salvar Edição" />
                    <input type="button" id="btn_identifiers_youtube_cancel" name="calcelChannel" class="btn btn-default pull-left" value="Cancelar Edição" /-->

                </div>

                <!-- Aba do flickr-->
                <div id="aba-flickr-mapping" class="tab-pane fade">
                    <div class="highlight">
                        <h3><?php _e("Flickr Profiles", 'tainacan'); ?></h3>
                        <div id="list_perfil_flickr">
                            <table  class="table table-bordered">
                                <th><?php _e('User Name', 'tainacan'); ?></th>
                                <th><?php _e('Edit', 'tainacan'); ?></th>
                                <th><?php _e('Delete', 'tainacan'); ?></th>
                                <th><?php _e('Import', 'tainacan'); ?></th>
                                <th><?php _e('Update', 'tainacan'); ?></th>
                                <tbody id="table_flickr_identifiers" >
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <input type="button" onclick="edit_mapping('flickr')" id="btn_mapping_flickr" name="btn_mapping_flickr" class="btn btn-primary social-networks-btn" value="<?php _e('Edit Flickr Mapping', 'tainacan'); ?>" /><br><br>
                    <!--label for="flickr_identifiers"><?php _e('Entry an user name from a flickr profile', 'tainacan'); ?></label>
                    <input type="text"  name="flickr_identifiers" id="flickr_identifier_input" style="width: 33.333%" placeholder="Digite aqui" class="form-control"/><br/>
                    <input type="button" id="btn_identifiers_flickr" name="addChannel" class="btn btn-default pull-left" value="Adicionar"  />
                    <input type="button" id="btn_identifiers_flickr_update" name="updateFlickrProfileIdentifier" class="btn btn-default pull-left" value="Salvar Edição" />
                    <input type="button" id="btn_identifiers_flickr_cancel" name="calcelFlickrProfileIdentifier" class="btn btn-default pull-left" value="Cancelar Edição" /-->
                </div>

                <!-- Aba do facebook-->
                <div id="aba-facebook-mapping" class="tab-pane fade">
                    <div class="highlight">
                        <h3><?php _e("Facebook Profiles", 'tainacan'); ?></h3>
                            <!--?php
                            $config = get_option('socialdb_theme_options');
                            $app['app_id'] = $config['socialdb_fb_api_id'];
                            $app['app_secret'] = $config['socialdb_fb_api_secret'];

                            $fb = new Facebook\Facebook([
                                'app_id' => $app['app_id'],
                                'app_secret' => $app['app_secret'],
                                'default_graph_version' => 'v2.3',
                            ]);

                            $helper = $fb->getRedirectLoginHelper();
                            $permissions = ['user_photos', 'email', 'user_likes']; // optional
                            $loginUrl = $helper->getLoginUrl(get_bloginfo(template_directory) . '/controllers/social_network/facebook_controller.php?collection_id='.$collection_id.'&operation=getAccessToken',$permissions);

                            //echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
                        ?>
                        <a href="<?php echo $loginUrl; ?>" class="btn btn-success"><?php _e('Import Facebook Photos', 'tainacan'); ?></a>

                        <!--input type="button" id="btn_update_photos_facebook" name="btn_update_photos_facebook" class="btn btn-primary" disabled="disabled" value="<?php _e('Update Facebook Photos', 'tainacan'); ?>"  /-->
                    </div>
                    <input type="button" onclick="edit_mapping('facebook')" id="btn_mapping_facebook" name="btn_mapping_facebook" class="btn btn-primary social-networks-btn" value="<?php _e('Edit Facebook Mapping', 'tainacan'); ?>" /><br><br>

                </div>
                <!-- Aba do instagram-->
                <div id="aba-instagram-mapping" class="tab-pane fade">
                    <div class="highlight">
                        <h3><?php _e("Instagram Profiles", 'tainacan'); ?></h3>
                        <!--div id="list_perfil_instram">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('User Name', 'tainacan'); ?></th>
                            <th><?php _e('Edit', 'tainacan'); ?></th>
                            <th><?php _e('Delete', 'tainacan'); ?></th>
                            <th><?php _e('Import', 'tainacan'); ?></th>
                            <th><?php _e('Update', 'tainacan'); ?></th>
                            <tbody id="table_instagram_identifiers" >
                            </tbody>
                        </table>
                    </div-->
                    </div>

                    <input type="button" onclick="edit_mapping('instagram')" id="btn_mapping_instagram" name="btn_mapping_instagram" class="btn btn-primary social-networks-btn" value="<?php _e('Edit Instagram Mapping', 'tainacan'); ?>" /><br><br>
                    <!--label for="instagram_identifiers"><?php _e('Entry an user name from a instagram profile', 'tainacan'); ?></label>
                    <input type="text"  name="instagram_identifiers" id="instagram_identifier_input" style="width: 33.333%" placeholder="Digite aqui" class="form-control"/><br/>
                    <input type="button" id="btn_identifiers_instagram" name="addChannel" class="btn btn-default pull-left" value="Adicionar"  />
                    <input type="button" id="btn_identifiers_instagram_update" name="updateInstagramProfileIdentifier" class="btn btn-default pull-left" value="Salvar Edição" />
                    <input type="button" id="btn_identifiers_instagram_cancel" name="calcelInstagramProfileIdentifier" class="btn btn-default pull-left" value="Cancelar Edição" /-->
                </div>

                <!-- Aba do vimeo-->
                <div id="aba-vimeo-mapping" class="tab-pane fade">
                    <div class="highlight">
                        <h3><?php _e("Vimeo Profiles", 'tainacan'); ?></h3>
                        <!--div id="list_perfil_instram">
                        <table  class="table table-bordered" style="background-color: #d9edf7;">
                            <th><?php _e('User Name', 'tainacan'); ?></th>
                            <th><?php _e('Edit', 'tainacan'); ?></th>
                            <th><?php _e('Delete', 'tainacan'); ?></th>
                            <th><?php _e('Import', 'tainacan'); ?></th>
                            <th><?php _e('Update', 'tainacan'); ?></th>
                            <tbody id="table_instagram_identifiers" >
                            </tbody>
                        </table>
                    </div-->
                    </div>

                    <input type="button" onclick="edit_mapping('vimeo')" id="btn_mapping_vimeo" name="btn_mapping_vimeo" class="btn btn-primary social-networks-btn" value="<?php _e('Edit Vimeo Mapping', 'tainacan'); ?>" /><br><br>
                    <!--label for="instagram_identifiers"><?php _e('Entry an user name from a instagram profile', 'tainacan'); ?></label>
                    <input type="text"  name="instagram_identifiers" id="instagram_identifier_input" style="width: 33.333%" placeholder="Digite aqui" class="form-control"/><br/>
                    <input type="button" id="btn_identifiers_instagram" name="addChannel" class="btn btn-default pull-left" value="Adicionar"  />
                    <input type="button" id="btn_identifiers_instagram_update" name="updateInstagramProfileIdentifier" class="btn btn-default pull-left" value="Salvar Edição" />
                    <input type="button" id="btn_identifiers_instagram_cancel" name="calcelInstagramProfileIdentifier" class="btn btn-default pull-left" value="Cancelar Edição" /-->
                </div>
            </div>
        </div>
    </div>
</div> 

