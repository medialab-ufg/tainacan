<form  id="submit-form" 
       onkeypress="return (event.keyCode == 13) ? false : true ;" 
       style="margin-left: 15px;">
    <?php 
    if($modeView&&$modeView=='one'){
        include_once(dirname(__FILE__).'/formItemMetadata.php');
    }else{
        include_once(dirname(__FILE__).'/formItemMedia.php');
    } 
    ?>
</form>