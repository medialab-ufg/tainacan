<?php
include_once ('../../helpers/view_helper.php');
include_once ('../../helpers/user-permission/user_permission_helper.php');
include_once('js/page-js.php');
$helper = new UserPermissionHelper;
?>
<style>
    .header-profile{
        font-size: 1.5em;
        color: #647B92;
        text-indent: 2%;
        padding-top: 15px;
        padding-bottom: 15px;
        margin-right: 0px;
        margin-left: 0px;
        border-right: 1px solid #d3d3d3;
        border-left: 1px solid #d3d3d3;
    }

    .label-profile{
        margin-left: 30px;
        width: 13.6%;
    }
    
    .label-item{
        width: 7%;
    }
    
    .label-category{
        width: 9%;
    }
    
    .label-tag{
        width: 7%;
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
            <form id="form-permission">
                <input type="hidden" name="operation" value="save-permission">
                <div class="header-profile row">
                    <span class="col-md-2 label-profile"><?php echo _e('Profile', 'tainacan') ?></span>
                    <span class="col-md-1 label-item"><?php echo _e('Item', 'tainacan') ?></span>
                    <span class="col-md-1"><?php echo _e('Metadata', 'tainacan') ?></span>
                    <span class="col-md-1 label-category"><?php echo _e('Category', 'tainacan') ?></span>
                    <span class="col-md-1 label-tag"><?php echo _e('Tag', 'tainacan') ?></span>
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
                            <?php $helper->generate_content_permission_view(['id'=>'admin', 'is-fixed'=>true ], true) ;?>
                        </div>   
                    </div>
                    <div id="profile-registered" class="form-group">
                        <h2> 
                            <?php _e('Registered', 'tainacan') ?> 
                        </h2>
                        <div>
                            <?php $helper->generate_content_permission_view(['id'=>'registered', 'is-fixed'=>true ]) ;?>
                        </div>     
                    </div>
                    <div id="profile-anonimous" class="form-group">
                        <h2> 
                            <?php _e('Anonimous', 'tainacan') ?> 
                        </h2>
                        <div>
                            <?php $helper->generate_content_permission_view(['id'=>'anonimous', 'is-fixed'=>true ]) ;?>
                        </div>     
                    </div>
                    <div id="new-profile" class="form-group">
                        <h2>
                            <button class="btn btn-primary"><?php _e('Add new profile', 'tainacan') ?></button>
                        </h2>
                        <div>
                            <input type="text" 
                                   placeholder="<?php _e('Type the name of the new profile','tainacan') ?>"
                                   class="form-control" 
                                   name="name_new_profile">
                            <br>
                            <?php $helper->generate_content_permission_view(['id'=>'anonimous', 'is-fixed'=>true ]) ;?>
                        </div> 
                    </div>
                </div>
                <br>
                <button
                    type="button"
                    onclick="back_main_list()"
                    class="btn btn-default"><?php _e('Cancel','tainacan') ?></button>
                <button 
                    type="submit" 
                    class="btn btn-success pull-right"><?php _e('Save','tainacan') ?></button>
            </form>    
        </div>
    </div>
</div>    
