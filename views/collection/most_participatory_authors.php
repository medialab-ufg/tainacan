<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');

?>
<div class="panel panel-default" style="margin-top: 5px;">
    <!--div class="panel-heading">
        <span class="glyphicon glyphicon-user grayleft"></span>&nbsp;<?php _e('Colaboration Ranking','tainacan') ?>
    </div-->
    <div class="panel-body">
        <div class="autores">
           <?php
            foreach ($authors as $autor) {
                ?>
                <div class="row">
                            <div class="col-md-8"><?= $autor->display_name ?></div>
                    <div class="col-md-2 autores_qtde_posts"><?= $autor->num_posts ?></div>
                </div>                                                           
                <?php
            }
            ?>
        </div> 

    </div>
</div>    