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
        $home = empty($instance['home']) ? __(get_site_url(), 'wpb_widget_domain') : $instance['home'];
        $handbook = empty($instance['handbook']) ? __('https://github.com/l3pufg/tainacan/blob/dev/extras/manual/manual_usuario_tainacan_v1.pdf?raw=true', 'wpb_widget_domain') : $instance['handbook'];

        $option1_title = empty($instance['option1_title']) ? __('', 'wpb_widget_domain') : $instance['option1_title'];
        $option1_url = empty($instance['option1_url']) ? __('', 'wpb_widget_domain') : $instance['option1_url'];
        $option1_new_page = empty($instance['option1_new_page']) ? __('', 'wpb_widget_domain') : $instance['option1_new_page'];

        $option2_title = empty($instance['option2_title']) ? __('', 'wpb_widget_domain') : $instance['option2_title'];
        $option2_url = empty($instance['option2_url']) ? __('', 'wpb_widget_domain') : $instance['option2_url'];
        $option2_new_page = empty($instance['option2_new_page']) ? __('', 'wpb_widget_domain') : $instance['option2_new_page'];

        $option3_title = empty($instance['option3_title']) ? __('', 'wpb_widget_domain') : $instance['option3_title'];
        $option3_url = empty($instance['option3_url']) ? __('', 'wpb_widget_domain') : $instance['option3_url'];
        $option3_new_page = empty($instance['option3_new_page']) ? __('', 'wpb_widget_domain') : $instance['option3_new_page'];

        $option4_title = empty($instance['option4_title']) ? __('', 'wpb_widget_domain') : $instance['option4_title'];
        $option4_url = empty($instance['option4_url']) ? __('', 'wpb_widget_domain') : $instance['option4_url'];
        $option4_new_page = empty($instance['option4_new_page']) ? __('', 'wpb_widget_domain') : $instance['option4_new_page'];

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ($title)
            echo $args['before_title'] . $title . $args['after_title'];

        echo '<div>';

        // This is where you run the code and display the output
        if($home){
            echo '<div class="col-md-12">
                    <div style="padding-left: 0px;" class="col-md-6">
                        <li><a class="" href="'. $home .'">'. __('Inicio') .'</a></li>';
            if(is_user_logged_in()) {
                $local = get_template_directory_uri();
                echo '
                        <li><a class="" href="#" onclick="showProfileScreen(\''. $local .'\');" return false;">' . __('Meu perfil') . '</a></li>
                ';
            }
            if ($option1_title && $option1_url){
                if($option1_new_page) {
                    echo '<li><a class="" target="_blank" href="' . $option1_url . '">' . $option1_title . '</a></li>';
                }
                else{
                    echo '<li><a class="" href="'. $option1_url .'">' . $option1_title . '</a></li>';
                }
            }
            if ($option2_title && $option2_url){
                if($option2_new_page) {
                    echo '<li><a class="" target="_blank" href="' . $option2_url . '">'. $option2_title .'</a></li>';
                }
                else{
                    echo '<li><a class="" href="'. $option2_url .'">'. $option2_title .'</a></li>';
                }
            }
            echo '</div>
                    <div style="padding-left: 0px;" class="col-md-6">
                        <li><a class="" onclick="redirectAdvancedSearch(false)" href="javascript:void(0)">'. __('Coleções e busca').'</a></li>
                        <li><a target="_blank" href="'. $handbook .'">'. __('Manual') .'</a></li>
                    ';
            if ($option3_title && $option3_url){
                if($option3_new_page) {
                    echo '<li><a class="" target="_blank" href="' . $option3_url . '">' . $option3_title . '</a></li>';
                }
                else{
                    echo '<li><a class="" href="'. $option3_url .'">'. $option3_title .'</a></li>';
                }
            }
            if ($option4_title && $option4_url){
                if($option4_new_page) {
                    echo '<li><a class="" target="_blank" href="' . $option4_url . '">' . $option4_title . '</a></li>';
                }
                else{
                    echo '<li><a class="" href="'. $option4_url .'">'. $option4_title .'</a></li>';
                }
            }
            echo '
                    </div>
                  </div>'
            ;
        }

        echo '</div>';

        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        $title = empty($instance['title']) ? __('Mapa do site', 'wpb_widget_domain'): $instance['title'];
        $handbook = empty($instance['handbook']) ? __('https://github.com/l3pufg/tainacan/blob/dev/extras/manual/manual_usuario_tainacan_v1.pdf?raw=true', 'wpb_widget_domain') : $instance['handbook'];
        $option1_title = empty($instance['option1_title']) ? __('', 'wpb_widget_domain') : $instance['option1_title'];
        $option1_url = empty($instance['option1_url']) ? __('', 'wpb_widget_domain') : $instance['option1_url'];
        $option1_new_page = empty($instance['option1_new_page']) ? __('Open in new page.', 'wpb_widget_domain') : $instance['option1_new_page'];

        $option2_title = empty($instance['option2_title']) ? __('', 'wpb_widget_domain') : $instance['option2_title'];
        $option2_url = empty($instance['option2_url']) ? __('', 'wpb_widget_domain') : $instance['option2_url'];
        $option2_new_page = empty($instance['option2_new_page']) ? __('Open in new page.', 'wpb_widget_domain') : $instance['option2_new_page'];

        $option3_title = empty($instance['option3_title']) ? __('', 'wpb_widget_domain') : $instance['option3_title'];
        $option3_url = empty($instance['option3_url']) ? __('', 'wpb_widget_domain') : $instance['option3_url'];
        $option3_new_page = empty($instance['option3_new_page']) ? __('Open in new page.', 'wpb_widget_domain') : $instance['option3_new_page'];

        $option4_title = empty($instance['option4_title']) ? __('', 'wpb_widget_domain') : $instance['option4_title'];
        $option4_url = empty($instance['option4_url']) ? __('', 'wpb_widget_domain') : $instance['option4_url'];
        $option4_new_page = empty($instance['option4_new_page']) ? __('Open in new page.', 'wpb_widget_domain') : $instance['option4_new_page'];

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
        <p>
            <label for="<?php echo $this->get_field_id( 'handbook' ); ?>"> <?php _e( 'Link to handbook:' ); ?> </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'handbook' ); ?>"
                   name="<?php echo $this->get_field_name( 'handbook' ); ?>"
                   type="text"
                   value="<?php echo esc_attr($handbook); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'option1_title' ); ?>"> <?php _e( 'Title:' ); ?> </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option1_title' ); ?>"
                   name="<?php echo $this->get_field_name( 'option1_title' ); ?>"
                   type="text"
                   value="<?php echo esc_attr($option1_title); ?>" />
            <label for="<?php echo $this->get_field_id( 'option1_url' ); ?>"><?php _e('URL:'); ?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option1_url' ); ?>"
                   name="<?php echo $this->get_field_name( 'option1_url' ); ?>"
                   type="url"
                   value="<?php echo esc_attr($option1_url); ?>" />
            <input class="widefat"
                   id="<?php echo $this->get_field_id('option1_new_page'); ?>"
                   name="<?php echo $this->get_field_name( 'option1_new_page');?>"
                   type="checkbox"
                   value="<?php echo esc_attr($option1_new_page)?>" /> <?php _e('Open in new page.')?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'option2_title' ); ?>"> <?php _e( 'Title:' ); ?> </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option2_title' ); ?>"
                   name="<?php echo $this->get_field_name( 'option2_title' ); ?>"
                   type="text"
                   value="<?php echo esc_attr($option2_title); ?>" />
            <label for="<?php echo $this->get_field_id( 'option2_url' ); ?>"><?php _e('URL:'); ?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option2_url' ); ?>"
                   name="<?php echo $this->get_field_name( 'option2_url' ); ?>"
                   type="url"
                   value="<?php echo esc_attr($option2_url); ?>" />
            <input class="widefat"
                   id="<?php echo $this->get_field_id('option2_new_page'); ?>"
                   name="<?php echo $this->get_field_name( 'option2_new_page');?>"
                   type="checkbox"
                   value="<?php echo esc_attr($option2_new_page)?>" /> <?php _e('Open in new page.')?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'option3_title' ); ?>"> <?php _e( 'Title:' ); ?> </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option3_title' ); ?>"
                   name="<?php echo $this->get_field_name( 'option3_title' ); ?>"
                   type="text"
                   value="<?php echo esc_attr($option3_title); ?>" />
            <label for="<?php echo $this->get_field_id( 'option3_url' ); ?>"><?php _e('URL:'); ?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option3_url' ); ?>"
                   name="<?php echo $this->get_field_name( 'option3_url' ); ?>"
                   type="url"
                   value="<?php echo esc_attr($option3_url); ?>" />
            <input class="widefat"
                   id="<?php echo $this->get_field_id('option3_new_page'); ?>"
                   name="<?php echo $this->get_field_name( 'option3_new_page');?>"
                   type="checkbox"
                   value="<?php echo esc_attr($option3_new_page)?>" /> <?php _e('Open in new page.')?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'option4_title' ); ?>"> <?php _e( 'Title:' ); ?> </label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option4_title' ); ?>"
                   name="<?php echo $this->get_field_name( 'option4_title' ); ?>"
                   type="text"
                   value="<?php echo esc_attr($option4_title); ?>" />
            <label for="<?php echo $this->get_field_id( 'option4_url' ); ?>"><?php _e('URL:'); ?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'option4_url' ); ?>"
                   name="<?php echo $this->get_field_name( 'option4_url' ); ?>"
                   type="url"
                   value="<?php echo esc_attr($option4_url); ?>" />
            <input class="widefat"
                   id="<?php echo $this->get_field_id('option4_new_page'); ?>"
                   name="<?php echo $this->get_field_name( 'option4_new_page');?>"
                   type="checkbox"
                   value="<?php echo esc_attr($option4_new_page)?>" /> <?php _e('Open in new page.')?>
        </p>
        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['handbook'] = sanitize_text_field($new_instance['handbook']);

        $instance['option1_title'] = sanitize_text_field($new_instance['option1_title']);
        $instance['option1_url'] = sanitize_text_field($new_instance['option1_url']);
        $instance['option1_new_page'] = sanitize_text_field($new_instance['option1_new_page']);

        $instance['option2_title'] = sanitize_text_field($new_instance['option2_title']);
        $instance['option2_url'] = sanitize_text_field($new_instance['option2_url']);
        $instance['option2_new_page'] = sanitize_text_field($new_instance['option2_new_page']);

        $instance['option3_title'] = sanitize_text_field($new_instance['option3_title']);
        $instance['option3_url'] = sanitize_text_field($new_instance['option3_url']);
        $instance['option3_new_page'] = sanitize_text_field($new_instance['option3_new_page']);

        $instance['option4_title'] = sanitize_text_field($new_instance['option4_title']);
        $instance['option4_url'] = sanitize_text_field($new_instance['option4_url']);
        $instance['option4_new_page'] = sanitize_text_field($new_instance['option4_new_page']);

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