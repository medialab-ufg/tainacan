<?php
$options = get_option('socialdb_theme_options');
$collection_default = get_option('disable_empty_collection');
$_special_configs = [
    'info_messages' => ( (isset($_GET['info_messages'])) ? trim($_GET['info_messages']) : ''),
    'item' => ( (isset($_GET['item'])) ? trim($_GET['item']) : ''),
    'category' => ( (isset($_GET['category'])) ? trim($_GET['category']) : ''),
    'tag' => ( (isset($_GET['tag'])) ? trim($_GET['tag']) : ''),
    'tax' => ( (isset($_GET['tax'])) ? trim($_GET['tax']) : '')
];
?>
<!-- TAINACAN: Hiddens responsaveis em realizar ações do repositório -->
<input type="hidden" id="show_collection_default" name="show_collection_default"
       value="<?php echo (!$collection_default || $collection_default === 'false') ? 'show' : 'hide'; ?>" />
<input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>" />
<input type="hidden" id="repository_main_page" name="repository_main_page" value="true" />
<input type="hidden" id="collection_root_url" value="<?php echo get_the_permalink(get_option('collection_root_id')) ?>" />
<input type="hidden" id="socialdb_fb_api_id" name="socialdb_fb_api_id" value="<?php echo $options['socialdb_fb_api_id']; ?>" />
<input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>" />
<input type="hidden" id="collection_id" name="collection_id" value="<?php echo get_option('collection_root_id'); ?>" />
<input type="hidden" id="max_collection_showed" name="max_collection_showed" value="20" />
<input type="hidden" id="total_collections" name="total_collections" value="" />
<input type="hidden" id="last_index" name="last_index" value="0" />

<input type="hidden" id="info_messages" name="info_messages" value="<?php echo $_special_configs['info_messages']; ?>" />
<!-- PAGINA DO ITEM -->
<input type="hidden" id="object_page" name="object_page" value="<?php echo $_special_configs['item']; ?>" />
<!-- PAGINA DA CATEGORIA -->
<input type="hidden" id="category_page" name="category_page" value="<?php echo $_special_configs['category']; ?>" />
<!-- PAGINA DA PROPRIEDADE -->
<input type="hidden" id="property_page" name="property_page" value="<?php echo $_special_configs['category']; ?>" />
<!-- PAGINA DA TAG -->
<input type="hidden" id="tag_page" name="tag_page" value="<?php echo $_special_configs['tag']; ?>" />
<!-- PAGINA DA TAXONOMIA -->
<input type="hidden" id="tax_page" name="object_page" value="<?php echo $_special_configs['tax']; ?>" />