<div class="col-md-12 repository-sharings">
    <div class="row">
        <div class="col-md-6 pull-right" style="text-align:right;padding:10px 0;"> <!-- compartilhamentos -->
            
            <!-- ******************** TAINACAN: compartilhar colecao (titutlo,imagem e descricao) no FACEBOOK ******************** -->
            <a target="_blank" href="http://www.facebook.com/sharer/sharer.php?s=100&amp;p[url]=<?php echo get_the_permalink($collection_post->ID); ?>&amp;p[images][0]=<?php echo wp_get_attachment_url(get_post_thumbnail_id($collection_post->ID)); ?>&amp;p[title]=<?php echo htmlentities($collection_post->post_title); ?>&amp;p[summary]=<?php echo strip_tags($collection_post->post_content); ?>">
                <div class="fab"><span data-icon="&#xe021;"></span></div>
            </a>

            <!-- ******************** TAINACAN: compartilhar colecao (titulo,imagem) no GOOGLE PLUS ******************** -->
            <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_post->ID); ?>">
                <div class="fab"><span data-icon="&#xe01b;"></span></div>
            </a>

            <!-- ************************ TAINACAN: compartilhar colecao  no TWITTER ******************** -->
            <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_post->ID); ?>&amp;text=<?php echo htmlentities($collection_post->post_title); ?>&amp;via=socialdb">
                <div class="fab"><span data-icon="&#xe005;"></span></div>
            </a>
            
            <!-- ******************** TAINACAN: RSS da colecao com seus metadados ******************** -->
            <?php if (get_option('collection_root_id') != $collection_post->ID): ?>
                <a target="_blank" href="<?php echo site_url() . '/feed/'; ?>">
                    <div class="fab"><span data-icon="&#xe00c;"></span></div>
                </a>
            <?php endif; ?>
            
            <!-- ******************** TAINACAN: exportar CSV os items da colecao que estao filtrados ******************** -->
            <?php if (get_option('collection_root_id') != $collection_post->ID) { ?>
                <a style="cursor: pointer;" onclick="export_selected_objects()">
                    <div class="fab"><small><h6><b>csv</b></h6></small></div>
                </a>
            <?php } ?>

            <button id="iframebutton" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="" data-original-title="" title="Embed URL">
              <div class="fab"><small><h6><b><></b></h6></small></div>
            </button>

            <script type="text/javascript"> /*
                function set_popover_content(content) {
                    $('[data-toggle="popover"]').popover();
                    var myPopover = $('#iframebutton').data('popover');
                    $('#iframebutton').popover('hide');
                    if (myPopover) {
                        myPopover.options.html = true;
                        //<iframe width="560" height="315" src="https://www.youtube.com/embed/CGyEd0aKWZE" frameborder="0" allowfullscreen></iframe>
                        myPopover.options.content = '<form>Search URL:&nbsp<input type="text" style="width:165px;" value="' + content + '" /><br><br>Iframe:&nbsp<input type="text" style="width:200px;" value="<iframe style=\'width:100%\' height=\'1000\' src=\'' + content + '\' frameborder=\'0\'></iframe>" /></form>';
                    }
                }
                set_popover_content($("#socialdb_permalink_collection").val() + '?' + elem.url + '&is_filter=1'); */
            </script>

            <!--button style="float:right;margin-left:5px;" id="iframebutton" type="button" class="btn btn-default btn-sm" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="">
                <span class="glyphicon glyphicon-link"></span>
            </button-->
        </div>
    </div>
</div>
