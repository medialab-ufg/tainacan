<?php
$_curr_user_ = ['is_logged' => is_user_logged_in(), 'is_subscriber' =>  current_user_can('subscriber'), 'is_admin' => current_user_can('manage_options') ];
if( $_curr_user_['is_logged'] && $_curr_user_['is_admin'] ) {
    $_current_menu = "menu-ibram";
} else if ( !$_curr_user_['is_logged'] || $_curr_user_['is_subscriber']) {
    $_current_menu = "menu-ibram-visitor";
}

wp_nav_menu( ['theme_location' => $_current_menu, 'container_class' => 'container', 'container' => false,
    'menu_class' => 'navbar navbar-inverse menu-ibram', 'walker' => new wp_bootstrap_navwalker() ] );

$fixed_home_collections = ["Empréstimos", "Conservação", "Restaurações", "Exposição"];
?>
<style>
    .index-menu{
        border: 4px solid #E8E8E8;
        background: white;
        padding: 10px 40px 30px 40px;
        margin: 0 0 40px 0;
        font-size: 13pt;
        text-align: left;
        color:black;
    }
    .index-menu p{
        color:black;
    }
</style>
<div class="col-md-12 ibram-home-container tainacan-config-container">
    <div class="row">
        <div class="col-md-12">
            <h4 class="title">
                <a href="javascript:void(0)">
                    <?php //echo $ibram_collection ?>
                </a>
            </h4>
            <div class="container" >
                <br>
                <div class="index-menu">
                    <p>Bem vindo ao Tainacan+Museu</p>
                    <p>Uma plataforma de inventário, gestão e difusão digital, desenvolvida
                    pelo Instituto Brasileiro de Museus (IBRAM) em parceria com a
                    Universidade Federal Goiás (UFG) para atender às instituições de
                    memória que preservam acervos museológicos.</p>

                    <p>Nessa primeira versão, você poderá realizar:</p>
                    <br>
                    <p>·         Cadastro de bens museológicos permanentes e temporários</p>
                    <p>·         Cadastro de conjuntos</p>
                    <p>·         Cadastro de coleções</p>
                    <p>·         Registrar o descarte e desaparecimento de bens.</p>
                    <br>
                   <p> Bom trabalho!</p>
                   <center>
                   <div style="width: 80%;margin-top: 50px;" class="row">
                       <div class="col-md-2" style="margin-top: 10px;">
                           <img src="<?php echo get_template_directory_uri() . '/libraries/images/ibram/Media-Lab.png' ?>" style="width: 100%" alt="Media Lab UFG" title="Media Lab UFG">
                       </div>
                       <div class="col-md-2" style="margin-top: -10px;">
                           <img src="<?php echo get_template_directory_uri() . '/libraries/images/ibram/Marca_UFG_cor_completa_horizontal.png' ?>" style="width: 100%" alt="Media Lab UFG" title="Media Lab UFG">
                       </div>
                       <div class="col-md-2">
                           <img src="<?php echo get_template_directory_uri() . '/libraries/images/ibram/ibram.gif' ?>" style="width: 100%" alt="IBRAM" title="IBRAM">
                       </div>
                       <div class="col-md-2" style="margin-top: 10px;">
                           <img src="<?php echo get_template_directory_uri() . '/libraries/images/ibram/ministerioeducacao.png' ?>" style="width: 100%" alt="Media Lab UFG" title="Media Lab UFG">
                       </div>
                       <div class="col-md-2" style="margin-top: 15px;">
                           <img src="<?php echo get_template_directory_uri() . '/libraries/images/ibram/minc.png' ?>" style="width: 90%" alt="Media Lab UFG" title="Media Lab UFG">
                       </div>
                       <div class="col-md-2" style="margin-top: 10px;">
                           <img src="<?php echo get_template_directory_uri() . '/libraries/images/ibram/governo-federal-brasil-logo-novo-temer.png' ?>" style="width: 100%" alt="Media Lab UFG" title="Media Lab UFG">
                       </div>
                   </div>
                   </center>    
                </div>
            </div>    
        </div>
        
        <?php //foreach( $fixed_home_collections as $ibram_collection): ?>
            <!--div class="col-md-3">
                <div class="ibram-box">
                    <h4 class="title">
                        <a href="javascript:void(0)">
                            <?php //echo $ibram_collection ?>
                        </a>
                    </h4>
                    <div class="ibram-info-container">
                        <p> <?php echo rand(10, 50); ?> em atraso </p>
                        <p> <?php echo rand(1, 70); ?> para vencer </p>
                        <p> <?php echo rand(60, 150); ?> em andamento </p>
                        <p> <?php echo rand(40, 120); ?> devolvidos esta semana </p>
                    </div>
                </div>

            </div-->
        <?php //endforeach; ?>
    </div>
</div>
