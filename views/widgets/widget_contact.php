<?php

/**
 * Created by PhpStorm.
 * User: weryques
 * Date: 18/04/17
 * Time: 12:24
 */
class contact extends WP_Widget {
    function __construct() {
        $widget_ops = array(
            'description' =>__( 'Show contacts info on footer. By MediaLab', 'wpb_widget_domain'),
            'customize_selective_refresh' => true,
        );
        parent::__construct('contact', __('Contacts', 'wpb_widget_domain'), $widget_ops);
    }

    // Creating widget front-end
    public function widget( $args, $instance ){
        $title = apply_filters('widget_title', $instance['title']);
        $institution = empty($instance['institution']) ? '' : $instance['institution'];
        $cnpj = empty($instance['cnpj']) ? '' : $instance['cnpj'];
        $street = empty($instance['street']) ? '' : $instance['street'];
        $address_number = empty($instance['address_number']) ? '' : $instance['address_number'];
        $complement = empty($instance['complement']) ? '' : $instance['complement'];
        $cep = empty($instance['cep']) ? '' : $instance['cep'];
        $city = empty($instance['city']) ? '' : $instance['city'];
        $state = empty($instance['state']) ? '' : $instance['state'];
        $country = empty($instance['country']) ? '' : $instance['country'];
        $email = empty($instance['email']) ? '' : $instance['email'];
        $phone = empty($instance['phone']) ? '' : $instance['phone'];
        $is_contact_page = ("page-contato.php" === basename( get_page_template()));

        // before and after widget arguments are defined by themes
        echo $args['before_widget'];

        if($is_contact_page) {
            $social = array_unique( get_option('widget_social_media', true) );
            if($phone)
                echo "<strong>" . _t('Phone') .   "</strong> <br/>" . $phone;

            if($street) {
                echo "<br/><br/><strong>" . _t('Address') .   "</strong> <br/>" . $street . " " . $address_number . " " .$complement;
                echo " " . $city . "<br />" . $state . " " . $cep . " <br />" . $country;
            }

            if($email)
                echo "<br/><strong>" . _t('Email') .   "</strong> <br/>" . $email;

            foreach ($social as $social_media) {
                if(is_array($social_media)) {
                    foreach ($social_media as $key => $mediaURL) {
                        if("facebook_url" === $key && !empty($mediaURL))
                            echo "<br /><br /><strong>" . _t('Facebook') .   "</strong> <br/> <a href='$mediaURL' target='_blank'>" . $mediaURL . "</a>";

                        if("youtube_url" === $key && !empty($mediaURL))
                            echo "<br /><br /><strong>" . _t('YouTube') .   "</strong> <br/> <a href='$mediaURL' target='_blank'>" . $mediaURL . "</a>";

                        if("twitter_url" === $key && !empty($mediaURL))
                            echo "<br /><br /><strong>" . _t('Twitter') .   "</strong> <br/> <a href='$mediaURL' target='_blank'>" . $mediaURL . "</a>";
                    }
                }
            }

        } else {

            if ($title)
                echo $args['before_title'] . $title . $args['after_title'];

            echo '<div>';

            // This is where you run the code and display the output
            echo '<p class="contactContent">';

            if ($institution) {
                echo $institution . '<br/>';
            }
            if ($cnpj) {
                echo 'CNPJ: ' . $cnpj . '<br/><br/>';
            }
            if ($street || $address_number) {
                echo $street . ' ' . $address_number . ',<br/>';
            }
            if ($complement) {
                echo $complement . '.<br/>';
            }
            if ($cep) {
                echo 'CEP: ' . $cep . '<br/>';
            }
            if ($city || $state || $country) {
                echo $city . ' - ' . $state . ' - ' . $country . '.<br/><br/>';
            }
            if ($email) {
                echo $email . '<br/>';
            }
            if ($phone) {
                echo 'Fone: ' . $phone . '<br/>';
            }
            echo '</p>';

            echo '</div>';
        }
        echo $args['after_widget'];
    }

