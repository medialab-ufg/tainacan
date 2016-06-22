<?php
/*
 * Template Name: Index
 * Description: teste
 */
  get_header();
?>

<div class="row">
    <div class="col-md-2">
    </div>
    <div class="col-md-10">
        <div id="header_collection">
        </div>
    </div>
</div>
<div class="row">
	<div class="col-md-2">
            <div id="dynatree">
	    </div>
	</div>	
	<div class="col-md-10">
		<input type="hidden" id="src" name="src" value="<?= get_template_directory_uri() ?>">
		<div id="remove">
		</div>
		<div id="form">
		</div>	
		<div id="list">
		</div>
		<br>
		<a id="create_button" href="#" class="btn btn-primary">Create</a>&nbsp;
		<a id="home_button" href="#" class="btn btn-primary">Home</a>
	</div>
</div>
