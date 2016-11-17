<?php
/*
 * Object Controller's view helper 
 * */

class UserPermissionHelper extends ViewHelper {
    
    /**
     * 
     * @param type $data
     * @param type $is_disabled
     */
    public function generate_content_permission_view($data,$is_disabled = false) {
        ?>
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-1">
                &nbsp;
                <?php echo ViewHelper::render_icon('person','png'); ?>&nbsp;
                <?php echo ViewHelper::render_icon('people','png'); ?>
            </div>
            <div class="col-md-1">
                &nbsp;
                <?php echo ViewHelper::render_icon('person','png'); ?>&nbsp;
                <?php echo ViewHelper::render_icon('people','png'); ?>
            </div>
            <div class="col-md-1">
                &nbsp;
                <?php echo ViewHelper::render_icon('person','png'); ?>&nbsp;
                <?php echo ViewHelper::render_icon('people','png'); ?>
            </div>
            <div class="col-md-1">
                &nbsp;
                <?php echo ViewHelper::render_icon('person','png'); ?>&nbsp;
                <?php echo ViewHelper::render_icon('people','png'); ?>
            </div>
            <div class="col-md-1">
               &nbsp;
                <?php echo ViewHelper::render_icon('person','png'); ?>&nbsp;
                <?php echo ViewHelper::render_icon('people','png'); ?>
            </div>
            <div class="col-md-1">
               &nbsp;
                <?php echo ViewHelper::render_icon('person','png'); ?>&nbsp;
                <?php echo ViewHelper::render_icon('people','png'); ?>
            </div>
        </div>  
        <br>
        <div class="row">
                <div class="col-md-2">
                    <span style="line-height: 150%;"><?php _e('Create/Edit/Delete', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('Sugest', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('Review', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('Download', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('View', 'tainacan') ?></span>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('item', $data, $is_disabled,true) ?>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('metadata', $data, $is_disabled) ?>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('category', $data, $is_disabled) ?>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('tag', $data, $is_disabled) ?>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('comment', $data, $is_disabled) ?>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('descritor', $data, $is_disabled) ?>
                </div>
                <div class="col-md-4" style="background-color: #DAEEEE;padding: 15px" >
                    <div class="col-md-6">
                        <?php $this->generate_menu_checkbox('configuration', 'Configuration', $data) ?>
                        <?php $this->generate_menu_checkbox('metadata', 'Metadata', $data) ?>
                        <?php $this->generate_menu_checkbox('layout', 'Layout', $data) ?>
                        <?php $this->generate_menu_checkbox('import', 'Import', $data) ?>
                        <?php $this->generate_menu_checkbox('social', 'Social', $data) ?>
                        <?php $this->generate_menu_checkbox('tag', 'Tags', $data) ?>
                    </div>
                    <div class="col-md-6">
                        <?php $this->generate_menu_checkbox('license', 'License', $data) ?>
                        <?php $this->generate_menu_checkbox('statistic', 'Statistics', $data) ?>
                        <?php $this->generate_menu_checkbox('permission', 'Permission', $data) ?>
                        <?php $this->generate_menu_checkbox('users', 'Users', $data) ?>
                        <?php $this->generate_menu_checkbox('export', 'Export', $data) ?>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <input type="text" 
                               name="profile_score_<?php echo $data['id'] ?>" class="col-md-6 input-medium">&nbsp;<?php _e('Score','tainacan') ?>
                    </div>
                </div>
        </div> 
        <?php
    }
    
    /**
     * 
     * @param type $entity
     * @param type $name
     * @param type $profile_id
     */
    public function generate_menu_checkbox($entity,$name,$data) {
        ?>
        <input  type="checkbox" 
                name="allow_view_<?php echo $entity ?>_<?php echo $data['id']  ?>"
                <?php if(isset($data['allow_view_'.$entity.'_'.$data['id'] ])&&
                               $data['allow_view_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                <?php echo ($data['id']=='admin') ? 'disabled="disabled"' : '' ?>
                value="yes">&nbsp;<?php _e($name,'tainacan') ?><br>
        <?php
    }
    
    /**
     * 
     * @param type $param
     */
    public function generate_entity_checkbox($entity,$data,$is_disabled = false,$all_checkbox = false) {
        ?>
        <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_crud_user_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_crud_user_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_crud_user_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
            </span> 
            <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_crud_other_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_crud_other_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_crud_other_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
                <br>
            </span>
            <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_suggest_user_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_suggest_user_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_suggest_user_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
            </span> 
            <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_suggest_other_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_suggest_other_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_suggest_other_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
                <br>
            </span>     
            <span class="icons-margin">
                &nbsp;&nbsp;&nbsp;
            </span> 
            <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_review_other_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_review_other_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_review_other_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
                <br>
            </span>
            <?php if($all_checkbox): ?>
            <span class="icons-margin">
                &nbsp;&nbsp;&nbsp;
            </span> 
            <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_download_other_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_download_other_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_download_other_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
                <br>
            </span>     
            <span class="icons-margin">
                &nbsp;&nbsp;&nbsp;
            </span> 
            <span class="icons-margin">
                <input type="checkbox" 
                       value="yes" 
                       name="socialdb_permission_view_other_<?php echo $entity ?>_<?php echo $data['id'] ?>" 
                       <?php if(isset($data['socialdb_permission_view_other_'.$entity.'_'.$data['id'] ])&&
                               $data['socialdb_permission_view_other_'.$entity.'_'.$data['id'] ]=='yes') echo 'checked="checked"'; ?>
                       <?php echo ($data['id']=='admin') ? 'checked="checked"' : ''; ?>
                       <?php echo ($is_disabled) ? 'disabled="disabled"' : '' ?>
                       />
                <br>
            </span> 
            <?php endif; ?>
        <?php
    }
}