    // Widget Backend
    public function form( $instance ) {
        $title = empty($instance['title']) ? '': $instance['title'];
        $institution = empty($instance['institution']) ? '' : $instance['institution'];
        $cnpj = empty($instance['cnpj']) ? '' : $instance['cnpj'];
        $street = empty($instance['street']) ? '' : $instance['street'];
        $address_number = empty($instance['address_number']) ? '' : $instance['address_number'];
        $complement = empty($instance['complement']) ? '' : $instance['complement'];
        $cep = empty($instance['cep']) ? '' : $instance['cep'];
        $city = empty($instance['city']) ? '' : $instance['city'];
        $state = empty($instance['state']) ? '' : $instance['state'];
        $country = empty($instance['country']) ? '' : $instance['country'];
        $email = empty($instance['email']) ? '' : $instance['email'];
        $phone = empty($instance['phone']) ? '' : $instance['phone'];

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
            <label for="<?php echo $this->get_field_id('institution');?>"><?php _e('Institution:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('institution');?>"
                   name="<?php echo $this->get_field_name('institution');?>"
                   type="text"
                   value="<?php echo esc_attr($institution);?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('cnpj');?>"><?php _e('CNPJ:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('cnpj');?>"
                   name="<?php echo $this->get_field_name('cnpj');?>"
                   type="text"
                   value="<?php echo esc_attr($cnpj);?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('street');?>"><?php _e('Street:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('street');?>"
                   name="<?php echo $this->get_field_name('street');?>"
                   type="text"
                   value="<?php echo esc_attr($street);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('address_number');?>"><?php _e('Address Number:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('address_number');?>"
                   name="<?php echo $this->get_field_name('address_number');?>"
                   type="text"
                   value="<?php echo esc_attr($address_number);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('complement');?>"><?php _e('Complement:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('complement');?>"
                   name="<?php echo $this->get_field_name('complement');?>"
                   type="text"
                   value="<?php echo esc_attr($complement);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('cep');?>"><?php _e('CEP:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('cep');?>"
                   name="<?php echo $this->get_field_name('cep');?>"
                   type="text"
                   value="<?php echo esc_attr($cep);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('city');?>"><?php _e('City:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('city');?>"
                   name="<?php echo $this->get_field_name('city');?>"
                   type="text"
                   value="<?php echo esc_attr($city);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('state');?>"><?php _e('State:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('state');?>"
                   name="<?php echo $this->get_field_name('state');?>"
                   type="text"
                   value="<?php echo esc_attr($state);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('country');?>"><?php _e('Country:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('country');?>"
                   name="<?php echo $this->get_field_name('country');?>"
                   type="text"
                   value="<?php echo esc_attr($country);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('email');?>"><?php _e('Email:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('email');?>"
                   name="<?php echo $this->get_field_name('email');?>"
                   type="email"
                   value="<?php echo esc_attr($email);?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('phone');?>"><?php _e('Phone:');?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id('phone');?>"
                   name="<?php echo $this->get_field_name('phone');?>"
                   type="tel"
                   value="<?php echo esc_attr($phone);?>" />
        </p>

        <?php
    }

    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['institution'] = sanitize_text_field($new_instance['institution']);
        $instance['cnpj'] = sanitize_text_field($new_instance['cnpj']);
        $instance['street'] = sanitize_text_field($new_instance['street']);
        $instance['address_number'] = sanitize_text_field($new_instance['address_number']);
        $instance['complement'] = sanitize_text_field($new_instance['complement']);
        $instance['cep'] = sanitize_text_field($new_instance['cep']);
        $instance['city'] = sanitize_text_field($new_instance['city']);
        $instance['state'] = sanitize_text_field($new_instance['state']);
        $instance['country'] = sanitize_text_field($new_instance['country']);
        $instance['email'] = sanitize_text_field($new_instance['email']);
        $instance['phone'] = sanitize_text_field($new_instance['phone']);

        return $instance;
    }
}
// Class ends here

// Register and load the widget'
function wpb_load_widget_contact() {
    register_widget( 'contact' );
}
add_action( 'widgets_init', 'wpb_load_widget_contact' );

?>
