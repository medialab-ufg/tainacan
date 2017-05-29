<?php

//use CollectionModel;
if (isset($_GET['by_function'])) {
    include_once (WORDPRESS_PATH . '/wp-config.php');
    include_once (WORDPRESS_PATH . '/wp-load.php');
    include_once (WORDPRESS_PATH . '/wp-includes/wp-db.php');
} else {
    include_once (dirname(__FILE__) . '/../../../../../wp-config.php');
    include_once (dirname(__FILE__) . '/../../../../../wp-load.php');
    include_once (dirname(__FILE__) . '/../../../../../wp-includes/wp-db.php');
}
require_once(dirname(__FILE__) . '../../general/general_model.php');
include_once (dirname(__FILE__) . '../../collection/collection_model.php');
include_once (dirname(__FILE__) . '../../log/log_model.php');

class UserModel extends Model {

    private $usuario_id;

    public function getUsuario_id() {
        return $this->usuario_id;
    }

    public function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    /**
     * function show_username($data)
     * @param array $data os dados vindo do formulario com o nome do usua\rio e o id da colecao
     * @return mix Retorna uma string com o nome do usuario ja no estado fi nal
     * 
     * @author: Eduardo
     * 
     */
    public function show_username($data) {
        $login = strip_tags(trim($data['username']));
        $login = str_replace(' ', '-', $login);
        $login = str_replace(array('-----', '----', '---', '--'), '-', $login);
        $login = sanitize_user($login, true);
        if (username_exists($login)) {
            return '<br><span style="font-size:small;" class="label label-danger">' . __('Sorry, that username already exists!') . '</span>';
        } elseif (!empty($login)) {
            return '<br><span style="font-size:small;" class="label label-success">' . __('Your valid username will be', 'tainacan') . ': <b>' . $login . '</b></span>';
        } else {
            return '<br><span style="font-size:small;" class="label label-danger">' . __('Please type a username', 'tainacan') . '</span>';
        }
    }

    /**
     * function list_user($data)
     * @param array $data os dados vindo do formulario
     * @return json com os dados do usuario
     * 
     * Autor: Eduardo Humberto 
     */
    public function list_user($data) {
        global $wpdb;
        $wp_user = "wp_users";
        $query = "
                SELECT u.ID AS ID ,u.user_nicename AS user_nicename FROM $wp_user u
                WHERE u.user_nicename LIKE '%{$data['term']}%'
                ORDER BY u.user_nicename";
        $items = $wpdb->get_results($query);

        foreach ($items as $item) {
            $altCompleteL[] = array(
                'value' => $item->ID,
                'label' => $item->user_nicename
            );
        }

        return json_encode($altCompleteL);
    }

