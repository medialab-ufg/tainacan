<?php
function home_collections_widget() {
    register_widget('collections');
}
add_action('widgets_init', 'home_collections_widget');

class Collections extends WP_Widget {
    function __construct() {
        $widget_options = ['description' => __( 'Three latest three Tainacan collections', 'tainacan')];
        parent::__construct("collections", _t("Tainacan Collections"), $widget_options);
    }

    public function widget($args, $instance) {
        $args = ['post_type' => 'socialdb_collection', 'posts_per_page' => 3];
        $collections = new WP_Query($args);
        ?>

        <div class="col-md-6">
            <h2> <?php _t('Collections',1) ?> </h2>
            <div class="col-md-12 home-widget-box no-padding">
                <?php
                if($collections->have_posts()) {
                    $counter = 0;
                    while( $collections->have_posts() ): $collections->the_post();
                        $extra_class = ($counter === 0) ? 'first left' : 'aside right'; ?>
                        <div class="col-md-6 tainacan-new-wrapper no-padding">
                            <?php if( has_post_thumbnail() ):
                                $img = get_item_thumb_image(get_the_ID());
                                echo "<div class='collec-thumb item-" . $counter . " " . $extra_class . "  '> <a href='" . get_the_permalink() ."'> " . $img . "</a> </div>";
                            else:
                                ?>
                                <a href="<?php echo the_permalink(); ?>">
                                    <div class="img-thumbnail placeholder-no-thumb <?php echo $extra_class; ?>"></div>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php
                        $counter++;
                    endwhile;
                    $col_root_id = get_option('collection_root_id');
                    ?>
                    <div class="read-more">
                        <a href="<?php echo get_permalink($col_root_id); ?>">
                            <?php _t('Read more...',1) ?>
                        </a>
                    </div>
                <?php
                } else {
                    echo "<h3>" . _t("No posts yet!") . "</h3>";
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}

?>