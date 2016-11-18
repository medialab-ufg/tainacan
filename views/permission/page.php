<?php
include_once ('../../helpers/view_helper.php');
include_once('js/page-js.php');
?>
<style>
    .header-profile{
        font-size: 1.5em;
        color: #647B92;
        text-indent: 2%;
    }

    .label-profile{
        margin-left: 30px;
    }

    #list-profiles h2.ui-accordion-header {
        border: 0px solid #d3d3d3;
        text-align: left;
        font-size: 12px;
        text-indent: 2%;
        font-weight: bold;
        background: white;
        width: 100%;
        border-radius: 2px;
        padding: 15px 0 15px 0;
        margin: 0px 0px 0 0 !important;
    }

    #list-profiles{
        width: 100%;
    }

    #list-profiles .form-group{
        margin: 0px;
        padding-left: 15px;
        border: 1px solid #d3d3d3;
    }

    .icons-margin{
        margin-left:7px;
        margin-right:7px;
    }

   
</style>    
<div class="col-md-12" 
     style="background: white;border: 3px solid #E8E8E8;font: 11px Arial;margin-left: 15px;margin-right: 15px;width: 98%;padding-top: 15px;">
    <h4>
        <?php _e('User permission', 'tainacan') ?>
        <button type="button" onclick="back_main_list();"class="btn btn-default pull-right">
            <b><?php _e('Back', 'tainacan') ?></b>
        </button>
    </h4>
    <hr>
    <!-- Abas para a Listagem dos metadados -->
    <div style="background: white;">
        <ul class="nav nav-tabs" style="background: white;font-size: 1.5em;">
            <li  role="presentation" class="active">
                <a id="click-tab-profile" href="#tab_profile" aria-controls="tab_profile" role="tab" data-toggle="tab">
                    <span id="tab-profile"><?php echo _e('Profile', 'tainacan') ?></span>
                </a>
            </li>
            <li  role="presentation">
                <a id="click-tab-colaboration" href="#tab_colaboration" aria-controls="tab_colaboration" role="tab" data-toggle="tab">
                    <span id="tab-colaboration"><?php echo _e('Colaboration', 'tainacan') ?></span>
                </a>
            </li>
            <li  role="presentation">
                <a id="click-tab-user" href="#tab_user" aria-controls="tab_user" role="tab" data-toggle="tab">
                    <span id="tab-user"><?php echo _e('User', 'tainacan') ?></span>
                </a>
            </li>
        </ul>
    </div>    
    <div id="tab-content" class="tab-content" style="background: white">
        <div id="tab_profile">
            <div class="header-profile row">
                <span class="col-md-2 label-profile"><?php echo _e('Profile', 'tainacan') ?></span>
                <span class="col-md-1"><?php echo _e('Item', 'tainacan') ?></span>
                <span class="col-md-1"><?php echo _e('Metadata', 'tainacan') ?></span>
                <span class="col-md-1"><?php echo _e('Category', 'tainacan') ?></span>
                <span class="col-md-1"><?php echo _e('Tag', 'tainacan') ?></span>
                <span class="col-md-1"><?php echo _e('Comment', 'tainacan') ?></span>
                <span class="col-md-1"><?php echo _e('Descritor', 'tainacan') ?></span>
                <span class="col-md-3"><center><?php echo _e('Manage', 'tainacan') ?></center></span>
            </div>
            <div id="list-profiles" class="multiple-items-accordion">
                <div id="profile-administrator" class="form-group">
                    <h2> 
                        <?php _e('Administrator', 'tainacan') ?> 
                    </h2>
                    <div>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-1">
                                <span  class="icons-margin glyphicon glyphicon-user"></span>
                                <span  class="icons-margin glyphicon glyphicon-user"></span>
                            </div>
                            <div class="col-md-1">
                                <span class="icons-margin glyphicon glyphicon-user"></span>
                                <span class="icons-margin glyphicon glyphicon-user"></span>
                            </div>
                            <div class="col-md-1">
                                <span class="icons-margin glyphicon glyphicon-user"></span>
                                <span class="icons-margin glyphicon glyphicon-user"></span>
                            </div>
                            <div class="col-md-1">
                                <span class="icons-margin glyphicon glyphicon-user"></span>
                                <span class="icons-margin glyphicon glyphicon-user"></span>
                            </div>
                        </div>  
                        <br>
                        <div class="row">
                            <div class="col-md-2">
                                <span><?php _e('Create/Edit/Delete', 'tainacan') ?></span><br>
                                <span><?php _e('Sugest', 'tainacan') ?></span><br>
                                <span><?php _e('Review', 'tainacan') ?></span><br>
                                <span><?php _e('Download', 'tainacan') ?></span><br>
                                <span><?php _e('See', 'tainacan') ?></span>
                            </div>
                            <div class="col-md-1">
                                <span class="icons-margin">
                                    <input type="checkbox" value="None" name="check" />
                                </span> 
                                <span class="icons-margin">
                                    <input type="checkbox" value="None" class="icons-margin roundedOne" name="check" /><br>
                                </span>
                                <span class="icons-margin">
                                    <input type="checkbox" value="None" name="check" />
                                </span> 
                                <span class="icons-margin">
                                    <input type="checkbox" value="None" class="icons-margin roundedOne" name="check" /><br>
                                </span>     
                            </div>
                            <div class="col-md-1">
                            </div>
                            <div class="col-md-1">
                            </div>
                            <div class="col-md-1">
                            </div>
                        </div> 
                    </div>   
                </div>
                <div id="profile-registered" class="form-group">
                    <h2> 
                        <?php _e('Registered', 'tainacan') ?> 
                    </h2>
                    <div>

                    </div>     
                </div>
                <div id="profile-anonimous" class="form-group">
                    <h2> 
                        <?php _e('Anonimous', 'tainacan') ?> 
                    </h2>
                    <div>

                    </div>     
                </div>
            </div>
        </div>
    </div>
</div>    
