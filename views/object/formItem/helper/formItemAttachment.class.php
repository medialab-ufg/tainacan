<?php
include_once (dirname(__FILE__) . '/formItem.class.php');

class FormItemAttachment extends FormItem {

    public function widget($property, $item_id,$isFocusMedia = false) {
        ?>
        <?php if (!$this->mediaHabilitate): ?>
            <div class="form-group">
                 <?php echo ($isFocusMedia) ? '<h5>' : '<h2>' ?>
                    <?php echo ($this->terms_fixed['attachments']) ? $this->terms_fixed['attachments']->name : _e('Attachments', 'tainacan') ?>
	            <?php
	            add_helpText($property, $this);
	            ?>
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

                <button type="button" id="edit-captions" class="btn btn-primary btn-xs pull-right" style="margin-bottom: 10px;"><?php _e("Edit attachment captions", "tainacan");?></button>
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
            $("#captions").submit(function (event) {
                let formData = new FormData();
                $(this).find("textarea").each(function (index, element) {
                    formData.append($(element).attr('name'), $(element).val());
                });

                formData.append('operation', 'add_captions');

                event.preventDefault();
                $.ajax({
                    url: $('#src').val() + '/controllers/object/object_controller.php',
                    type: 'POST', data: formData,
                    processData: false, contentType: false
                }).done(function( result ) {
                    $("#att-captions").modal('hide');
                });
            });

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
                    let thisDropzone = this;
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
                    $("#edit-captions").click(function () {
                        list_att(thisDropzone, true);
                    });

                    list_att(thisDropzone);
                    this.on("success", function (file, message) {
                        file.id = message.trim();
                       });
                    },
                url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=<?php echo $item_id ?>',
            });

            function list_att(thisDropzone, openModal = false) {
                $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=<?php echo $item_id ?>', function (data) {
                    try {
                        if(openModal)
                        {
                            $("#captions").html($(".to_copy:first"));
                        }
                        $.each(data, function (key, value) {
                            if (value.name !== undefined && value.name !== 0) {
                                if(openModal === false)
                                {
                                    let mockFile = {name: value.name, size: value.size};
                                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                }else
                                {
                                    /*Add to modal*/
                                    $(".to_copy:first").clone().insertAfter(".to_copy:last");
                                    let last = $(".to_copy:last");
                                    if(value.thumbnail !== false)
                                    {
                                        $(last).find("img").attr("src", value.thumbnail);
                                    }else {
                                        $(last).find("img").hide();
                                        $(last).find(".item_name").text(value.name).show();
                                    }

                                    $(last).find("textarea").attr("name", value.ID);
                                    if(value.caption.length > 0)
                                    {
                                        $(last).find("textarea").val(value.caption);
                                    }

                                    $(last).show();
                                }
                            }
                        });

                        if(openModal)
                        {
                            $("#att-captions").modal('show');
                        }
                    }
                    catch (e) { }
                });
            }
        </script>
        <?php
    }

}
