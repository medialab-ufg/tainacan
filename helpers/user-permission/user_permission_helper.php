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
                    <span style="line-height: 150%;"><?php _e('Create/Edit/Delete', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('Sugest', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('Review', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('Download', 'tainacan') ?></span><br>
                    <span style="line-height: 150%;"><?php _e('View', 'tainacan') ?></span>
                </div>
                <div class="col-md-1">
                    <?php $this->generate_entity_checkbox('item', $data, $is_disabled) ?>
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
                    <?php $this->generate_entity_checkbox('view', $data, $is_disabled) ?>
                </div>
        </div> 
        <?php
    }
    
    /**
     * 
     * @param type $param
     */
    public function generate_entity_checkbox($entity,$data,$is_disabled = false) {
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
        <?php
    }
}
