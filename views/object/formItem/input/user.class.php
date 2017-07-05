<?php
class UserClass extends FormItem
{
    public function generate($compound,$property,$item_id,$index_id) {
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        $values = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id]))) ? $this->getValues($this->value[$index_id][$property_id]) : false;
        $username = $userlogin = $useremail = $usercpf = '';
        if($values !== false)
        {
            $user_id = intval($values[0]);
            $user_info = get_user_by("id", $user_id);

            $username = $user_info->display_name;
            $userlogin = $user_info->user_nicename;
            $useremail = $user_info->user_email;

            $usercpf = get_user_meta($user_id, 'CPF')[0];
        }

        ?>
            <!--Look for user-->
            <!--<input type="text" id="selected_user_info_hidden" name="socialdb_property_<?php echo $property['id']; ?>[]" value="" style="display: none;">-->
            <div class="metadata-related col-md-12">
                <div class="col-md-3">
                    <div class="selected_user">
                        <div id="selected-user-info">
                            <?php 
                            if (!empty($username))
                            {
                                $display_no_users = 'none';
                                $display_user = 'block';
                            }else
                            {
                                $display_no_users = 'block';
                                $display_user = 'none';
                            }
                            ?>
                            
                            <p class="text-center text-primary" style="font-size: 15px; padding-top: 30%; display: <?php echo $display_no_users ?>;" id="no_users_msg">
                                <?php _e("No user selected", "tainacan"); ?>
                            </p>
                            <div id="place_to_show_user_info" style="display: <?php echo $display_user ?>;">
                                <div class="label_info">
                                    <label class="label label-default"><?php _e("Name", "tainacan"); ?></label>
                                    <input class="form-control" type="text" readonly id="selected_user_name" value="<?php echo $username ?>"><br>
                                </div>
                                <div class="label_info">
                                    <label class="label label-default"><?php _e("User login", "tainacan"); ?></label>
                                    <input class="form-control" type="text" readonly id="selected_user_login" value="<?php echo $userlogin ?>"><br>
                                </div>
                                <div class="label_info">
                                    <label class="label label-default"><?php _e("E-mail", "tainacan"); ?></label>
                                    <input class="form-control" type="text" readonly id="selected_user_email" value="<?php echo $useremail ?>"></label><br>
                                </div>
                                <div class="label_info">
                                    <label class="label label-default"><?php _e("CPF", "tainacan"); ?></label>
                                    <input class="form-control" type="text" readonly id="selected_user_cpf" value="<?php echo $usercpf ?>"></label><br>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <form id="users_search_<?php echo $property['id'] ?>">
                        <div class="form-group" style="border-bottom: none;">
                            <label><?php _e("User's name"); ?>: </label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="text_box_search" onkeyup="verify_enter(window.event, 'magnifying_glass');" placeholder="<?php _e("Type user's name", "tainacan");?>">

                                                               <span class="input-group-addon" style="cursor: pointer;" id="magnifying_glass" onclick="search_for_users()">
                                                                   <span class="glyphicon glyphicon-search"></span>
                                                               </span>
                            </div>

                            <div id="where_to_show_users" style="margin-top: 13px; display: none;">
                                <label>
                                    <?php _e("Users found")?>
                                </label>
                                <input type="hidden" id="meta_id" value="<?php echo $property['id'] ?>">
                                <div class="col-md-12" id="users_found">
                                    <!-- Onde os usuarios encontrados serão colocados -->
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php
        $this->initScriptsIncrementClass($compound_id,$property_id, $item_id, $index_id);
    }

    /**
     *
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsIncrementClass($compound_id,$property_id, $item_id, $index_id) {
        ?>
        <script>
            function search_for_users() {
                var user_name = $("#text_box_search").val();
                if(user_name.length > 0)
                {
                    var send_url = $('#src').val() + "/modules/tainacan-library/controllers/user_controller.php?operation=search_for_user";
                    $.ajax({
                        type: 'POST',
                        url: send_url,
                        data: {user_name: user_name},
                        success: function (result) {

                            $("#where_to_show_users").show();
                            $("#users_found").html(result);
                        }
                    });
                }
            }
            
            function select_user(obj) {
                //Tratamento CSS
                $(".user_result_selected").removeClass("user_result_selected");
                $(obj).addClass("user_result_selected");

                //Selecionando usuario
                $("#no_users_msg").hide();
                $("#place_to_show_user_info").show();

                //Colocando informações na tela
                $("#selected_user_name").val($(obj).text().trim());
                $("#selected_user_login").val($(obj).attr('data-login').trim());
                $("#selected_user_email").val($(obj).attr('data-email').trim());
                $("#selected_user_cpf").val($(obj).attr('data-cpf').trim());

                //$("#selected_user_info_hidden").val($(obj).attr('data-id').trim());

                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type: 'data',
                        value: $(obj).attr('data-id').trim(),
                        item_id: '<?php echo $item_id ?>',
                        compound_id: '<?php echo $compound_id ?>',
                        property_children_id: '0',
                        index: 0,
                        indexCoumpound: 0
                    }
                }).done(function (result) {

                });
            }
        </script>
        <?php
    }
}