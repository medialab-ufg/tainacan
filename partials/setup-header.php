<?php
$options = get_option('socialdb_theme_options');
$collection_default = get_option('disable_empty_collection');
$_special_configs = [
    'info_messages' => ( (isset($_GET['info_messages'])) ? trim($_GET['info_messages']) : ''),
    'item' => ( (isset($_GET['item'])) ? trim($_GET['item']) : ''),
    'category' => ( (isset($_GET['category'])) ? trim($_GET['category']) : ''),
    'property' => ( (isset($_GET['property'])) ? trim($_GET['property']) : ''),
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
    <input type="hidden" id="site_url" value="<?php echo site_url(); ?>" />

    <input type="hidden" id="info_messages" name="info_messages" value="<?php echo $_special_configs['info_messages']; ?>" />
    <!-- PAGINA DA CATEGORIA -->
    <input type="hidden" id="category_page" name="category_page" value="<?php echo $_special_configs['category']; ?>" />
    <!-- PAGINA DA PROPRIEDADE -->
    <input type="hidden" id="property_page" name="property_page" value="<?php echo $_special_configs['property']; ?>" />
    <!-- PAGINA DA TAG -->
    <input type="hidden" id="tag_page" name="tag_page" value="<?php echo $_special_configs['tag']; ?>" />
    <!-- PAGINA DA TAXONOMIA -->
    <input type="hidden" id="tax_page" name="tax_page" value="<?php echo $_special_configs['tax']; ?>" />

    <input type="hidden" id="search-advanced-text" value="<?php echo (isset($_GET['search-advanced-text']) && !empty($_GET['search-advanced-text'])) ? $_GET['search-advanced-text'] : '' ?>" />

<?php
if(is_single()) {
    $parent = get_post($post->post_parent);
    ?>
    <input type="hidden" id="alert_attention" value="<?php _t('Attention', 1); ?>" />
    <input type="hidden" id="alert_removed_item" value="<?php _t('This item has been removed, redirecting to collection home page! ', 1); ?>" />
    <input type="hidden" id="alert_empty_comment" value="<?php _t('Fill your comment', 1); ?>" />
    <input type="hidden" id="alert_error" value="<?php _t('Error', 1); ?>" />
    <input type="hidden" id="current_user_id" name="current_user_id" value="<?php echo get_current_user_id(); ?>" />
    <?php

    if(is_singular('socialdb_object')) { ?>
        <!-- PAGINA DO ITEM -->
        <input type="hidden" id="object_page"      name="object_page"      value="<?php echo $_special_configs['item']; ?>" />
        <input type="hidden" id="single_object_id" name="single_object_id" value="<?php echo $post->ID; ?>" />
        <input type="hidden" id="single_name"      name="item_single_name" value="<?php echo $post->post_name; ?>" />
        <input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($parent->ID) . '?item=' . $post->post_name; ?>" />
        <input type="hidden" class="object_id"     value="<?php echo $post->ID ?>"  />
        <input type="hidden" class="post_id"       name="post_id" value="<?= $post->ID ?>">
        <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $parent->ID ?>" />

    <?php } else if(is_singular('socialdb_collection')) {
        $visualization_page_category = get_post_meta($post->ID, 'socialdb_collection_visualization_page_category', true);
        $collection_default = get_option('disable_empty_collection');
        $collection_params = [
            "recovery_password" => getPageParam('recovery_password'),
            "is_filter" => getPageParam('is_filter'),
            "info_title" => getPageParam('info_title'),
            "mycollections" => getPageParam('mycollections', true),
            "sharedcollections" => getPageParam('sharedcollections', true),
            "open_wizard" => getPageParam('open_wizard'),
            "open_login" => getPageParam('open_login'),
            "open_edit_item" => getPageParam('open_edit_item'),
        ];
        ?>
        <!-- TAINACAN - BEGIN: ITENS NECESSARIOS PARA EXECUCAO DE VARIAS PARTES DO SOCIALDB -->
        <input type="hidden" id="visualization_page_category" name="visualization_page_category"
               value="<?php echo (!$visualization_page_category || $visualization_page_category === 'right_button') ? 'right_button' : 'click'; ?>">
        <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $post->ID ?>" />
        <input type="hidden" id="collection_root_id" value="<?php echo $root_id; ?>" />
        <input type="hidden" id="socialdb_permalink_collection" name="socialdb_permalink_collection" value="<?php echo get_the_permalink($post->ID); ?>" />
        <input type="hidden" id="slug_collection" name="slug_collection" value="<?php echo $post->post_name; ?>"> <!-- utilizado na busca -->
        <input type="hidden" id="search_collection_field" name="search_collection_field" value="<?php if ( isset($_GET['search'])) echo $_GET['search']; ?>" />
        <!-- Se devera abrir o formulario de adicao item -->
        <input type="hidden" id="open_create_item_text" name="open_create_item_text"
               value="<?php if (isset($_GET['create-item'])) { echo $_GET['create-item']; } ?>">
        <input type="hidden" id="object_page" name="object_page"
               value="<?php if (get_query_var('item') && !get_query_var('edit-item')) { echo trim(get_query_var('item')); } ?>">

        <input type="hidden" id="wp_query_args" name="wp_query_args" value=""> <!-- utilizado na busca -->
        <input type="hidden" id="change_collection_images" name="change_collection_images" value="">
        <input type="hidden" id="value_search" name="value_search" value=""> <!-- utilizado na busca -->
        <input type="hidden" id="flag_dynatree_ajax" name="flag_dynatree_ajax" value="true"> <!-- utilizado na busca -->
        <input type="hidden" id="global_tag_id" name="global_tag_id" value="<?php echo (get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id) ? get_term_by('slug', 'socialdb_property_fixed_tags', 'socialdb_property_type')->term_id : 'tag' ?>"> <!-- utilizado na busca -->

        <?php if( isset($_SESSION['instagramInsertedIds']) ): ?>
            <input type="hidden" id="instagramInsertedIds" name="instagramInsertedIds" value="<?php echo socialMediaResponse($_SESSION['instagramInsertedIds'], "instagram"); ?>">
        <?php elseif ( isset($_SESSION['facebookInsertedIds']) ): ?>
            <input type="hidden" id="facebookInsertedIds" name="facebookInsertedIds" value="<?php echo socialMediaResponse($_SESSION['facebookInsertedIds'], "facebook"); ?>">
        <?php endif; ?>

        <?php foreach ($collection_params as $k => $param): ?>
            <input type="hidden" id="<?php echo $k?>" name="<?php echo $k?>" value="<?php echo $param; ?>"/>
        <?php
        endforeach;
    } // is collection's page
} else { ?>
    <input type="hidden" id="collection_id" name="collection_id" value="<?php echo $root_id; ?>" />
<?php } ?>