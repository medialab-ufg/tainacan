<?php

//error_reporting(E_ALL);
//require_once("../../../wp-load.php");
//include_once 'facebook.php';

/**
 *  Facebook class de publicaï¿½ï¿½o do wp-idea. 
 */
class FacebookSocialDB {

    private $appid;
    private $secretId;
    private $token;
    private $link_post;
    private $usuario_id;
    private $mensagem;
    private $picture;
    private $name;
    private $descricao;

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getMensagem() {
        return $this->mensagem;
    }

    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }

    public function getPicture() {
        return $this->picture;
    }

    public function setPicture($picture) {
        $this->picture = $picture;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getAppid() {
        return $this->appid;
    }

    public function setAppid($appid) {
        $this->appid = $appid;
    }

    public function getSecretId() {
        return $this->secretId;
    }

    public function setSecretId($secretId) {
        $this->secretId = $secretId;
    }

    public function getLink_post() {
        return $this->link_post;
    }

    public function setLink_post($link_post) {
        $this->link_post = $link_post;
    }

    public function getUsuario_id() {
        return $this->usuario_id;
    }

    public function setUsuario_id($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    /**
     * posta o conteï¿½do votado no facebook.
     */
    public function posting_facebook() {
        try {
//      http://www.acontecendoaqui.com.br/wp-content/uploads/2013/02/arrumem_foto4.jpg  
            //pegando dados da app na tabela options do wordpress.
            $options = get_option('socialdb_theme_options');
            $this->setAppid($options['socialdb_fb_api_id']);
            $this->setSecretId($options['socialdb_fb_api_secret']);

//    var_dump(implode("/",explode("/", $this->getPicture())));
//   exit();
            //instancia o objeto da api do facebook.
            $facebook = new Facebook(array(
                'appId' => $this->appid,
                'secret' => $this->secretId,
            ));

            $teste = utf8_encode($this->getPicture());

            $facebook->setAccessToken($this->get_token());
            $facebook->api('/me/feed', 'POST', array(
                'description' => str_replace('<br>', "\n", utf8_encode($this->descricao)),
                'message' => utf8_encode(str_replace('<br>', "\n", $this->mensagem)),
                'picture' => $this->getPicture(),
                'name' => $this->getName(),
//             'link'=>  $this->short_url()
                'link' => $this->getLink_post()
                    // 'link'=>  'http://facetlog.com/'.$_GET['endereco']
            ));
        } catch (FacebookApiException $e) {
            error_log($e);
            $this->token_save();
        }
    }

    /*
     * Encurta a url para 
     * fascilitar a visualizaï¿½ï¿½o o link no facebook.
     * integraï¿½ï¿½o com  api Migre.me
     */

    private function short_url() {

        //iniciamos a sessï¿½o com cURL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://is.gd/create.php?format=json&url=" . urlencode($this->link_post));
        //porta de comunicaï¿½ï¿½o
        curl_setopt($curl, CURLOPT_PORT, 80);
        //informamos ao cURL que deve retornar a string com os resultados, sem exibir na tela
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resposta = curl_exec($curl);
        $dados = curl_getinfo($curl);
        //Encerramos a conexï¿½o com cURL
        curl_close($curl);

        if ($dados['http_code'] == 200 && strlen($retorno) < 20) {
            $resposta = json_decode($resposta);
            return $resposta->shorturl;
        }
    }

    public function create_user() {

        $result = array();
        $result = $this->token_save();
        return $this->login($result['user_login'], $result['user_pass']);
    }

    /**
     * Salva a url do usuï¿½rio que ta logado no portal 
     * e logado no facebook apï¿½s  aceitar a app. 
     */
    private function token_save() {

        //pegando dados da app na tabela options do wordpress.
        $options = get_option('socialdb_theme_options');
        $this->setAppid($options['socialdb_fb_api_id']);
        $this->setSecretId($options['socialdb_fb_api_secret']);
//        $this->setAppid('1003980369621510');
//        $this->setSecretId('3c89421b29a2862d3ea8089e84d64147');
        //instancia o objeto da api do facebook.
        $facebook = new Facebook(array(
            'appId' => $this->appid,
            'secret' => $this->secretId,
        ));


        //pega o dados o usuï¿½rio no facebook ID.
        $user = $facebook->getUser();
        //verifica se o usuï¿½rio jï¿½ tem a devida permisï¿½o para logar.
        if ($user) {

            try {

                $user_profile = $facebook->api("/me");

                //verifica ser o usuï¿½rio existe no banco de dados.
                if (email_exists($user_profile['email']) == false) {

                    //constroi os dados para criaï¿½ï¿½o do usuï¿½rio no wordpress. 
                    $login = trim(strtolower($user_profile['first_name'])) . '-' . trim(strtolower($user_profile['last_name']));
                    $login = str_replace(' ', '-', $login);

                    $user_data = array(
                        'user_login' => $login,
                        'user_nicename' => $login,
                        'display_name' => (!empty($user_profile['name']) ? $user_profile['name'] : $login),
                        'user_email' => $user_profile['email'],
                        'first_name' => $user_profile['first_name'],
                        'last_name' => $user_profile['last_name'],
                        'user_url' => $user_profile['link'],
                        'user_pass' => $user,
                        'role' => 'subscriber'
                    );
                    $this->setUsuario_id(wp_insert_user($user_data));
                    add_user_meta($this->getUsuario_id(), 'fb_token', $facebook->getAccessToken());
//                    if (get_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']) == "")
//                        add_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']);
                    //retornando os dados do usuÃ¡rio;
                    return $user_data;
                } else {

                    //atualizando os dados do usuÃ¡rio.
                    $data_user = get_user_by('email', $user_profile['email']);

                    $login = trim(strtolower($user_profile['first_name'])) . '-' . trim(strtolower($user_profile['last_name']));
                    $login = str_replace(' ', '-', $login);

                    $user_data = array(
                        'ID' => $data_user->ID,
                        'user_nicename' => $login,
                        'user_login' => $data_user->user_login,
                        'display_name' => (!empty($user_profile['name']) ? $user_profile['name'] : $login),
                        'user_email' => $user_profile['email'],
                        'first_name' => $user_profile['first_name'],
                        'last_name' => $user_profile['last_name'],
                        'user_url' => $user_profile['link'],
                        'user_pass' => $user
                    );

//                    if (get_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']) == "")
//                        add_user_meta($data_user->ID, 'primary_blog', $_SESSION['id_blog']);
                    //jogando os dados novos no banco.
                    wp_update_user($user_data);
                    update_user_meta($data_user->ID, 'fb_token', $facebook->getAccessToken());

                    //retornando os dados do usuï¿½rio;
                    return $user_data;
                }
            } catch (FacebookApiException $e) {
                $user = null;
                die(header("location:" . $facebook->getLoginUrl(array("scope" => "publish_stream,read_stream,email"))));
            }
        } else {
            die(header("location:" . $facebook->getLoginUrl(array("scope" => "read_stream,email"))));
        }
    }

    /**
     * pega a token do usuï¿½rio salva no banco de dados.
     * para uso com oauth do facebook.
     */
    private function get_token() {
        $dados = get_user_meta(get_current_user_id(), 'fb_token');
        return $dados[0];
    }

    /**
     * Salvar ou atualiza as configuraï¿½ï¿½es da app do facebook.
     *  
     */
    public function option($array = array()) {

        //adiciona a opï¿½ï¿½o da app no banco de dados  
        if (isset($array['action_option']) && $array['action_option'] == "add") {
            add_option($array['option'], $array['value']);
        } else {
            
        }

        //update a opï¿½ï¿½o da app no banco de dados 
        if (isset($array['action_option']) && $array['action_option'] == "update") {
            
        } else {
            
        }
    }

    /**
     * Logar usuï¿½rio no wordpress 
     */
    public function login($login = '', $pass = "") {

        $user = wp_signon(array('user_login' => $login, 'user_password' => $pass, 'remember' => true), false);

        //wp_authenticate($user->user_login, $user->user_password);
        // wp_set_auth_cookie($user->ID,true, false);
        //  do_action('wp_login', $user->user_login, $user);
        //then you should call it this way
        //before get_header() or any html
        // login_after_register($user->user_login, $user->user_password);
        // wp_set_auth_cookie($user->ID);

        wp_clear_auth_cookie();
        wp_set_auth_cookie($user->ID, true);
        do_action('wp_login', $user->user_login, $user);

        if (is_wp_error($user))
            return false;
        else {
            return $user;
        }
    }

}

//$fb=new idea_stream_fb();
////$fb->token_save();
//$fb->setLink_post("http://facetlog.com/is/all-ideas/?galeria=110&categoria[]=462");
//$fb->posting_facebook();
//$result=$fb->token_save();
//$fb->login($result['user_login'], $result['user_pass']);
//configuraï¿½ï¿½o da app
//idea_stream_fb::option(array(
//                            'action_option'=>'add',
//                            'option'=>'fb_appid',
//                            'value'=>'296800753772209')
//                        );
//idea_stream_fb::option(array(
//                            'action_option'=>'add',
//                            'option'=>'fb_secretid',
//                            'value'=>'f372b7fa4952a52d5ad5aa1c3bd1d0d6')
//                        );
?>
