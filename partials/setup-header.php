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
$root_id = get_option('collection_root_id');
?>
<!-- TAINACAN: Hiddens responsaveis em realizar ações do repositório -->
<input type="hidden" id="show_collection_default" name="show_collection_default" value="<?php echo (!$collection_default || $collection_default === 'false') ? 'show' : 'hide'; ?>" />
<input type="hidden" id="src" name="src" value="<?php echo get_template_directory_uri() ?>" />
<input type="hidden" id="repository_main_page" name="repository_main_page" value="<?php echo is_front_page() ?>" />
<input type="hidden" id="collection_root_url" value="<?php echo get_the_permalink($root_id) ?>" />
<input type="hidden" id="socialdb_fb_api_id" name="socialdb_fb_api_id" value="<?php echo $options['socialdb_fb_api_id']; ?>" />
<input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>" />

<input type="hidden" id="info_messages" name="info_messages" value="<?php echo $_special_configs['info_messages']; ?>" />
<!-- PAGINA DA CATEGORIA -->
<input type="hidden" id="category_page" name="category_page" value="<?php echo $_special_configs['category']; ?>" />
<!-- PAGINA DA PROPRIEDADE -->
<input type="hidden" id="property_page" name="property_page" value="<?php echo $_special_configs['category']; ?>" />
<!-- PAGINA DA TAG -->
<input type="hidden" id="tag_page" name="tag_page" value="<?php echo $_special_configs['tag']; ?>" />
<!-- PAGINA DA TAXONOMIA -->
<input type="hidden" id="tax_page" name="object_page" value="<?php echo $_special_configs['tax']; ?>" />

<?php
if(is_single()) {
        $parent = get_post($post->post_parent);

    if(is_singular('socialdb_object')) { ?>
        <!-- PAGINA DO ITEM -->
        <input type="hidden" id="object_page"      name="object_page"      value="<?php echo $_special_configs['item']; ?>" />
        <input type="hidden" id="single_object_id" name="single_object_id" value="<?php echo $post->ID; ?>" />
        <input type="hidden" id="single_name"      name="item_single_name" value="<?php echo $post->post_name; ?>" />
        <input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($parent->ID) . '?item=' . $post->post_name; ?>" />
        <input type="hidden" class="object_id"     value="<?php echo $post->ID ?>"  />
        <input type="hidden" class="post_id"       name="post_id" value="<?= $post->ID ?>">

    <?php } else if(is_singular('socialdb_collection')) {
        $visualization_page_category = get_post_meta($post->ID, 'socialdb_collection_visualization_page_category', true);
        $collection_default = get_option('disable_empty_collection');
        // $options = get_option('socialdb_theme_options');
        ?>
        <!-- TAINACAN - BEGIN: ITENS NECESSARIOS PARA EXECUCAO DE VARIAS PARTES DO SOCIALDB -->
        <input type="hidden" id="visualization_page_category" name="visualization_page_category"
               value="<?php echo (!$visualization_page_category || $visualization_page_category === 'right_button') ? 'right_button' : 'click'; ?>">
        <input type="hidden" id="socialdb_embed_api_id" name="socialdb_embed_api_id" value="<?php echo $options['socialdb_embed_api_id']; ?>" />
        <input type="hidden" id="current_user_id" name="current_user_id" value="<?php echo get_current_user_id(); ?>" />
        <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $post->ID ?>" />
        <input type="hidden" id="mode" name="mode" value="<?php echo $mode ?>" />
        <input type="hidden" id="site_url" value="<?php echo site_url(); ?>" />
        <input type="hidden" id="collection_root_id" value="<?php echo $root_id; ?>" />
        <input type="hidden" id="socialdb_permalink_collection" name="socialdb_permalink_collection" value="<?php echo get_the_permalink($post->ID); ?>" />
        <input type="hidden" id="slug_collection" name="slug_collection" value="<?php echo $post->post_name; ?>"> <!-- utilizado na busca -->
        <input type="hidden" id="search_collection_field" name="search_collection_field" value="<?php if ($_GET['search']) echo $_GET['search']; ?>" />

    <?php
    }
} else { ?>
    <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $root_id; ?>" />
<?php } ?>