    /**
     * function list_user($data)
     * @param int $user_id os dados vindo do formulario
     * @return mix Retorna um array  com o nome e o ID do usuario ou false se não existir
     * 
     * @author: Marcus
     * 
     * @global type $wpdb
     * @param type $data
     * @return \mix */
    public function register_user($data) {
        // global $wpdb;
        $login = strip_tags(trim($data['user_login']));
        $login = str_replace(' ', '-', $login);
        $login = str_replace(array('-----', '----', '---', '--'), '-', $login);
        $userdata = array(
            'user_login' => $login,
            'user_email' => $data['user_email'],
            'user_url' => '',
            'user_pass' => $data['user_pass'],
            //user meta
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'rich_editing' => 'true',
            'comment_shortcuts' => false,
            'show_admin_bar_front' => false,
            'wp_user_level' => 0,
            'wp_capabilities' => 'a:1:{s:10:"subscriber";b:1;}'
        );

        //Verifica se o novo usuario esta sendo cadastrado com funcionario da biblioteca, caso sim então ele é cadastrado como administrador
        if(key_exists('user_type', $data) && strcmp(sanitize_text_field($data['user_type']), 'employee') == 0)
        {
            $userdata['wp_user_level'] = 10; //Admin Level
        }

        $user_id = wp_insert_user($userdata);

        if(isset($data['about_you'])) {
            $about_you = sanitize_text_field($data['about_you']);
            update_user_meta($user_id, 'about_you', $about_you);
        }
        if ( isset($data['current_work']) ) {
            $current_work = sanitize_text_field($data['current_work']);
            update_user_meta($user_id, 'current_work', $current_work);
        }
        if (isset($data['prof_resume'])) {
            $p_resume = sanitize_text_field($data['prof_resume']);
            update_user_meta($user_id, 'prof_resume', $p_resume);
        }

        if(has_action('add_new_user_properties')) //Verifica se é biblioteca
        {
            //Bib User Properties
            $user_properties = [];
            foreach($data as $index => $value)
            {
                $user_properties[$index] = sanitize_text_field($data[$index]);
            }

            if(!key_exists("user_type", $data))
            {
                $user_properties['user_type'] = 'reader';
            }

            if(!key_exists('user_situation', $data))
            {
                $user_properties['user_situation'] = 'active';
            }

            foreach ($user_properties as $index => $value)
            {
                update_user_meta($user_id, $index, $value);
            }
            
        }

        $get_login = get_user_by('id', $user_id);

        //On success
        if (!is_wp_error($user_id)) {
            $resultRegister['id'] = $user_id;
            $resultRegister['result'] = '1';
            $resultRegister['title'] = __('Success', 'tainacan');
            $resultRegister['msg'] = __('User registered successfully! Your login is: ', 'tainacan') . $get_login->user_login;
            $resultRegister['url'] = get_the_permalink(get_option('collection_root_id')) . '?open_login=true';

            /* $user_info = get_userdata($user_id);
            $user_role = implode(', ', $user_info->roles);
            Log::addLog(['user_id' => $user_id, 'event_type' => 'user_profile', 'event' => $user_role]);
            */
            Log::addLog(['user_id' => $user_id, 'event_type' => 'user_status', 'event' => 'register']);
            
            $this->send_welcome_email($data, $get_login->user_login);
        } else {
            $resultRegister['result'] = '0';
            $resultRegister['title'] = __('Error', 'tainacan');
            $resultRegister['msg'] = __('Email already exists or login already exists!', 'tainacan');
            $resultRegister['type'] = 'error';
        }

        return json_encode($resultRegister);
    }

    public function send_welcome_email($data, $user_login) {
        $site_name = (get_option('blogname') == '' ? 'Tainacan' : get_option('blogname'));
        $content = (get_option('socialdb_welcome_email') == '' ? __('Welcome on the Tainacan Repository ', 'tainacan') : get_option('socialdb_welcome_email'));
        $content = str_replace('__USER_NAME__', $data['first_name'] . ' ' . $data['last_name'], $content);
        $content = str_replace('__USER_LOGIN__', $user_login, $content);

        $to = $data['user_email'];
        $subject = __("Welcome - ", 'tainacan') . $site_name;

        add_filter('wp_mail_content_type', 'set_html_content_type');

        $status = wp_mail($to, $subject, $content);

        // Reset content-type to avoid conflicts
        remove_filter('wp_mail_content_type', 'set_html_content_type');
    }

    /**
     * function list_user($data)
     * @param int $user_id os dados vindo do formulario
     * @return mix Retorna um array  com o nome e o ID do usuario ou false se não existir
     * 
     * @author: Marcus
     * 
     * @global type $wpdb
     * @param type $data
     * @return \mix */
    public function get_moderators($data) {
        $json_moderators = array();
        $moderators = get_post_meta($data['collection_id'], 'socialdb_collection_moderator');
        if ($moderators) {
            foreach ($moderators as $moderator) {
                $json_moderators[] = $this->get_user($moderator);
            }
        }
        return $json_moderators;
    }

    public function create_user_gplus($me, $access_token) {
        $result = $this->token_save_gplus($me, $access_token);
        return $this->login_gplus($result['user_login'], $result['user_pass']);
    }

