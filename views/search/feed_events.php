<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
$count = 0;
if(!is_array($events)){
    exit();
}
?>
<div  style="margin-top: 5px;height: 300px;overflow-y: scroll;">
        <div class="autores">
            <div class="list-group">
                <?php
                 foreach ($events as $event) {
                     if($count==20)
                         break;
                     $datetime1  = new DateTime(date("Y-m-d",$event['date']));
                     $datetime2  = new DateTime(date("Y-m-d",time()));
                     $interval = $datetime1->diff($datetime2);
                     ?>
                         <a href="#" class="list-group-item" style="margin: 5px;">
                             <h7 style="font-size: 12px;" class="list-group-item-heading">
                                 <span class="glyphicon glyphicon-pencil"></span>
                                     <?php echo $event['author'] ?>:
                                     <?php echo $event['type'] ?>
                             </h7>
                           <p class="list-group-item-text" style="font-size: small;margin-left:15px;"><?php echo $event['name'] ?></p>
                             <span style="font-size: 10px;margin-left:15px; ">
                                 <?php printf(__('%1$d days ago','tainacan'),$interval->format('%d')) ?>
                             </span>
                         </a>                                                        
                     <?php
                     $count++;
                 }
                 ?>
            </div>         
        </div> 

</div>    