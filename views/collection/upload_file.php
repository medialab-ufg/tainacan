<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');

$upload_dir = wp_upload_dir();
$imagePath = $upload_dir["path"]."/";
$imageURL = $upload_dir["url"]."/";

$allowedExts = ["gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG"];
$temp = explode(".", $_FILES["img"]["name"]);
$extension = end($temp);

if(!is_writable($imagePath)) {
	$response = [ "status" => 'error', "message" => __( "Can't upload File - permission denied.", "tainacan") ];
	print json_encode($response);
	return;
}

if ( in_array($extension, $allowedExts)) {
	if ($_FILES["img"]["error"] > 0) {
		 $response = [ "status" => 'error', "message" => 'ERROR Return Code: '. $_FILES["img"]["error"] ];
	} else {
		$filename = $_FILES["img"]["tmp_name"];
		list($width, $height) = getimagesize( $filename );
		
		move_uploaded_file($filename,  $imagePath . $_FILES["img"]["name"]);

	  $response = [ "status" => 'success', "url" => $imageURL.$_FILES["img"]["name"], "width" => $width, "height" => $height ];
	} 
} else {
   $response = [ "status" => 'error', "message" => __("Something went wrong. Is file too large for upload?", "tainacan") ]; 
}
	  
print json_encode($response);
?>