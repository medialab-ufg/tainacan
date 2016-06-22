<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/delete_js.php');
$post = get_post($_GET['id']);
?>
<form method="post" action="" id="submit_form">
	  <span><?= __('Confirm the exclusion of the object: ').$post->post_title ?></span>
	  <input type="hidden" id="ID" name="ID" value="<?= $post->ID ?>">
	  <input type="hidden" id="operation" name="operation" value="delete">
	  <button type="submit" id="submit" class="btn btn-default"><?php _e('Submit','tainacan'); ?></button>
</form>