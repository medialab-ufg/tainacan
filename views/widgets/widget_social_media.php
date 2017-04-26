<?php

/**
 * Created by PhpStorm.
 * User: weryques
 * Date: 18/04/17
 * Time: 12:31
 */
class social_media extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'description' =>__( 'Show social medias info on footer. By MediaLab', 'wpb_widget_domain'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('social_media', __('Social Medias', 'wpb_widget_domain'), $widget_ops);
    }

    // Creating widget front-end
    public function widget( $args, $instance ){
        $title = apply_filters('widget_title', $instance['title']);
        $facebook_url = empty($instance['facebook_url']) ? __('https://www.facebook.com/tainacan.l3p', 'wpb_widget_domain') : $instance['facebook_url'];
        $youtube_url = empty($instance['youtube_url']) ? __('https://www.youtube.com/channel/UC_G6mfKrktesaBufjA9GU8w', 'wpb_widget_domain') : $instance['youtube_url'];
        $twitter_url = empty($instance['twitter_url']) ? __('https://twitter.com/Tainacan_L3P', 'wpb_widget_domain') : $instance['twitter_url'];
        $googleplus_url = empty($instance['googleplus_url']) ? __('https://plus.google.com/108603352387487216716', 'wpb_widget_domain') : $instance['googleplus_url'];
        $github_url = empty($instance['github_url']) ? __('https://github.com/medialab-ufg/tainacan', 'wpb_widget_domain') : $instance['github_url'];

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if ($title)
            echo $args['before_title'] . $title . $args['after_title'];

        echo '<div>';

        // This is where you run the code and display the output
        echo '<a target="_blank" class="smedialink" href="'. $facebook_url .'"><img class="backicon" src="'. get_template_directory_uri() .'/libraries/images/icon_smedia_widget/face.png" alt="Facebook" /> Facebook</br></a>';
        echo '<a target="_blank" class="smedialink" href="'. $youtube_url .'"><img class="backicon" src="'. get_template_directory_uri() .'/libraries/images/icon_smedia_widget/yt.png" alt="Youtube" /> Youtube</br></a>';
        echo '<a target="_blank" class="smedialink" href="'. $twitter_url .'"><img class="backicon" src="'. get_template_directory_uri() .'/libraries/images/icon_smedia_widget/twit.png" alt="Twitter" /> Twitter</br></a>';
        echo '<a target="_blank" class="smedialink" href="'. $googleplus_url .'"><img class="backicon" src="'. get_template_directory_uri() .'/libraries/images/icon_smedia_widget/gplus.png" alt="Google Plus" /> Google Plus</br></a>';
        echo '<a target="_blank" class="smedialink" href="'. $github_url .'"><img class="backicon" src="'. get_template_directory_uri() .'/libraries/images/icon_smedia_widget/ghub.png" alt="Github"/> Github</a>';

        echo '</div>';
        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        $title = empty($instance['title']) ? __('Redes sociais', 'wpb_widget_domain'): $instance['title'];
        $facebook_url = empty($instance['facebook_url']) ? __('https://www.facebook.com/tainacan.l3p', 'wpb_widget_domain') : $instance['facebook_url'];
        $youtube_url = empty($instance['youtube_url']) ? __('https://www.youtube.com/channel/UC_G6mfKrktesaBufjA9GU8w', 'wpb_widget_domain') : $instance['youtube_url'];
        $twitter_url = empty($instance['twitter_url']) ? __('https://twitter.com/Tainacan_L3P', 'wpb_widget_domain') : $instance['twitter_url'];
        $googleplus_url = empty($instance['googleplus_url']) ? __('https://plus.google.com/108603352387487216716', 'wpb_widget_domain') : $instance['googleplus_url'];
        $github_url = empty($instance['github_url']) ? __('https://github.com/medialab-ufg/tainacan', 'wpb_widget_domain') : $instance['github_url'];

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
            <label for="<?php echo $this->get_field_id('facebook_url');?>"><?php _e('Facebook URL:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('facebook_url');?>"
                   name="<?php echo $this->get_field_name('facebook_url');?>"
                   type="text"
                   value="<?php echo esc_attr($facebook_url);?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('youtube_url');?>"><?php _e('Youtube URL:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('youtube_url');?>"
                   name="<?php echo $this->get_field_name('youtube_url');?>"
                   type="text"
                   value="<?php echo esc_attr($youtube_url);?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twitter_url');?>"><?php _e('Twitter URL:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('twitter_url');?>"
                   name="<?php echo $this->get_field_name('twitter_url');?>"
                   type="text"
                   value="<?php echo esc_attr($twitter_url);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('googleplus_url');?>"><?php _e('Google Plus URL:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('googleplus_url');?>"
                   name="<?php echo $this->get_field_name('googleplus_url');?>"
                   type="text"
                   value="<?php echo esc_attr($googleplus_url);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('github_url');?>"><?php _e('Github URL:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('github_url');?>"
                   name="<?php echo $this->get_field_name('github_url');?>"
                   type="text"
                   value="<?php echo esc_attr($github_url);?>" />
        </p>

        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['facebook_url'] = sanitize_text_field($new_instance['facebook_url']);
        $instance['youtube_url'] = sanitize_text_field($new_instance['youtube_url']);
        $instance['twitter_url'] = sanitize_text_field($new_instance['twitter_url']);
        $instance['googleplus_url'] = sanitize_text_field($new_instance['googleplus_url']);
        $instance['github_url'] = sanitize_text_field($new_instance['github_url']);

        return $instance;
    }
}
// Class ends here

// Register and load the widget'
function wpb_load_widget_social_media() {
    register_widget( 'social_media' );
}
add_action( 'widgets_init', 'wpb_load_widget_social_media' );

?>