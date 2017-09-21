$(window).load(function () {
//silence is gold
});
/*
 *  funcao que abre a view de gerenciamento de arquivos
 */
function showArchivalManagement(src) {
    $.ajax({
        url: src + '/controllers/archival_management/archival_management_controller.php',
        type: 'POST',
        data: {operation: 'list', collection_id: $("#collection_id").val()}
    }).done(function (result) {
        $('#main_part').hide();
        $('#configuration').html(result);
        $('#configuration').show();
    });
}

function clear_archival_fields_category(){
    $('#current_phase_year').html('');
    $('#current_phase_month').html('');
    $('#current_phase_string').html('');
    $('#intermediate_phase_year').html('');
    $('#intermediate_phase_month').html('');
    $('#classification_code').html('');
    $('#classification_code').html('');
    $('#observation').val('');
}

function set_fields_modal_categories(elem){
    if(elem.socialdb_category_current_phase){
       if($.isNumeric( elem.socialdb_category_current_phase )){
            var months = parseInt(elem.socialdb_category_current_phase.trim());
            $("#current_phase_year").val(Math.floor(months / 12));
            $("#current_phase_month").val(months % 12);
       }else{
           $('#current_phase_checkbox').attr('checked','checked');
           $('#current_phase_number').hide();
           $('#current_phase_text').show();
           $("#current_phase_string").val(elem.socialdb_category_current_phase);
       }
    }
    if(elem.socialdb_category_intermediate_phase){
        if($.isNumeric( elem.socialdb_category_intermediate_phase )){
            var months = parseInt(elem.socialdb_category_intermediate_phase.trim());
            $("#intermediate_phase_year").val(Math.floor(months / 12));
            $("#intermediate_phase_month").val(months % 12);
       }else{
           $("#intermediate_phase_string").val(elem.socialdb_category_current_phase);
       }
    }
    if(elem.socialdb_category_destination){
        $('input:radio[name="socialdb_event_term_destination"]').filter('[value="'+elem.socialdb_category_destination+'"]').attr('checked', true);
    }
    if(elem.socialdb_category_classification_code){
        $("#classification_code").val(elem.socialdb_category_classification_code);
    }
    if(elem.term && elem.term.description){
         $("#observation").val(elem.term.description);
    }
} 

/**
     * funcao que nao permite que o numer oem anos seja menor do que 0
     * @argument {object DOM} input O valor colocado no input
     * @returns void
     */
    function handleChange(input) {
        if (input.value <= 0)
            input.value = '';
    }