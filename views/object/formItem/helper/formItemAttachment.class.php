<?php
include_once (dirname(__FILE__) . '/formItem.class.php');

class FormItemAttachment extends FormItem {

    public function widget($property, $item_id,$isFocusMedia = false) {
        ?>
        <?php if (!$this->mediaHabilitate): ?>
            <div class="form-group">
                 <?php echo ($isFocusMedia) ? '<h5>' : '<h2>' ?>
                    <?php echo ($this->terms_fixed['attachments']) ? $this->terms_fixed['attachments']->name : _e('Attachments', 'tainacan') ?>
                    <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
                <?php echo ($isFocusMedia) ? '</h5>' : '</h2>' ?>
                <div >
                    <div id="dropzone_form"
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
         <?php
        $this->initScriptsAttachmentContainer($property, $item_id); ?>    
        <?php endif;
    }


    /**
     *
     * @param int $property
     * @param int $item_id
     */
    public function initScriptsAttachmentContainer($property, $item_id) { ?>
        <script>
            Dropzone.autoDiscover = false;
            var myDropzone = new Dropzone("#dropzone_form", {
                addRemoveLinks: true,
                accept: function(file, done) {
                    if (file.type === ".exe") {
                        done("Error! Files of this type are not accepted");
                    } else {
                        done();
                        //set_attachments_valid(myDropzone.getAcceptedFiles().length);
                    }
                },
                init: function () {
                    thisDropzone = this;
                    this.on("removedfile", function (file) {
                        //    if (!file.serverId) { return; } // The file hasn't been uploaded
                        $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=<?php echo $item_id ?>&file_name=' + file.name, function (data) {
                            //set_attachments_valid(thisDropzone.getAcceptedFiles().length);
                            if (data.trim() === 'false') {
                                showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!", 'tainacan') ?>', 'error');
                            } else {
                                showAlertGeneral('<?php _e("Success", 'tainacan') ?>', '<?php _e("File removed!", 'tainacan') ?>', 'success');
                            }
                        }); // Send the file id along
                    });
                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=<?php echo $item_id ?>', function (data) {
                        try {
                            //var jsonObject = JSON.parse(data);
                            $.each(data, function (key, value) {
                                if (value.name !== undefined && value.name !== 0) {
                                    var mockFile = {name: value.name, size: value.size};
                                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                }
                            });
                            //set_attachments_valid(thisDropzone.getAcceptedFiles().length);
                        }
                        catch (e) { }
                    });
                    this.on("success", function (file, message) {
                        file.id = message.trim();
                       });
                    },
                url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=<?php echo $item_id ?>',
            });
        </script>
        <?php
    }

}
