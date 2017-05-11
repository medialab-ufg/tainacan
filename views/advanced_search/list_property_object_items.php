<?php
    /**
     * 
     * View que lista os itens encontrados na busca de propriedade de objeto em proprieddades Simples
     * 
     */
    include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
    include_once(dirname(__FILE__) . '/../../helpers/object/object_properties_widgets_helper.php');
    $object = new ObjectWidgetsHelper;
    $found_items = []
?>
<div class="col-md-12">
<?php
 if (isset($loop_objects) && $loop_objects->have_posts()) :  ?>
    <table class="table table-bordered"  style="margin-top: 15px;" id="found_items_property_object_<?php echo $property_id ?>">
        <?php
        while ($loop_objects->have_posts()) : $loop_objects->the_post();
            if(($avoid_selected_items === '1'|| $avoid_selected_items == 'true') && $object->is_selected_property($property_id,  get_the_ID())){
                continue;
            }
            if(in_array( get_the_ID(), $found_items))
                  continue;   
            ?>
            <tr id="line_property_object_<?php echo $property_id ?>_<?php echo get_the_ID() ?>">
                <td style="width: 100%;font-size: 12pt;" class="title-text" onclick="temporary_insert_items('<?php echo get_the_ID() ?>','<?php echo $property_id ?>')">
                    <span><?php the_title(); ?></span>
                    <input style="display:none;" 
                           type="checkbox" 
                           name="temporary_items_<?php echo $property_id ?>[]" 
                           class="item_values_<?php echo get_the_ID() ?>"
                           value="<?php echo get_the_ID() ?>">
                </td>
            </tr>    
        <?php  
            $found_items[] = get_the_ID();
        endwhile; 
        ?>
    </table>    
     <?php   
endif;
?>
</div>
<!---------------------- FIM:LISTA DE OBJETOS ------------------------------------->   
<div class="col-md-12 no-padding" style="padding-right: 0px;margin-top: 15px;">
    <button type="button" 
            class="btn btn-default btn-lg pull-left" 
            onclick="$('#metadata-search-<?php echo $property_id ?>').show();$('#metadata-result-<?php echo $property_id ?>').hide();">
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
            $('#core_validation_<?php echo $property_id; ?>').val('true');
            set_field_valid(property_id,'core_validation_'+property_id);
            //se ja existir retiro o botao de adiconar do lado esquerdo
            if($('#line_property_object_<?php echo $property_id ?>_'+$(value).attr('item')).length>0){
                $('#line_property_object_<?php echo $property_id ?>_'+$(value).attr('item')).css('color','#fff').css('background-color','#4285f4');
                $('#line_property_object_<?php echo $property_id ?>_'+$(value).attr('item')+' .item_values_'+$(value).attr('item')).attr('checked','checked');
            }
        });
        $('#selected_items_property_object_<?php echo $property_id ?>').parent().height($('#found_items_property_object_<?php echo $property_id ?>').height())
    });
    //adicona o item nos selecionados
    function temporary_insert_items(id,property_id){
        if(!$('#line_property_object_'+property_id+'_'+id+' .item_values_'+id).is(':checked')){
            $('#line_property_object_'+property_id+'_'+id).css('color','#fff').css('background-color','#4285f4');
            $('#line_property_object_'+property_id+'_'+id+' .item_values_'+id).attr('checked','checked');
        }else{
            remove_line_property_object(id,property_id)
        }
    }
    //remove o item dos temporarios
    function remove_line_property_object(id,property_id){
         $('#line_property_object_'+property_id+'_'+id).css('color','black').css('background-color','white');
         $('#line_property_object_'+property_id+'_'+id+' .item_values_'+id).removeAttr('checked');
         //$('#remove_line_property_object_'+property_id+'_'+id).remove();
    }
    // adiciona nos inseridos
    function save_selected_items_property_object(){
        var results = 0;
        //percorro todos os selecionados para serem inseridos
        $.each($('input[name="temporary_items_<?php echo $property_id ?>[]"]:checked'),function(index,value){
            results++;
            if($('#inserted_property_object_<?php echo $property_id ?>_'+$(value).val()).length==0){
                var object_id = ($('#object_id_add').length > 0) ? $('#object_id_add').val() : $('#object_id_edit').val();
                if($('#cardinality_<?php echo $property_id ?>_'+object_id).val()=='1'){
                    $('#results_property_<?php echo $property_id; ?> ul').html('');
                    $('select[name="socialdb_property_<?php echo $property_id; ?>[]"]').html('');
                }
                $('#results_property_<?php echo $property_id; ?> ul')
                        .append('<li id="inserted_property_object_<?php echo $property_id ?>_'+$(value).val()+'" item="'+$(value).val()+'" class="selected-items-property-object property-<?php echo $property_id; ?>">'+$('#line_property_object_<?php echo $property_id ?>_'+$(value).val()+' .title-text').html()
                        +'<span  onclick="remove_item_objet(this)" style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span></li>');
                add_in_item_value($(value).val(),<?php echo $property_id ?>);
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
        //validacao do campo
        $('#core_validation_'+property_id).val('true');
        set_field_valid(property_id,'core_validation_'+property_id);
    }
    //remove no formulario de fato
    function remove_in_item_value(id,property_id){
        $('select[name="socialdb_property_'+property_id+'[]"]  option[value="'+id+'"]').remove();
    }
    
</script>



