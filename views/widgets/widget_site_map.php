<?php

/**
 * Created by PhpStorm.
 * User: weryques
 * Date: 18/04/17
 * Time: 13:27
 */
class site_map extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'description' =>__( 'Show site map on footer. By MediaLab', 'wpb_widget_domain'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('site_map', __('Template Map', 'wpb_widget_domain'), $widget_ops);
    }

    // Creating widget front-end
    public function widget( $args, $instance ){
        $title = apply_filters('widget_title', $instance['title']);

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ($title)
            echo $args['before_title'] . $title . $args['after_title'];

        echo '<div>';

        // This is where you run the code and display the output

        echo '</div>';

        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        $title = empty($instance['title']) ? __('Mapa do site', 'wpb_widget_domain'): $instance['title'];

        // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"> <?php _e( 'Title:' ); ?> </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>"
                   type="text"
                   value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);

        return $instance;
    }
}
// Class ends here

// Register and load the widget'
function wpb_load_widget_site_map() {
    register_widget( 'site_map' );
}
add_action( 'widgets_init', 'wpb_load_widget_site_map' );

?>