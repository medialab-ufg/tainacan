<!-- compartilhamentos -->
<!-- ******************** TAINACAN: compartilhar colecao (titutlo,imagem e descricao) no FACEBOOK ******************** -->
<a class="share-link" target="_blank" rel="noopener"
   href="http://www.facebook.com/sharer.php?u=<?php echo the_permalink($collection_post->ID); ?>">
	<div class="fab"><span data-icon="&#xe021;"></span></div>
</a>

<!-- ******************** TAINACAN: compartilhar colecao (titulo,imagem) no GOOGLE PLUS ******************** -->
<a target="_blank" class="share-link" rel="noopener"
   href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_post->ID); ?>">
	<div class="fab"><span data-icon="&#xe01b;"></span></div>
</a>

<!-- ************************ TAINACAN: compartilhar colecao  no TWITTER ******************** -->
<a target="_blank" class="share-link" rel="noopener"
   href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_post->ID); ?>&amp;text=<?php echo htmlentities($collection_post->post_title); ?>&amp;via=socialdb">
	<div class="fab"><span data-icon="&#xe005;"></span></div>
</a>
<!-- ******************** TAINACAN: RSS da colecao com seus metadados ******************** -->
<?php if (get_option('collection_root_id') != $collection_post->ID): ?>
	<a target="_blank" class="share-link" rel="noopener"
	   href="<?php echo site_url() . '/feed_collection/' . $collection_post->post_name ?>">
		<div class="fab"><span data-icon="&#xe00c;"></span></div>
	</a>
<?php endif; ?>
<!-- ******************** TAINACAN: exportar CSV os items da colecao que estao filtrados ******************** -->
<?php if (get_option('collection_root_id') != $collection_post->ID) { ?>
	<!--a style="cursor: pointer;" onclick="export_selected_objects()">
		<div class="fab"><small><h6><b>csv</b></h6></small></div>
	</a-->
<?php } ?>
<!--button id="iframebutton" data-container="body" data-toggle="popover" data-placement="left"
		data-title="URL Iframe" data-content="" data-original-title="" title="Embed URL">
	<div class="fab">
		<small><h6><b><></b></h6></small>
	</div>
</button-->
<script>
    set_popover_content($("#socialdb_permalink_collection").val());
</script>

<!--button style="float:right;margin-left:5px;" id="iframebutton" type="button" class="btn btn-default btn-sm" data-container="body" data-toggle="popover" data-placement="left" data-title="URL Iframe" data-content="">
	<span class="glyphicon glyphicon-link"></span>
</button-->
<!-- ******************** TAINACAN: se o plugin de restful estiver ativo ***-->
<?php if (is_restful_active()): ?>
	<!--a target="_blank" href="<?php echo site_url() . '/wp-json/posts/' . $collection_post->ID . '/?type=socialdb_collection' ?>">
                               <div class="fab"><small><h6><b>json</b></h6></small></div>
                            </a>
                        <!--a style="cursor: pointer;" onclick="export_selected_objects_json()">
                            <div class="fab"><small><h6><b>items</b></h6></small></div>
                        </a-->
<?php endif; ?>
<div class="dropdown collec_menu_opnr" style="padding:0;">
	<a href="javascript:void(0)" id="resources_collection_button" class="dropdown-toggle share-link" data-toggle="dropdown"
	   role="button" aria-expanded="false">
		<div class="fab">
			<div style="font-size:1em; cursor:pointer;" data-icon="&#xe00b;"></div>
		</div>
	</a>
	<ul id="resources_collection_dropdown" class="dropdown-menu pull-right dropdown-show" role="menu">
		<li>
			<a target="_blank" href="<?php echo get_the_permalink($collection_post->ID) ?>?all.rdf"><span
					class="glyphicon glyphicon-upload"></span> <?php _e('RDF', 'tainacan'); ?>&nbsp;
			</a>
		</li>
		<?php if (is_restful_active()): ?>
			<li>
				<a href="<?php echo site_url() . '/wp-json/posts/' . $collection_post->ID . '/?type=socialdb_collection' ?>"><span
						class="glyphicon glyphicon-upload"></span> <?php _e('JSON', 'tainacan'); ?>
					&nbsp;
				</a>
			</li>
		<?php endif; ?>
		<?php if (get_option('collection_root_id') != $collection_post->ID) { ?>
			<li>
				<a style="cursor: pointer;" onclick="export_selected_objects()"><span
						class="glyphicon glyphicon-upload"></span> <?php _e('CSV', 'tainacan'); ?>
					&nbsp;
				</a>
			</li>
		<?php } ?>
		<li>
			<a onclick="showGraph('<?php echo get_the_permalink($collection_post->ID) ?>?all.rdf')"
			   style="cursor: pointer;">
				<span class="glyphicon glyphicon-upload"></span> <?php _e('Graph', 'tainacan'); ?>
				&nbsp;
			</a>
		</li>
		<?php if (get_post_meta($collection_post->ID, 'socialdb_collection_mapping_exportation_active', true)): ?>
			<li>
				<a href="<?php echo site_url() ?>/oai/socialdb-oai/?verb=ListRecords&metadataPrefix=oai_dc&set=<?php echo $collection_post->ID ?>"
				   style="cursor: pointer;">
                                            <span
	                                            class="glyphicon glyphicon-upload"></span> <?php _e('OAI-PMH', 'tainacan'); ?>
					&nbsp;
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>
<!-- ******************** TAINACAN: Comentarios ******************** -->
<a style="cursor: pointer;" onclick="showPageCollectionPage()">
	<div class="fab"><span style="font-size: medium;"
	                       class="glyphicon glyphicon-comment"></span></div>
</a>
<div class="dropdown collec_menu_opnr " style="padding:0px;">
	<a id="iframebutton" class="dropdown-toggle" data-toggle="dropdown"
	   role="button" aria-expanded="false">
		<div class="fab">
			<small><h6><b><></b></h6></small>
		</div>
	</a>
	<ul id="iframebutton_dropdown" class="dropdown-menu pull-right dropdown-show" role="menu" style="color: #000;">
	</ul>
</div>