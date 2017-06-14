<?php
class FormItemThumbnail extends FormItem {

    public function widget($property, $item_id) {
        ?>
        <?php if (!$this->mediaHabilitate): ?>
            <div class="form-group">
                <h2>
                    <?php echo ($this->terms_fixed['thumbnail']) ? $this->terms_fixed['thumbnail']->name : _e('Thumbnail', 'tainacan') ?>
                    <?php do_action('optional_message') ?>
                    <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
                </h2>
                <div  >
                    <input type="hidden" name="thumbnail_url" id="thumbnail_url" value="">
                    <div id="image_side_create_object">
                    </div>
                    <input type="file"
                           id="object_thumbnail"

                           name="object_thumbnail"
                           class="form-control auto-save">
                </div>
            </div>
        <?php endif; ?>
        <?php
    }

}
