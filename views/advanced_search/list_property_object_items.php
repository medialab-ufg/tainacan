<div class="col-md-6 no-padding">
<?php
 if (isset($loop_objects) && $loop_objects->have_posts()) :  ?>
    <table class="table table-bordered" id="found_items_property_object_<?php echo $property_id ?>">
        <?php
        while ($loop_objects->have_posts()) : $loop_objects->the_post();
        ?>
            <tr id="line_property_object_<?php echo $property_id ?>_<?php echo get_the_ID() ?>">
                <td style="width: 90%;font-size: 12pt;" class="title-text">
                    <span><?php the_title(); ?></span>
                </td>
                <td  style="width: 5%;cursor: pointer;" 
                     class="click"
                     onclick="temporary_insert_items('<?php echo get_the_ID() ?>','<?php echo $property_id ?>')">
                        <span class="glyphicon glyphicon-chevron-right"/>
                </td>
            </tr>    
        <?php    
        endwhile; 
        ?>
    </table>    
     <?php   
endif;
?>
</div>
<div style="border: 1px solid #ddd;" class="col-md-6">
    <table class="table table-bordered" id="selected_items_property_object_<?php echo $property_id ?>"></table>
</div>
<!---------------------- FIM:LISTA DE OBJETOS ------------------------------------->   
<div class="col-md-12 no-padding" style="padding-right: 0px;margin-top: 15px;">
    <button type="button" 
            class="btn btn-default btn-lg pull-left" 
            onclick="back_to_search_form()">
        <?php _e('Cancel','tainacan')?>
    </button>
    <button type="button" 
            class="btn btn-primary btn-lg pull-right" 
            onclick="save_selected_items_property_object()">
        <?php _e('Add','tainacan')?>
    </button>
</div>
<script>
    $(function(){
        //verificando se dentro os ja inseridos como relacionamento estao dentro do resultado da busca
        $.each($('#results_property_<?php echo $property_id; ?> ul li'),function(index,value){
            //se ja existir retiro o botao de adiconar do lado esquerdo
            if($('#line_property_object_<?php echo $property_id ?>_'+$(value).attr('item')).length>0){
                $('#line_property_object_<?php echo $property_id ?>_'+$(value).attr('item')).css('color','#ccc');
                $('#line_property_object_<?php echo $property_id ?>_'+$(value).attr('item')+' .click').attr('onclick','');
            }
        });
        $('#selected_items_property_object_<?php echo $property_id ?>').parent().height($('#found_items_property_object_<?php echo $property_id ?>').height())
    });
    //adicona o item nos selecionados
    function temporary_insert_items(id,property_id){
        $('#line_property_object_'+property_id+'_'+id).css('color','#ccc');
        $('#line_property_object_'+property_id+'_'+id+' .click').attr('onclick','');
        $('#selected_items_property_object_'+property_id)
                .append('<tr item="'+id+'" id="remove_line_property_object_'+property_id+'_'+id+'"><td style="width: 90%;font-size: 12pt;cursor:pointer" onclick="remove_line_property_object('+id+','+property_id+')">'
                +$('#line_property_object_'+property_id+'_'+id+' .title-text').html()+'</td></tr>');
    }
    //remove o item dos temporarios
    function remove_line_property_object(id,property_id){
         $('#line_property_object_'+property_id+'_'+id).css('color','black');
         $('#line_property_object_'+property_id+'_'+id+' .click').attr('onclick',"temporary_insert_items('"+id+"','"+property_id+"')");
         $('#remove_line_property_object_'+property_id+'_'+id).remove();
    }
    // adiciona nos inseridos
    function save_selected_items_property_object(){
        var results = 0;
        //percorro todos os selecionados para serem inseridos
        $.each($('#selected_items_property_object_<?php echo $property_id ?> tr'),function(index,value){
            results++;
            if($('#inserted_property_object_<?php echo $property_id ?>_'+$(value).attr('item')).length==0){
                $('#results_property_<?php echo $property_id; ?> ul')
                        .append('<li id="inserted_property_object_<?php echo $property_id ?>_'+$(value).attr('item')+'" item="'+$(value).attr('item')+'" class="selected-items-property-object property-<?php echo $property_id; ?>">'+$(value).children().text()
                        +'<span  onclick="remove_item_objet(this)" style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span></li>');
                add_in_item_value($(value).attr('item'),<?php echo $property_id ?>);
            }
        });
        if(results>0){
            $('#no_results_property_<?php echo $property_id; ?>').hide()
        }
    }
    //remove dos selecionados
    function remove_item_objet(seletor){
        var id = $(seletor).parent().attr('item');
        remove_line_property_object(id,'<?php echo $property_id; ?>');//retirando do contianer abaixo
        $(seletor).parent().remove();
        remove_in_item_value(id,'<?php echo $property_id; ?>');//retirando do item
        if($('#results_property_<?php echo $property_id; ?> ul li').length==0){
             $('#no_results_property_<?php echo $property_id; ?>').show();
        }
    }
    
    //adiciona no formulario de fato
    function add_in_item_value(id,property_id){
        $('select[name="socialdb_property_'+property_id+'[]"]').append('<option value="'+id+'" selected="selected">'+id+'</option>');
    }
    //remove no formulario de fato
    function remove_in_item_value(id,property_id){
        $('select[name="socialdb_property_'+property_id+'[]"]  option[value="'+id+'"]').remove();
    }
    
</script>



