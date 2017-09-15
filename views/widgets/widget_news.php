<?php
function home_news_widget() {
    register_widget('HomeNews');
}
add_action('widgets_init', 'home_news_widget');

class HomeNews extends WP_Widget {
    function __construct() {
        $widget_options = ['description' => __( 'Two latest posts', 'tainacan')];
        parent::__construct("home_news", _t("Home News"), $widget_options);
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $news_args = ['post_type' => 'post', 'posts_per_page' => 2];
        $latest_news = new WP_Query($news_args);
        ?>

        <div class="col-md-6">
            <h2> <?php echo wp_trim_words($title, 5, '...'); ?> </h2>
            <div class="col-md-12 home-widget-box no-padding">
                <?php
                if($latest_news->have_posts()) {
                    while( $latest_news->have_posts() ): $latest_news->the_post(); ?>
                        <div class="col-md-12 tainacan-new-wrapper no-padding">
                            <?php get_template_part( 'partials/content/content' ); ?>
                        </div>
                    <?php endwhile;
                } else {
                    echo "<h3>" . _t("No posts yet!") . "</h3>";
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function form($instance) {
        $title = empty($instance['title']) ? _t('News') : $instance['title'];
        ?>
        <p>
            <label for="news"> <?php _e('Title:'); ?> </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        return $instance;
    }
}

?>