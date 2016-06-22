<?php 
/*
 * View Responsavel em mostrar as licenças na hora de INSERCAO do objeto, NAO UTILIZADA NOS EVENTOS
 */
include_once ('js/show_insert_object_licenses_js.php'); 
$has_cc = 0;
if(isset($licenses) && !empty($licenses)): ?>
    <?php foreach ($licenses as $license) { ?>
            <?php if(strpos($license['nome'], 'Creative Commons') !== false) $has_cc = 1;?>
            <div class="radio">
                <input type="checkbox" 
                              class="object_license" 
                              name="object_license[]" 
                              value="<?php echo $license['id']; ?>" 
                              id="radio<?php echo $license['id']; ?>" 
                              <?php 
                              if($license['id'] == $pattern[0]){ 
                                  $has_checked = true;
                                  echo "checked='checked'"; 
                              } 
                              ?> ><label><?php echo $license['nome']; ?></label>
            </div>
    <?php  } ?>
<?php else: ?>    
    <input type="hidden" class='hide_license' value="true">
<?php endif; ?>

<?php if(isset($has_checked)): ?>    
    <input type="hidden" class='already_checked_license' value="true">
<?php endif; ?>    
    