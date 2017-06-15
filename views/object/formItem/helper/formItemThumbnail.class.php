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
                    <div id="image_side_create_object">
                      <img class="thumbnail" src="<?php echo get_the_post_thumbnail_url($item_id) ?>">
                    </div>
                    <form></form>
                    <form id="formUpdateThumbnail">
                        <input type="file"
                               id="object_thumbnail"
                               name="object_thumbnail"
                               class="form-control auto-save">
                        <input type="hidden" name="operation" value="saveThumbnail">
                        <input type="hidden" name="item_id" value="<?php echo $item_id ?>">
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <?php
        $this->initScriptsThumbnailContainer($property, $item_id);
    }

    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsThumbnailContainer($property, $item_id) {
        ?>
        <script>
        $('#formUpdateThumbnail').submit(function (e) {
            e.preventDefault();
            $.ajax( {
              url: $('#src').val() + '/controllers/object/form_item_controller.php',
              type: 'POST',
              data: new FormData( this ),
              processData: false,
              contentType: false
            } );
        });

        $('#object_thumbnail').change(function () {
            var input = $(this);
            var target = $('#image_side_create_object img');
            var fileDefault = target.attr('default');

            if (!input.val()) {
                target.fadeOut('fast', function () {
                    $(this).attr('src', fileDefault).fadeIn('slow');
                });
                return false;
            }

            if (this.files && (this.files[0].type.match("image/jpeg") || this.files[0].type.match("image/png"))) {
                //TriggerClose();
                var reader = new FileReader();
                reader.onload = function (e) {
                    target.fadeOut('fast', function () {
                        $(this).attr('src', e.target.result).fadeIn('fast');
                    });
                };
                reader.readAsDataURL(this.files[0]);
                console.log($('#formUpdateThumbnail'));
                $('#formUpdateThumbnail').trigger('submit');
            } else {
                showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File is not compatible!", 'tainacan') ?>', 'error');
                target.fadeOut('fast', function () {
                    $(this).attr('src', fileDefault).fadeIn('slow');
                });
                input.val('');
                return false;
            }
        });
        </script>
        <?php
    }

}
