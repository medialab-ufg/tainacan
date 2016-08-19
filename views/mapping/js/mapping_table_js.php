<script>
    $(function () {
       init_sortables();
    });
    
    function init_sortables(){
        var size = $('#count_found_properties').val() * 80;
        $('#tainacan-properties-ul').height(size);
        $('#tainacan-mapped-ul').height(size);
        $('#generic-mapped-ul').height(size);
        $('#generic-properties-ul').height(size);
        $( "#generic-mapped-ul").addClass('border-table-mapping');
        $( "#generic-mapped-ul").addClass('border-table-mapping');
        $( "#generic-properties-ul").addClass('border-table-mapping');
        $( "#tainacan-properties-ul, #tainacan-mapped-ul" ).sortable({
            cursor: "n-resize",
            connectWith: ".connected-tainacan",
            receive: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var item_id =  ui.item.context.id;
                var block_input = ui.item.hasClass('tainacan-create-properties-li');
                if(block_input){
                    ui.sender.sortable("cancel");
                }
                if ( $ui_container === "filters-accordion" ) {
                    
                }
            },
            remove: function(event, ui) {

            },
            stop: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var sortedIds = $( "#tainacan-mapped-ul").sortable("toArray");
                update_position_mapped(sortedIds);
               // $("#metadata-container").removeClass("change-meta-container");
            },
            sort: function(event, ui) {
               // $("#filters-accordion").addClass("adding-meta");
               // var filtros_atuais = get_current_filters();
            },
            update: function( event, ui ) { 
            }        

        }).disableSelection();  
        
       $( "#generic-mapped-ul, #generic-properties-ul" ).sortable({
            connectWith: ".connected-generic",
            revert: 250,
            receive: function(event, ui) {
                return false;
                var $ui_container = ui.item.context.parentNode.id;
                var item_id =  ui.item.context.id;
            },
            remove: function(event, ui) {
            },
            stop: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var sortedIds = $("#filters-accordion").sortable("toArray");
                $("#filters-accordion").removeClass("adding-meta");
                if ( $ui_container === "filters-accordion" ) {
                    updateFacetPosition(sortedIds);
                }
            },
            sort: function(event, ui) {
            },
            update: function( event, ui ) { 
            }        

        }).disableSelection();  
    }
    /**
     * 
     * @param {type} array
     * @returns {undefined}
     */
    function update_position_mapped(array){
        var mapped_tainacan = [];
        var mapped_extracted =  $( "#generic-mapped-ul").sortable("toArray");
        var max = mapped_extracted.length;
        
        for(var i = 0;i < array.length;i++){
            if(i>=max&&array[i].indexOf("new_")<0){
               $copy = $('#'+array[i]).clone();
               $("#tainacan-mapped-ul").find('#'+array[i]).remove();
               $("#tainacan-properties-ul").append($copy);
               //$('#'+array[i]).append("#tainacan-properties-ul");
            }else if(i<max){
               mapped_tainacan.push(array[i]);
               set_name_mapped('#create_property_'+i,i);
            }
        }
        
        $('#mapped_tainacan_properties').val(mapped_tainacan.join(','));
        $('#mapped_generic_properties').val(mapped_extracted.join(','));
    }
    
    function set_name_mapped(seletor,id){
        if($(seletor).is(':checked')){
            //if($('#name_property_'+id).val()==''){
                 var mapped_tainacan =  $( "#tainacan-mapped-ul").sortable("toArray");
                 var mapped_extracted =  $( "#generic-mapped-ul").sortable("toArray");
                if(mapped_tainacan.length>0&&mapped_extracted.length>0){
                     for(var i = 0;i<mapped_extracted.length;i++){
                         if(mapped_tainacan[i]=='new_'+id){
                             $('#name_property_'+id).val(mapped_extracted[i])
                         }
                     }
                 }
            //}
        }
    }
  
</script>
