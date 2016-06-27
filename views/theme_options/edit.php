<?php
include_once('../../../../../wp-config.php');
include_once('../../../../../wp-load.php');
include_once('../../../../../wp-includes/wp-db.php');
include_once('js/edit_js.php');
?>
<div class="fuelux">
    <div id="MyWizard" class="fuelux wizard">

        <!--ul class="steps fuelux step-content">
            <a href="#"><li ><span class="fuelux badge">1</span><?php echo __("Configuration", 'tainacan') ?><span class="chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">2</span><?php echo __("Categories", 'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">3</span><?php echo __("Properties", 'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">4</span><?php echo __("Rankings", 'tainacan') ?><span class="fuelux chevron"></span></li></a>
            <a onclick="showAPIConfiguration('<?php echo get_template_directory_uri() ?>');" href="#"><li class="active"><span class="badge badge-info">5</span><?php echo __("Social / API Keys") ?><span class="fuelux chevron"></span></li></a>
            <a href="#"><li><span class="fuelux badge">6</span><?php echo __("Design") ?><span class="fuelux chevron"></span></li></a>
        </ul
        <div class="fuelux actions">
             <a href="#" class="btn btn-mini btn-prev"> <span class="glyphicon glyphicon-chevron-left"></span></i><?php echo __("Previous") ?></a>
             
             <a href="#" class="btn btn-mini btn-next" data-last="Finish"><?php echo __("Next") ?><span class="glyphicon glyphicon-chevron-right"></span></i></a>
        </div-->
    </div>
</div>
<div class="row">

    <?php include_once "common/redirect_button.php" ?>

    <div class="col-md-9">
        <div class="row">
            <h3><?php _e("API Keys Configuration", 'tainacan'); ?></h3>
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#aba-youtube-repository" aria-controls="property_data_tab" role="tab" data-toggle="tab"><?php _e('Youtube', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-flickr-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Flickr', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-faceboook-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Facebook', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-instagram-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Instagram', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-vimeo-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Vimeo', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-embed-ly-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Embed.ly', 'tainacan') ?></a></li>
                <li role="presentation"><a href="#aba-google-repository" aria-controls="property_object_tab" role="tab" data-toggle="tab"><?php _e('Google / Google +', 'tainacan') ?></a></li>
            </ul>
            <div class="tab-content">
                <!-- Aba do youtube-->
                <div id="aba-youtube-repository" class="tab-pane fade in active">
                    <?php $socialdb_youtube_api_id = (isset($socialdb_youtube_api_id) ? $socialdb_youtube_api_id : ''); ?>
                    <h3><?php _e("Youtube", 'tainacan'); ?></h3>

                    <form name="formYoutubeApi" id="formYoutubeApi" method="post">
                        <input type="hidden" id="operation_Youtube" name="operation" value="update_options" />
                        <label for="socialdb_youtube_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_youtube_api_id" id="socialdb_youtube_api_id" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_youtube_api_id; ?>"/></br>
                        <input type="submit" class="btn btn-default pull-left" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>

                </div>

                <!-- Aba do flickr-->
                <div id="aba-flickr-repository" class="tab-pane fade">
                    <?php $socialdb_flickr_api_id = (isset($socialdb_flickr_api_id) ? $socialdb_flickr_api_id : ''); ?>
                    <h3><?php _e("Flickr", 'tainacan'); ?></h3>

                    <form name="formFlickrApi" id="formFlickrApi" method="post">
                        <input type="hidden" id="operation_Flickr" name="operation" value="update_options" />
                        <label for="socialdb_flickr_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_flickr_api_id" id="socialdb_flickr_api_id" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_flickr_api_id; ?>"/></br>
                        <input type="submit" class="btn btn-default pull-left" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>
                </div>

                <!-- Aba do facebook-->
                <div id="aba-faceboook-repository" class="tab-pane fade">
                    <?php $socialdb_fb_api_id = (isset($socialdb_fb_api_id) ? $socialdb_fb_api_id : ''); ?>
                    <?php $socialdb_fb_api_secret = (isset($socialdb_fb_api_secret) ? $socialdb_fb_api_secret : ''); ?>
                    <h3><?php _e("Facebook", 'tainacan'); ?></h3>

                    <form name="formFacebookApi" id="formFacebookApi" method="post">
                        <input type="hidden" id="operation_Facebook" name="operation" value="update_options" />
                        <label for="socialdb_fb_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_fb_api_id" id="socialdb_fb_api_id" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_fb_api_id; ?>"/></br>

                        <label for="socialdb_fb_api_secret"><?php _e('API Secret', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_fb_api_secret" id="socialdb_fb_api_secret" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_fb_api_secret; ?>"/></br>

                        <input type="submit" class="btn btn-default pull-left" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>

                </div>
                <!-- Aba do instagram-->
                <div id="aba-instagram-repository" class="tab-pane fade">
                    <?php $socialdb_instagram_api_id = (isset($socialdb_instagram_api_id) ? $socialdb_instagram_api_id : ''); ?>
                    <?php $socialdb_instagram_api_secret = (isset($socialdb_instagram_api_secret) ? $socialdb_instagram_api_secret : ''); ?>
                    <h3><?php _e("Instagram", 'tainacan'); ?></h3>

                    <form name="formInstagramApi" id="formInstagramApi" method="post">
                        <input type="hidden" id="operation_Instagram" name="operation" value="update_options" />
                        <label for="socialdb_instagram_api_id"><?php _e('API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_instagram_api_id" id="socialdb_instagram_api_id" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_instagram_api_id; ?>"/></br>

                        <label for="socialdb_instagram_api_secret"><?php _e('API Secret', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_instagram_api_secret" id="socialdb_instagram_api_secret" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_instagram_api_secret; ?>"/></br>

                        <input type="submit" class="btn btn-default pull-left" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>

                </div>
                <!-- Aba do Vimeo-->
                <div id="aba-vimeo-repository" class="tab-pane fade">
                    <?php $socialdb_vimeo_client_id = (isset($socialdb_vimeo_client_id) ? $socialdb_vimeo_client_id : ''); ?>
                    <?php $socialdb_vimeo_api_secret = (isset($socialdb_vimeo_api_secret) ? $socialdb_vimeo_api_secret : ''); ?>
                    <h3><?php _e("Vimeo", 'tainacan'); ?></h3>

                    <form name="formVimeoApi" id="formVimeoApi" method="post">
                        <input type="hidden" id="operation_Vimeo" name="operation" value="update_options" />
                        <label for="socialdb_vimeo_client_id"><?php _e('API Client ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_vimeo_client_id" id="socialdb_vimeo_client_id" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_vimeo_client_id; ?>"/></br>

                        <label for="socialdb_vimeo_api_secret"><?php _e('API Client Secrets', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_vimeo_api_secret" id="socialdb_vimeo_api_secret" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_vimeo_api_secret; ?>"/></br>

                        <input type="submit" class="btn btn-default pull-left" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>

                </div>
                <!-- Aba do Embed.Ly-->
                <div id="aba-embed-ly-repository" class="tab-pane fade">
                    <?php $socialdb_embed_api_id = (isset($socialdb_embed_api_id) ? $socialdb_embed_api_id : ''); ?>
                    <h3><?php _e("Embed.Ly", 'tainacan'); ?></h3>

                    <form name="formEmbedApi" id="formEmbedApi" method="post">
                        <input type="hidden" id="operation_Embed" name="operation" value="update_options" />
                        <label for="socialdb_embed_api_id"><?php _e('Embed Ly API ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_embed_api_id" id="socialdb_embed_api_id" style="width: 33.333%" placeholder="<?php _e("Type the Embed Ly API ID", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_embed_api_id; ?>"/></br>
                        <input type="submit" class="btn btn-default pull-left" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>
                </div>
                <!-- Aba do Google-->
                <div id="aba-google-repository" class="tab-pane fade">
                    <?php $socialdb_google_client_id = (isset($socialdb_google_client_id) ? $socialdb_google_client_id : ''); ?>
                    <?php $socialdb_google_secret_key = (isset($socialdb_google_secret_key) ? $socialdb_google_secret_key : ''); ?>
                    <?php $socialdb_google_api_key = (isset($socialdb_google_api_key) ? $socialdb_google_api_key : ''); ?>
                    <h3><?php _e("Google / Google +", 'tainacan'); ?></h3>

                    <form name="formGoogleApi" id="formGoogleApi" method="post">
                        <input type="hidden" id="operation_google" name="operation" value="update_options" />
                        <label for="socialdb_google_client_id"><?php _e('Client ID', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_client_id" id="socialdb_google_client_id" style="width: 33.333%" placeholder="<?php _e("Type here"); ?>" class="form-control" value="<?php echo $socialdb_google_client_id; ?>"/><br>

                        <label for="socialdb_google_secret_key"><?php _e('Client Secret Key', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_secret_key" id="socialdb_google_secret_key" style="width: 33.333%" placeholder="<?php _e("Type here"); ?>" class="form-control" value="<?php echo $socialdb_google_secret_key; ?>"/><br>

                        <label for="socialdb_google_redirect_uri"><?php _e('Redirect URI', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_redirect_uri" id="socialdb_google_redirect_uri" placeholder="<?php _e("Type here"); ?>" class="form-control" value="<?php echo site_url() . "/wp-content/themes/theme_socialdb/controllers/user/user_controller.php?operation=return_login_gplus"; ?>" disabled="disabled"/></br>

                        <label for="socialdb_google_api_key"><?php _e('API Key', 'tainacan'); ?></label>
                        <input type="text"  name="socialdb_google_api_key" id="socialdb_google_api_key" style="width: 33.333%" placeholder="<?php _e("Type here", 'tainacan'); ?>" class="form-control" value="<?php echo $socialdb_google_api_key; ?>"/></br>

                        <input type="submit" class="btn btn-primary btn-lg  pull-right" value="<?php _e("Save", 'tainacan'); ?>"  />
                    </form>

                </div>
            </div>
        </div>
    </div>
</div> 

