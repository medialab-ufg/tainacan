<?php

class Spot extends WP_Widget {

    function __construct() {
        $widget_options = ['description' => 'Home spot to be displayed at home page'];
        parent::__construct("home_spot", "Localização Home", $widget_options);
    }

    public function form( $instance ) {
        $title = empty($instance['title']) ? _t('Spot') : $instance['title'];
        $institution = empty($instance['institution']) ? _t('Institution Name') : $instance['institution'];
        $phone_1 = empty($instance['phone_1']) ? '' : $instance['phone_1'];
        $phone_2 = empty($instance['phone_2']) ? '' : $instance['phone_2'];
        $general_address = empty($instance['general_address']) ? '' : $instance['general_address'];
        $cep = empty($instance['cep']) ? '' : $instance['cep'];
        $city = empty($instance['city']) ? '' : $instance['city'];
        $state = empty($instance['state']) ? '' : $instance['state'];
        $read_more_link = empty($instance['read_more_link']) ? '' : $instance['read_more_link'];
        $top_fields = [
            ['title' => __('Title:'), 'id' => $this->get_field_id('title'), 'name' => $this->get_field_name('title') , 'value' => esc_attr($title)],
            ['title' => __('Institution Name:'), 'id' => $this->get_field_id('institution'), 'name' => $this->get_field_name('institution') , 'value' => esc_attr($institution)],
            ['title' => __('Main Phone:'), 'id' => $this->get_field_id('phone_1'), 'name' => $this->get_field_name('phone_1') , 'value' => esc_attr($phone_1)],
            ['title' => __('Secondary Phone'), 'id' => $this->get_field_id('phone_2'), 'name' => $this->get_field_name('phone_2') , 'value' => esc_attr($phone_2)]
        ];
        $text_fields = [
          ['title' => __('CEP:'), 'id' => $this->get_field_id('cep'), 'name' => $this->get_field_name('cep') , 'value' => esc_attr($cep)],
          ['title' => __('City:'), 'id' => $this->get_field_id('city'), 'name' => $this->get_field_name('city') , 'value' => esc_attr($city)],
          ['title' => __('UF:'), 'id' => $this->get_field_id('state'), 'name' => $this->get_field_name('state') , 'value' => esc_attr($state)],
          ['title' => __('Read More URL:'), 'id' => $this->get_field_id('read_more_link'), 'name' => $this->get_field_name('read_more_link') , 'value' => esc_attr($read_more_link)]
        ];

        foreach ($top_fields as $i => $_field) {
            echo "<p><label for='" . $_field['id'] . "'>" . $_field['title'] . "</label>";
            echo "<input class='widefat' id='". $_field['id'] ."' type='text' name='". $_field['name'] ."' value='". $_field['value'] ."' /></p>";
        }
        ?>
        <p>
            <label for="general_address"> <?php _t('Complete Address Text:', '1'); ?> </label>
            <textarea name="<?php echo $this->get_field_name('general_address')?>" class="widefat"
                      id="<?php echo $this->get_field_id('general_address')?>" cols="30" rows="10"><?php echo esc_attr($general_address); ?></textarea>
        </p>

        <?php
            foreach ($text_fields as $i => $_field) {
                echo "<p><label for='" . $_field['id'] . "'>" . $_field['title'] . "</label>";
                echo "<input class='widefat' id='". $_field['id'] ."' type='text' name='". $_field['name'] ."' value='". $_field['value'] ."' /></p>";
            }
    }

    public function widget($args, $instance) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $institution = apply_filters('widget_title', $instance['institution']);
        $general_address = apply_filters( 'widget_title', $instance['general_address'] );
        $phone_1 = empty($instance['phone_1']) ? '' : $instance['phone_1'];
        $phone_2 = empty($instance['phone_2']) ? '' : $instance['phone_2'];
        $cep = empty($instance['cep']) ? '' : $instance['cep'];
        $city = empty($instance['city']) ? '' : $instance['city'];
        $state = empty($instance['state']) ? '' : $instance['state'];
        $read_more_link = apply_filters('widget_title', $instance['read_more_link']);

        echo $args['before_widget'];
        ?>
        <div class="col-md-6">
            <h2> <?php echo $title; ?> </h2>
            <div class='home-widget-box col-md-12'>
                <div class="teaser-cnt spot-box">
                    <h4> <?php echo $institution; ?> </h4>
                    <?php echo "<p> $phone_1 </p> <p> $phone_2 </p>"; ?> <br>
                    <?php echo "<p> $general_address </p> <p> $cep </p><p> $city - $state </p>"; ?>
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

    public function update($new_instance, $old_instance)
    {
        return parent::update($new_instance, $old_instance);
    }
}

function spot_widget() {
    register_widget('spot');
}
add_action('widgets_init', 'spot_widget');