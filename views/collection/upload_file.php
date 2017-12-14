<?php
if( $_SERVER["REQUEST_METHOD"] === "POST" ) {
    include_once('../../../../../wp-config.php');
    include_once('../../../../../wp-load.php');
    include_once('../../../../../wp-includes/wp-db.php');

    $upload_dir = wp_upload_dir();
    $imagePath = $upload_dir["path"] . "/";
    $imageURL = $upload_dir["url"] . "/";

    $allowedExts = ["gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG"];
    $temp = explode(".", $_FILES["img"]["name"]);
    $extension = end($temp);

    print "Image Path: ".$imagePath."\n";
    if (!is_writable($imagePath)) {
        print json_encode(["status" => 'error', "message" => _t("Can't upload File - permission denied.")]);
        return false;
    }

    if (in_array($extension, $allowedExts)) {
        if ($_FILES["img"]["error"] > 0) {
            $response = ["status" => 'error', "message" => 'ERROR Return Code: ' . $_FILES["img"]["error"]];
        } else {
            $filename = $_FILES["img"]["tmp_name"];
            list($width, $height) = getimagesize($filename);
            $_sanitized_img_name = sanitize_file_name(remove_accents($_FILES["img"]["name"]));
            $_new_file_name = $imagePath . $_sanitized_img_name;
            move_uploaded_file($filename, $_new_file_name);

            $response = ["status" => 'success', "url" => $imageURL . $_sanitized_img_name, "width" => $width, "height" => $height];
        }
    } else {
        $response = ["status" => 'error', "message" => __("Something went wrong. Is file too large for upload?", "tainacan")];
    }

    print json_encode($response);
}