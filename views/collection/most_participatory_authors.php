<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/most_participatory_authors_js.php');
?>
<div class="panel panel-default" style="margin-top: 5px;">
    <!--div class="panel-heading">
        <span class="glyphicon glyphicon-user grayleft"></span>&nbsp;<?php _e('Colaboration Ranking', 'tainacan') ?>
    </div-->
    <div class="panel-body">
        <!--div class="autores">
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
        </div --> 

        <div class="row" >
            <div class="col-xs-12">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" 
                           id="search-users-autocomplete" 
                           aria-describedby="inputSuccess4Status">
                    <span class="glyphicon glyphicon-search form-control-feedback" aria-hidden="true"></span>
                </div>
            </div>
        </div>

        <ul class="list-group" id="contact-list" >
            <?php
                foreach ($authors as $autor) {
                    ?>
            <li class="list-group-item" style="padding: 2px;line-height: 1.2em;text-indent: 0px;cursor: pointer;" onclick="wpquery_author(<?php echo $autor->ID; ?>);" >
                            <div class="col-sm-3">
                               <?php echo get_avatar($autor->ID,50); ?>
                            </div>
                            <div class="col-sm-9">
                                <span style="font-size: 8pt;display:block; clear:both"><b><?= $autor->display_name ?></b></span>
                                <span style="font-size: 7pt;display:block; clear:both"><?php _e('Last visited','tainacan') ?>: <?= date('d/m/y') ?></span>
                                <span style="font-size: 8pt;display:block; clear:both"><b><?= $autor->num_posts ?> <?php _e('Colaborations','tainacan') ?></b></span>
                            </div>
                            <div class="clearfix"></div>
                    </li>                                                        
                    <?php
                }
            ?>
        </ul>
    </div>
</div>  