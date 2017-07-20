<?php


get_header();

//$collController = new CollectionController();

echo $collController->operation('show_header', [ 
    'collection_id' => get_the_ID() ,
    'mycollections' => false,
    'sharedcollections' => false
]);


the_post();
$cat_root = get_post_meta(get_the_ID(), 'socialdb_collection_object_type', true);

$items = new WP_Query([
    'tax_query' => [
        [
            'taxonomy' => 'socialdb_category_type',
            'terms' => $cat_root
        ]
    ]
]);


if ($items->have_posts()) {
    while ($items->have_posts()) {
        $items->the_post();
        
        the_title();
        echo '<br>';
        
    }
}
?>

<div id="collection_post"></div>

<script>

    $.ajax({
        url: '/tainacan/wp-admin/admin-ajax.php',
        type: 'POST',
        data: {action: 'Collection', operation: 'show_header', collection_id: 12, sharedcollections: 12, mycollections: 12}
    }).done(function (result) {
        $("#collection_post").html(result);
        $('.nav-tabs').tab();
        $('.dropdown-toggle').dropdown();
    });


</script>


<?php

get_footer();
