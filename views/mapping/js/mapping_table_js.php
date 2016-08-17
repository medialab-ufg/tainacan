<script>
    $(function () {
       init_sortables();
    });
    
    function init_sortables(){
        console.log($('#generic-mapped-ul').innerHeight());
      $( "#tainacan-properties-ul, #tainacan-mapped-ul" ).sortable({
            cursor: "n-resize",
            revert: 250,
            connectWith: ".connected-tainacan",
            receive: function(event, ui) {
                var $ui_container = ui.item.context.parentNode.id;
                var item_id =  ui.item.context.id;
                $( "#" + item_id + " .action-icons").append( $sorter_span );
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
    //            if(is_blocked||$( "#" + item_id.replace('meta-item-','')).length>0){
    //                list_collection_metadata();
    //                showAlertGeneral('<?php _e('Attention!','tainacan') ?>','<?php _e('Metadata already inserted or not allowed as filter','tainacan') ?>','info');
    //                return false;
    //            }
    //            
    //            if ( $ui_container === "filters-accordion" ) {
    //                list_collection_metadata();
    //                $("#filters-accordion").addClass("receiving-metadata");
    //                $( "#" + item_id + " .action-icons").append( $sorter_span );
    //                if ( is_fixed_meta ) {
    //                    setCollectionFacet("add", item_id, "tree");
    //                    showAlertGeneral('<?php _e('Success','tainacan') ?>','<?php _e('Metadata inserted as filter successfully','tainacan') ?>','success');
    //                    $('.data-widget').removeClass('select-meta-filter');
    //                } else {
    //                    
    //                console.log(' item_search_widget :'+item_search_widget);
    //                    if ( item_search_widget === "null" || item_search_widget == "undefined" ) {
    //                        $("#"+item_id + " a").first().click();
    //                        $(".property_data_use_filter").click();
    //                        $('.data-widget').addClass('select-meta-filter').show();
    //                        $('.term-widget').addClass('select-meta-filter').show();
    //                    } else {
    //                        $('.data-widget').removeClass('select-meta-filter');
    //                        $('.term-widget').removeClass('select-meta-filter');
    //                        setCollectionFacet( "add", item_id, item_search_widget );
    //                    }
    //                }
    //
    //            } else if ( $ui_container === "metadata-container" ) {
    //                $(ui.item.context).addClass('hide');
    //            }
            },
            remove: function(event, ui) {
    //            var $ui_container = ui.item.context.parentNode.id;
    //            if ( $ui_container === "metadata-container" ) {
    //                removeFacet(ui.item.context.id);
    //            }
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
    //            var $ui_container = ui.item.context.parentNode.id;
    //            if ( $ui_container === "metadata-container" ) {
    //                var data = [];
    //                $("#metadata-container li").each(function(i, el){
    //                    var p = $(el).attr('id').replace("meta-item-", "");
    //                    data.push(p);
    //               });
    //               $.ajax({
    //                    type: "POST",
    //                    url: $('#src').val() + "/controllers/collection/collection_controller.php",
    //                    data: {
    //                        collection_id: $('#collection_id').val(), 
    //                        operation: 'update_ordenation_properties', 
    //                        ordenation: data.join(',')}
    //                });
    //            }

            }        

        }).disableSelection();  
    }
  
</script>
