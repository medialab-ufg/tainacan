<?php
class FormItemAttachment extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <?php if (!$this->mediaHabilitate): ?>
            <div class="form-group">
                <h2>
                    <?php echo ($this->terms_fixed['attachments']) ? $this->terms_fixed['attachments']->name : _e('Attachments', 'tainacan') ?>
                    <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>    
                </h2>
                <div >
                    <div id="dropzone_new"
                         class="dropzone"
                         style="margin-bottom: 15px;min-height: 150px;padding-top: 0px;">
                        <div class="dz-message" data-dz-message>
                            <span style="text-align: center;vertical-align: middle;">
                                <h3>
                                    <span class="glyphicon glyphicon-upload"></span>
                                    <b><?php _e('Drop Files', 'tainacan') ?></b>
                                    <?php _e('to upload', 'tainacan') ?>
                                </h3>
                                <h4>(<?php _e('or click', 'tainacan') ?>)</h4>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php
    }

}