    private function token_save_gplus($user, $access_token) {

        //verifica se o usuï¿½rio jï¿½ tem a devida permisï¿½o para logar.
        if ($user['id']) {
            //verifica ser o usuï¿½rio existe no banco de dados.
            if (email_exists($user['emails'][0]['value']) == false) {

                //constroi os dados para criaï¿½ï¿½o do usuï¿½rio no wordpress. 
                $login = trim(strtolower($user['name']['givenName'])) . '-' . trim(strtolower($user['name']['familyName']));
                $login = str_replace(' ', '-', $login);

                $user_data = array(
                    'user_login' => $login,
                    'user_nicename' => $login,
                    'display_name' => (!empty($user['displayName']) ? $user['displayName'] : $login),
                    'user_email' => $user['emails'][0]['value'],
                    'first_name' => $user['name']['givenName'],
                    'last_name' => $user['name']['familyName'],
                    'user_url' => $user['url'],
                    'user_pass' => $user['id'],
                    'role' => 'subscriber'
                );
                $this->setUsuario_id(wp_insert_user($user_data));
                add_user_meta($this->getUsuario_id(), 'gplus_token', $access_token);
//                    if (get_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']) == "")
//                        add_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']);
                //retornando os dados do usuÃ¡rio;
                return $user_data;
            } else {
                //atualizando os dados do usuÃ¡rio.
                $data_user = get_user_by('email', $user['emails'][0]['value']);

                $login = trim(strtolower($user['name']['givenName'])) . '-' . trim(strtolower($user['name']['familyName']));
                $login = str_replace(' ', '-', $login);

                $user_data = array(
                    'ID' => $data_user->ID,
                    'user_nicename' => $login,
                    'user_login' => $data_user->user_login,
                    'display_name' => (!empty($user['displayName']) ? $user['displayName'] : $login),
                    'user_email' => $user['emails'][0]['value'],
                    'first_name' => $user['name']['givenName'],
                    'last_name' => $user['name']['familyName'],
                    'user_url' => $user['url'],
                    'user_pass' => $user['id']
                );

//                    if (get_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']) == "")
//                        add_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']);
                //jogando os dados novos no banco.
                wp_update_user($user_data);
                update_user_meta($data_user->ID, 'gplus_token', $access_token);

                //retornando os dados do usuï¿½rio;
                return $user_data;
            }
        } else {
            die(header("location:" . site_url()));
        }
    }

    public function fb_register_login($accessToken, $collection_id, array $app) {
        $fbApp = new Facebook\FacebookApp($app['app_id'], $app['app_secret']);
        $request_me = new Facebook\FacebookRequest(
                $fbApp, $accessToken, 'GET', '/me', array(
            'fields' => 'about,birthday,email,first_name,gender,id,last_name,middle_name,name,link'
                )
        );

        $urlBase_me = 'https://graph.facebook.com' . $request_me->getUrl();

        $resposta_me = file_get_contents($urlBase_me);
        $json_me = &json_decode($resposta_me, true);

        if ($json_me) {
            if (email_exists($json_me['email']) == false) {
                //constroi os dados para registro do usuario no wordpress. 
                $login = trim(strtolower($json_me['first_name'])) . '-' . trim(strtolower($json_me['last_name']));
                $login = str_replace(' ', '-', $login);

                $user_data = array(
                    'user_login' => $login,
                    'user_nicename' => $login,
                    'display_name' => (!empty($json_me['name']) ? $json_me['name'] : $login),
                    'user_email' => $json_me['email'],
                    'first_name' => $json_me['first_name'],
                    'last_name' => $json_me['last_name'],
                    'user_url' => $json_me['link'],
                    'user_pass' => $json_me['id'],
                    'role' => 'subscriber'
                );
                $user_id = wp_insert_user($user_data);
                if ($user_id) {
                    add_user_meta($user_id, 'fb_token', $accessToken);
                }
                //return $user_data;
            } else {
                //atualizando os dados do usuario.
                $data_user = get_user_by('email', $json_me['email']);

                $login = trim(strtolower($json_me['first_name'])) . '-' . trim(strtolower($json_me['last_name']));
                $login = str_replace(' ', '-', $login);

                $user_data = array(
                    'ID' => $data_user->ID,
                    'user_nicename' => $login,
                    'user_login' => $data_user->user_login,
                    'display_name' => (!empty($json_me['name']) ? $json_me['name'] : $login),
                    'user_email' => $json_me['email'],
                    'first_name' => $json_me['first_name'],
                    'last_name' => $json_me['last_name'],
                    'user_url' => $json_me['link'],
                    'user_pass' => $json_me['id']
                );
                wp_update_user($user_data);
                update_user_meta($data_user->ID, 'fb_token', $accessToken);

                //retornando os dados do usuï¿½rio;
                //return $user_data;
            }
        } else {
            //return erro
            return false;
        }

        return $this->do_login($user_data['user_login'], $user_data['user_pass']);
    }

