<?php
get_header();

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


get_footer();
