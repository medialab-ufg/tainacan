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
                var sortedIds = $("#filters-accordion").sortable("toArray");
                $("#filters-accordion").removeClass("adding-meta");
                if ( $ui_container === "filters-accordion" ) {
                    updateFacetPosition(sortedIds);
                }
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
  
</script>
