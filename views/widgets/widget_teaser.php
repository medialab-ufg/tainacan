<?php
class Teaser extends WP_Widget {

    function __construct() {
        $tease_config = [
            'description' => __( 'Write your text with some excerpt text, and a read more link', 'tainacan' ),
            'customize_selective_refresh' => true
        ];
        parent::__construct('teaser', _t('Teaser Content'), $tease_config );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $teaser = apply_filters( 'widget_title', $instance['teaser'] );
        $read_more_link = apply_filters( 'widget_title', $instance['read_more_link'] );

        echo $args['before_widget'];
        ?>
        <div class="col-md-6">
            <h2> <?php echo $title; ?> </h2>
            <div class='home-widget-box col-md-12'>
                <div class="teaser-cnt">
                    <?php echo $teaser; ?>
                </div>

                <div class="read-more">
                    <a href="<?php echo $read_more_link; ?>" rel="noopener" target="_blank">
                        <?php _t('Read more...',1) ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = empty($instance['title']) ? _t('Widget Title') : $instance['title'];
        $teaser = empty($instance['teaser']) ? '' : $instance['teaser'];
        $read_more_link = empty($instance['read_more_link']) ? '' : $instance['read_more_link'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="teaser"> <?php _t('Teaser Text:', '1'); ?> </label>
            <textarea name="<?php echo $this->get_field_name('teaser')?>" class="widefat"
                      id="<?php echo $this->get_field_id('teaser')?>" cols="30" rows="10"><?php echo esc_attr($teaser); ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('read_more_link');?>"><?php _e('Read More URL:', 'tainacan');?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('read_more_link');?>" name="<?php echo $this->get_field_name('read_more_link');?>"
                   type="text"
                   value="<?php echo esc_attr($read_more_link);?>" />
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = [];

        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['teaser'] = ( ! empty( $new_instance['teaser'] ) ) ? strip_tags( $new_instance['teaser'] ) : '';
        $instance['read_more_link'] = ( ! empty( $new_instance['read_more_link'] ) ) ? strip_tags( $new_instance['read_more_link'] ) : '';
        return $instance;
    }
}

function teaser_widget() {
    register_widget( 'teaser' );
}
add_action( 'widgets_init', 'teaser_widget' );