<?php
require_once(dirname(__FILE__) . '../../../../controllers/general/general_controller.php');


class userController extends Controller
{
    public function operation ($operation)
    {
        switch($operation)
        {
            case 'get_user':
                $user_id = $_POST['user_id'];
                $user_info = get_user_by('id', $user_id);
                $user_meta = get_user_meta($user_id);
                ?>
                <form id="editUser" name="editUser" type="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="first_name"><?php _e('First Name', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="text" value="<?php echo $user_meta['first_name'][0] ?>" required="required" class="form-control" name="first_name" id="first_name" placeholder="<?php _e('Type here your first name', 'tainacan'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name"><?php _e('Last Name', 'tainacan'); ?><!--span style="color: #EE0000;"> *</span--></label>
                        <input type="text" value="<?php echo $user_meta['last_name'][0]; ?>" class="form-control" name="last_name" id="last_name" placeholder="<?php _e('Type here your last name', 'tainacan'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_email"><?php _e('Email', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <input type="email" value="<?php echo explode(" ", $user_info->data->user_email)[0]; ?>" required="required" class="form-control" name="user_email" id="user_email" placeholder="<?php _e('Type here your e-mail', 'tainacan'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_login"><?php _e('Username', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <p class="help-block"><?php _e('Help: Limit of 25 characters', 'tainacan'); ?></p>
                        <input onkeyup="showUserName(this)" value="<?php echo explode(" ", $user_info->data->user_login)[0]; ?>" maxlength="25" type="text" required="required" class="form-control" name="user_login" id="user_login" placeholder="<?php _e('Type here the username that you will use for login', 'tainacan'); ?>">
                        <span id="result_username"></span>
                    </div>

                    <div class="form-group">
                        <label for="user_type"><?php _e('User type', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <select class="form-control" name="user_type" id="user_type">
                            <option value="employee" <?php if(strcmp($user_meta['user_type'][0], 'employee') == 0) echo 'selected' ?>><?php _e("Employee", "tainacan"); ?></option>
                            <option value="reader" <?php if(strcmp($user_meta['user_type'][0], 'reader') == 0) echo 'selected' ?> ><?php _e("Reader", "tainacan"); ?></option>
                        </select>
                    </div>


                    <?php
                        $user_situation = $user_meta['user_situation'][0];
                        $active_situation = array("active" => '', 'blocked' => '', 'pendencies' => '', 'inactive' => '');
                        $active_situation[$user_situation] = 'selected';
                    ?>

                    <div class="form-group">
                        <label for="user_situation"><?php _e('User situation', 'tainacan'); ?><span style="color: #EE0000;"> *</span></label>
                        <select class="form-control" name="user_situation" id="user_situation">
                            <option value="active" <?php echo $active_situation['active']; ?>><?php _e("Active", "tainacan"); ?></option>
                            <option value="blocked" <?php echo $active_situation['blocked']; ?>><?php _e("Blocked", "tainacan"); ?></option>
                            <option value="pendencies" <?php echo $active_situation['pendencies']; ?>><?php _e("Has pendencies", "tainacan"); ?></option>
                            <option value="inactive" <?php echo $active_situation['inactive']; ?>><?php _e("Inactive", "tainacan"); ?></option>
                        </select>
                    </div>

                    <!------------------------------------------------------- Opcional ------------------------------------------------------->
                    <!-- Sexo -->
                    <div class="form-group">
                        <label for="gender"><?php _e('Gender', 'tainacan'); ?><!--span style="color: #EE0000;"> *</span--></label>
                        <?php
                            if($user_meta['gender'][0] == 'm')
                            {
                                $male = 'selected';
                                $female = '';
                            }else if ($user_meta['gender'][0] == 'f')
                            {
                                $female = 'selected';
                                $male = '';
                            }
                        ?>
                        <select class="form-control" name="gender" id="user_gender">
                            <option <?php echo $male; ?> value="m"><?php _e("Male", "tainacan"); ?></option>
                            <option <?php echo $female; ?> value="f"><?php _e("Female", "tainacan"); ?></option>
                        </select>
                    </div>

                    <!-- Telefone celular    -->
                    <div class="form-group">
                        <label for="mobile_phone"><?php _e('Mobile phone', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['mobile_phone'][0]; ?>" type="tel" placeholder="<?php _e("Mobile phone", "tainacan"); ?>" name="mobile_phone" id="mobile_phone">
                    </div>

                    <!-- Telefone fixo    -->
                    <div class="form-group">
                        <label for="land_line"><?php _e('Land line', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['land_line'][0]; ?>" type="tel" placeholder="<?php _e("Land line", "tainacan"); ?>" name="land_line" id="land_line">
                    </div>

                    <!-- RG -->
                    <div class="form-group">
                        <label for="rg"><?php _e('RG', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['rg'][0]; ?>" type="" placeholder="<?php _e("RG", "tainacan"); ?>" name="rg" id="rg">
                    </div>

                    <!-- CPF -->
                    <div class="form-group">
                        <label for="cpf"><?php _e('CPF', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['CPF'][0]; ?>" placeholder="<?php _e("CPF", "tainacan"); ?>" name="CPF" id="CPF">
                    </div>

                    <!-- CEP -->
                    <div class="form-group">
                        <label for="CEP"><?php _e('CEP', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['CEP'][0]; ?>" type="" placeholder="<?php _e("CEP", "tainacan"); ?>" name="CEP" id="CEP">
                    </div>


                    <!-- Endereço -->
                    <div class="form-group">
                        <label for="address"><?php _e('Address', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['address'][0]; ?>" type="text" placeholder="<?php _e("Address", "tainacan"); ?>" name="address" id="address">
                    </div>

                    <div class="form-group">
                        <label for="number"><?php _e('Number', 'tainacan'); ?></label>
                        <input class="form-control" min="1" value="<?php echo $user_meta['number'][0]; ?>" type="number" placeholder="<?php _e("Number", "tainacan"); ?>" name="number" id="number">
                    </div>

                    <div class="form-group">
                        <label for="additional_address"><?php _e('Additional address', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['additional_address'][0]; ?>" type="text" placeholder="<?php _e("Additional address", "tainacan"); ?>" name="additional_address" id="additional_address">
                    </div>

                    <!-- Data de nascimento -->
                    <div class="form-group">
                        <label for="birthday"><?php _e('Birthday', 'tainacan'); ?></label>
                        <input class="form-control" value="<?php echo $user_meta['birthday'][0]; ?>" type="date" placeholder="<?php _e("Birthday", "tainacan"); ?>" name="birthday" id="birthday">
                    </div>

                    <div class="form-group">
                        <label for="about_you"> <?php _e('About you', 'tainacan'); ?> </label>
                        <input value="<?php echo $user_meta['about_you'][0]; ?>" type="text" name="about_you" class="form-control about_you">
                    </div>
                    <div class="form-group">
                        <label for="current_work"> <?php _e('Current workplace', 'tainacan'); ?> </label>
                        <input type="text" value="<?php echo $user_meta['current_work'][0]; ?>" name="current_work" class="form-control current_work">
                    </div>
                    <div class="form-group">
                        <label for="prof_resume"> <?php _e('Professional Resume', 'tainacan'); ?> </label>
                        <input type="text" value="<?php echo $user_meta['prof_resume'][0]; ?>" name="prof_resume" class="form-control prof_resume">
                    </div>
                    
                    <!-- Input das mascaras -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.min.js"></script>
                    <script>
                        $("#mobile_phone").mask('(00) 0-0000-0000');
                        $("#land_line").mask('(00) 0000-0000');
                        $("#CPF").mask( '000.000.000-00');
                        $("#CEP").mask('00000-000');
                        $("#rg").mask('00.000.000-0')
                    </script>
                </form>
                <?php
                break;
            case 'update_user_info':
                return update_user_properties($_POST);
                break;
            case 'search_for_user':
                $user_name = $_POST['user_name'];
                return  search_for_user($user_name);
                break;
            case 'get_impressions_page':
                return $this->render(dirname(__FILE__) . '../../views/impressions.php');
                break;
            default:
                return false;
                break;
        }
    }
}

$operation = isset($_POST['operation']) ? $_POST['operation'] : false;
if(!$operation)
{
    $operation = isset($_GET['operation']) ? $_GET['operation'] : false;
}

$userController = new userController();
echo $userController->operation($operation);