    /**
     * Logar usuario no wordpress 
     */
    public function do_login($login = '', $pass = "") {
        $user = wp_signon(array('user_login' => $login, 'user_password' => $pass, 'remember' => true), false);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);

        if (is_wp_error($user))
            return false;
        else {
            return $user;
        }
    }

    public function login_gplus($login = '', $pass = "") {
        $user = wp_signon(array('user_login' => $login, 'user_password' => $pass, 'remember' => true), false);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);

        if (is_wp_error($user))
            return false;
        else {
            return $user;
        }
    }

    public function forgot_password($user_login) {
        if (filter_var($user_login, FILTER_VALIDATE_EMAIL)) {
            $email_user = email_exists($user_login);
            if ($email_user == false) {
                //Email nao existe no banco
                $result['title'] = __('Error', 'tainacan');
                $result['msg'] = __('Your email was not found in our database!', 'tainacan');
                $result['type'] = 'error';
            } else {
                //Email existe no banco
                $new_password = wp_generate_password(12, false);

                //envia o email
                $status = $this->send_reset_password_email(get_user_by('id', $email_user), $new_password);

                if ($status) {
                    wp_set_password($new_password, $email_user);
                    $result['title'] = __('Success', 'tainacan');
                    $result['msg'] = __('Your new password was sent to your email!', 'tainacan');
                    $result['type'] = 'success';
                } else {
                    $result['title'] = __('Error', 'tainacan');
                    $result['msg'] = __('Something went wrong. Error sending email.', 'tainacan');
                    $result['type'] = 'error';
                }
            }
        } else {
            $username_user = username_exists($user_login);
            if ($username_user == null) {
                //Username nao existe no banco
                $result['title'] = __('Error', 'tainacan');
                $result['msg'] = __('Your username was not found in our database!', 'tainacan');
                $result['type'] = 'error';
            } else {
                //Username existe no banco
                $new_password = wp_generate_password(12, false);

                //envia o email
                $status = $this->send_reset_password_email(get_user_by('id', $username_user), $new_password);

                if ($status) {
                    wp_set_password($new_password, $username_user);
                    $result['title'] = __('Success', 'tainacan');
                    $result['msg'] = __('Your new password was sent to your email!', 'tainacan');
                    $result['type'] = 'success';
                } else {
                    $result['title'] = __('Error', 'tainacan');
                    $result['msg'] = __('Something went wrong. Error sending email.', 'tainacan');
                    $result['type'] = 'error';
                }
            }
        }

        return $result;
    }

    public function send_reset_password_email($user, $new_password) {
        $site_name = get_option('blogname');
        $link = get_the_permalink(get_option('collection_root_id')) . "?recovery_password=" . base64_encode($user->data->ID);

        $to = $user->data->user_email;
        $subject = __("You requested a new ", 'tainacan') . $site_name . __(" password", 'tainacan');
        $content = __('Hi', 'tainacan') . " {$user->data->display_name},<br><br>
                    " . __(' You recently asked to reset your ', 'tainacan') . " " . $site_name . " 
                    " . __('password', 'tainacan') . " .<br>
                    <a href='{$link}' target='_blank'>" . __('Click here to change your password', 'tainacan') . ".</a><br><br>

                    " . __('Your new password is:', 'tainacan') . "<br><hr>
                    {$new_password}<hr><br><br>
                    " . __("Didn't request this change?", 'tainacan') . "<br>
                     " . __("If you didn't request a new password, let us know immediately", 'tainacan') . ".<br><br><hr>

                    <small>" . __("This message was sent to", 'tainacan') . " {$user->data->user_email} " . __("at your request", 'tainacan') . ".<br>
                    {$site_name}</small>
                    ";

        add_filter('wp_mail_content_type', 'set_html_content_type');

        $status = wp_mail($to, $subject, $content);

        // Reset content-type to avoid conflicts
        remove_filter('wp_mail_content_type', 'set_html_content_type');

        return $status;
    }

    public function reset_password($data) {
        $old_password = ($data['old_password_reset'] != '' ? $data['old_password_reset'] : $data['old_password']);
        $new_password = ($data['new_password_reset'] != '' ? $data['new_password_reset'] : $data['new_password']);
        $user = get_user_by('id', $data['password_user_id']);
        if ($user && wp_check_password($old_password, $user->data->user_pass, $user->ID)) {
            wp_set_password($new_password, $user->ID);

            $result['title'] = __('Success', 'tainacan');
            $result['msg'] = __('Password changed successfully! Redirecting to login page...', 'tainacan');
            $result['type'] = "success";
        } else {
            $result['title'] = __('Error', 'tainacan');
            $result['msg'] = __('Your old password do not match!', 'tainacan');
            $result['type'] = "error";
        }

        return $result;
    }

    public function send_share_email($data) {
        $site_name = get_option('blogname');
        $link = get_the_permalink($data['collection_id']) . '?item=' . get_post($data['object_id'])->post_name;

        $to = $data['email'];
        $subject = __("An item has been shared with you! - ", 'tainacan') . $site_name;
        $content = __('Hi', 'tainacan') . " ,<br><br>
                    " . __(' Someone shared an item with you in the ', 'tainacan') . " " . $site_name . " 
                    " . __('website.', 'tainacan') . " .<br>
                    <a href='{$link}' target='_blank'>" . __('Click here to see', 'tainacan') . ".</a><br><br><hr>
                    <small>{$site_name}</small>
                    ";

        add_filter('wp_mail_content_type', 'set_html_content_type');

        $status = wp_mail($to, $subject, $content);

        // Reset content-type to avoid conflicts
        remove_filter('wp_mail_content_type', 'set_html_content_type');

        return $status;
    }
    
    /**
     * funcao que busca os autores mais participativos de uma colecao a partir
     * da quantidade de items inseridos
     * @param int $collection_id O id da colecao que esa sendo montado o autocomplete
     * @author: Eduardo Humberto 
     */
    public function search_participatory_authors($collection_id,$search) {
        global $wpdb;
        $category_root_id = $this->get_category_root_of($collection_id);
        $wp_term_relationships = $wpdb->prefix . "term_relationships";
        $wp_posts = $wpdb->prefix . "posts";
        $wp_term_taxonomy = $wpdb->prefix . "term_taxonomy";
        $wp_users = $wpdb->base_prefix . "users";
        $query = "
	SELECT p.post_author, u.display_name, u.user_nicename, count(*) AS num_posts
	FROM $wp_posts p
		INNER JOIN $wp_term_relationships tr ON p.ID = tr.object_id
		INNER JOIN $wp_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
		INNER JOIN $wp_users u ON p.post_author = u.ID
	WHERE tt.term_id = $category_root_id
        AND u.display_name LIKE '%$search%'
	GROUP BY p.post_author
	ORDER BY num_posts DESC
	LIMIT 10";

        $authors = $wpdb->get_results($query);
        return $authors;
    }

}
