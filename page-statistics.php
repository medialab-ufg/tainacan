<?php
/**
 * Template Name: Statistics
 */

$home_url = get_bloginfo('url');
$repository_title = get_bloginfo('name');
$repo_desc = get_bloginfo('description');
if ( current_user_can('manage_options') ):
    get_header(); ?>
    <script type="text/javascript">
        $(function() {
            $("#expand-top-search").hide();
            $.ajax({
                url: $('.stat_path').val() + '/controllers/log/log_controller.php',
                type: 'POST',
                data: { operation: 'show_statistics' }
            }).done(function(res) {
                $('#tainacan-stats').html(res);
            });
        });
    </script>

    <div id="stats-cover">
        <h1> <?php echo $repository_title; ?> </h1>
        <h3> <?php echo $repo_desc; ?> </h3>

        <div class="col-md-3 pull-right" style="text-align:right; margin-top: -20px">
            <a target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo $home_url; ?>&amp;p[title]=<?php echo $repository_title; ?>&amp;p[summary]=<?php echo $repo_desc ?>">
                <div class="fab"><span data-icon="&#xe021;"></span></div>
            </a>

            <a target="_blank" href="https://plus.google.com/share?url=<?php echo $home_url; ?>">
                <div class="fab"><span data-icon="&#xe01b;"></span></div>
            </a>

            <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo $home_url; ?>&amp;text=<?php echo $repository_title . " - " . $repo_desc; ?>&amp;via=socialdb">
                <div class="fab"><span data-icon="&#xe005;"></span></div>
            </a>

            <a target="_blank" href="<?php bloginfo('rss_url'); ?>">
                <div class="fab"><span data-icon="&#xe00c;"></span></div>
            </a>

            <!-- ******************** TAINACAN: IFRAME URL ******************** -->
            <button id="iframebutton" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="" data-original-title="" title="Emded URL">
                <div class="fab"><small><h6><b><></b></h6></small></div>
            </button>
            <a href="#" id="resources_collection_button" class="dropdown-toggle"  data-toggle="dropdown" role="button" aria-expanded="false" >
                <div class="fab">
                    <div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
                </div>
            </a>
            <ul id="resources_collection_dropdown" class="dropdown-menu" role="menu">
                <li>
                    <a target="_blank" href="<?php echo $home_url ?>?all.rdf"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;</a>
                </li>
                <?php if(is_restful_active()): ?>
                    <li>
                        <a href="<?php echo $home_url . '/wp-json/posts/?type=socialdb_collection' ?>"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>&nbsp; </a>
                    </li>
                <?php endif; ?>
                <?php if (get_option('collection_root_id') != $collection_post->ID) { ?>
                    <li>
                        <a style="cursor: pointer;" onclick="export_selected_objects()"  ><span class="glyphicon glyphicon-upload"></span> <?php _e('CSV', 'tainacan'); ?>&nbsp;
                        </a>
                    </li>
                <?php } ?>
                <li>
                    <a onclick="showGraph('<?php echo get_the_permalink($collection_post->ID) ?>?all.rdf')"  style="cursor: pointer;"   >
                        <span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>&nbsp;
                    </a>
                </li>
            </ul>

        </div>

    </div>

    <input type="hidden" class="stat_path" value="<?php echo get_template_directory_uri() ?>">
    <input type="hidden" id="src" value="<?php echo get_template_directory_uri() ?>">

<!--  <div id="configuration" class="col-md-12"> </div>-->

    </header> <!-- DO NOT ERASE -->


    <div id='tainacan-stats' class='row'>
        <center style="margin: 40px 0 40px 0">
            <img src="<?php echo get_template_directory_uri() . '/libraries/images/ajaxLoader.gif' ?>" width="64px" height="64px" />
            <br> <br>
            <?php _t('Loading Statistics ...', 1); ?>
        </center>
    </div>
    <?php
    require_once (dirname(__FILE__) . '/extras/routes/routes.php');
    get_footer();
else:
    $home = home_url("/");
    header("Location: " . $home);
endif;