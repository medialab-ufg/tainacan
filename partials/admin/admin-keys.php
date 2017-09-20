<?php
include_once ( dirname(__FILE__) . '/../../helpers/view_helper.php');
include_once ( dirname(__FILE__) . '/../../views/theme_options/js/edit_js.php');

$SocialDB_Api = get_option('socialdb_theme_options');
extract($SocialDB_Api);
?>

<div class="col-md-12">
    <div id="api-keys" class="col-md-12 config_default_style">
        <div class="">
            <?php ViewHelper::render_config_title( __("API Keys Configuration", 'tainacan') ); ?>

            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#aba-youtube-repository" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Youtube', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-flickr-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Flickr', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-faceboook-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Facebook', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-instagram-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Instagram', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-vimeo-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Vimeo', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-embed-ly-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Embed.ly', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-google-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Google / Google +', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-europeana" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Europeana', 'tainacan') ?></a></li>
            </ul>
            <div class="tab-content" style="padding: 15px;">
                <!-- Aba do youtube-->
                <div id="aba-youtube-repository" class="tab-pane fade in active">
                    <?php $socialdb_youtube_api_id = (isset($socialdb_youtube_api_id) ? $socialdb_youtube_api_id : ''); ?>

                    <form name="formYoutubeApi" id="formYoutubeApi" method="post">
                        <input type="hidden" id="operation_Youtube" name="operation" value="update_options" />
                        <label for="socialdb_youtube_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_youtube_api_id" id="socialdb_youtube_api_id" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_youtube_api_id; ?>"/><br/>
                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>
                </div>

                <!-- Aba do flickr-->
                <div id="aba-flickr-repository" class="tab-pane fade">
                    <?php $socialdb_flickr_api_id = (isset($socialdb_flickr_api_id) ? $socialdb_flickr_api_id : ''); ?>

                    <form name="formFlickrApi" id="formFlickrApi" method="post">
                        <input type="hidden" id="operation_Flickr" name="operation" value="update_options" />
                        <label for="socialdb_flickr_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_flickr_api_id" id="socialdb_flickr_api_id" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_flickr_api_id; ?>"/><br/>
                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>
                </div>

                <!-- Aba do facebook-->
                <div id="aba-faceboook-repository" class="tab-pane fade">
                    <?php $socialdb_fb_api_id = (isset($socialdb_fb_api_id) ? $socialdb_fb_api_id : ''); ?>
                    <?php $socialdb_fb_api_secret = (isset($socialdb_fb_api_secret) ? $socialdb_fb_api_secret : ''); ?>

                    <form name="formFacebookApi" id="formFacebookApi" method="post">
                        <input type="hidden" id="operation_Facebook" name="operation" value="update_options" />
                        <label for="socialdb_fb_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_fb_api_id" id="socialdb_fb_api_id" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_fb_api_id; ?>"/><br/>

                        <label for="socialdb_fb_api_secret"><?php _e('API Secret', 'tainacan'); ?></label>
                        <input type="text" name="socialdb_fb_api_secret" id="socialdb_fb_api_secret" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_fb_api_secret; ?>"/><br/>

                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>

                </div>
                <!-- Aba do instagram-->
                <div id="aba-instagram-repository" class="tab-pane fade">
                    <?php $socialdb_instagram_api_id = (isset($socialdb_instagram_api_id) ? $socialdb_instagram_api_id : ''); ?>
                    <?php $socialdb_instagram_api_secret = (isset($socialdb_instagram_api_secret) ? $socialdb_instagram_api_secret : ''); ?>

                    <form name="formInstagramApi" id="formInstagramApi" method="post">
                        <input type="hidden" id="operation_Instagram" name="operation" value="update_options" />
                        <label for="socialdb_instagram_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text" name="socialdb_instagram_api_id" id="socialdb_instagram_api_id" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_instagram_api_id; ?>"/><br/>

                        <label for="socialdb_instagram_api_secret"><?php _e('API Secret', 'tainacan'); ?></label>
                        <input type="text" name="socialdb_instagram_api_secret" id="socialdb_instagram_api_secret" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_instagram_api_secret; ?>"/><br/>

                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>

                </div>
                <!-- Aba do Vimeo-->
                <div id="aba-vimeo-repository" class="tab-pane fade">
                    <?php $socialdb_vimeo_client_id = (isset($socialdb_vimeo_client_id) ? $socialdb_vimeo_client_id : ''); ?>
                    <?php $socialdb_vimeo_api_secret = (isset($socialdb_vimeo_api_secret) ? $socialdb_vimeo_api_secret : ''); ?>

                    <form name="formVimeoApi" id="formVimeoApi" method="post">
                        <input type="hidden" id="operation_Vimeo" name="operation" value="update_options" />
                        <label for="socialdb_vimeo_client_id"><?php _e('API Client ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_vimeo_client_id" id="socialdb_vimeo_client_id" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_vimeo_client_id; ?>"/><br/>

                        <label for="socialdb_vimeo_api_secret"><?php _e('API Client Secrets', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_vimeo_api_secret" id="socialdb_vimeo_api_secret" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_vimeo_api_secret; ?>"/><br/>

                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>

                </div>
                <!-- Aba do Embed.Ly-->
                <div id="aba-embed-ly-repository" class="tab-pane fade">
                    <?php $socialdb_embed_api_id = (isset($socialdb_embed_api_id) ? $socialdb_embed_api_id : ''); ?>

                    <form name="formEmbedApi" id="formEmbedApi" method="post">
                        <input type="hidden" id="operation_Embed" name="operation" value="update_options" />
                        <label for="socialdb_embed_api_id"><?php _e('Embed Ly API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_embed_api_id" id="socialdb_embed_api_id"  placeholder="<?php _e("Type the Embed Ly API ID", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_embed_api_id; ?>"/><br/>
                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>
                </div>
                <!-- Aba do Google-->
                <div id="aba-google-repository" class="tab-pane fade">
                    <?php $socialdb_google_client_id = (isset($socialdb_google_client_id) ? $socialdb_google_client_id : ''); ?>
                    <?php $socialdb_google_secret_key = (isset($socialdb_google_secret_key) ? $socialdb_google_secret_key : ''); ?>
                    <?php $socialdb_google_api_key = (isset($socialdb_google_api_key) ? $socialdb_google_api_key : ''); ?>

                    <form name="formGoogleApi" id="formGoogleApi" method="post">
                        <input type="hidden" id="operation_google" name="operation" value="update_options" />
                        <label for="socialdb_google_client_id"><?php _e('Client ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_client_id" id="socialdb_google_client_id"  placeholder="<?php _e("Type here"); ?>" class="form-control" value="<?php echo $socialdb_google_client_id; ?>"/><br>

                        <label for="socialdb_google_secret_key"><?php _e('Client Secret Key', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_secret_key" id="socialdb_google_secret_key"  placeholder="<?php _e("Type here"); ?>" class="form-control" value="<?php echo $socialdb_google_secret_key; ?>"/><br>

                        <label for="socialdb_google_redirect_uri"><?php _e('Redirect URI', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_redirect_uri" id="socialdb_google_redirect_uri" placeholder="<?php _e("Type here"); ?>" class="form-control" value="<?php echo site_url() . "/wp-content/themes/theme_socialdb/controllers/user/user_controller.php?operation=return_login_gplus"; ?>" disabled="disabled"/><br/>

                        <label for="socialdb_google_api_key"><?php _e('API Key', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_api_key" id="socialdb_google_api_key"  placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_google_api_key; ?>"/><br/>

                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>

                </div>
                <!-- Aba do facebook-->
                <div id="aba-europeana" class="tab-pane fade">
                    <?php $socialdb_eur_api_key = (isset($socialdb_eur_api_key) ? $socialdb_eur_api_key : ''); ?>
                    <?php $socialdb_eur_private_key = (isset($socialdb_eur_private_key) ? $socialdb_eur_private_key : ''); ?>

                    <form name="formEuropeanaApi" id="formEuropeanaApi" method="post">
                        <input type="hidden" id="operation_Europeana" name="operation" value="update_options" />
                        <label for="socialdb_eur_api_key"><?php _e('API KEY', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_eur_api_key" id="socialdb_eur_api_key" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_eur_api_key; ?>"/><br/>

                        <label for="socialdb_eur_private_key"><?php _e('Private Key', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_eur_private_key" id="socialdb_eur_private_key" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_eur_private_key; ?>"/><br/>

                        <?php echo ViewHelper::render_default_submit_button(); ?>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

