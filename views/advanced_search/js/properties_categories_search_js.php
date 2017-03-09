<script>
    $(function () {
        search_list_properties_term_insert_objects_adv();
        var search_properties_autocomplete = search_get_val($("#search_properties_autocomplete_<?php echo $property_searched_id ?>").val());
        autocomplete_object_property_add(search_properties_autocomplete);
    });
    
//************************* properties terms ******************************************//
    function search_list_properties_term_insert_objects_adv() {
        var radios = search_get_val($("#search_properties_terms_radio_<?php echo $property_searched_id ?>").val());
        var selectboxes = search_get_val($("#search_properties_terms_selectbox_<?php echo $property_searched_id ?>").val());
        var trees = search_get_val($("#search_properties_terms_tree_<?php echo $property_searched_id ?>").val());
        var checkboxes = search_get_val($("#search_properties_terms_checkbox_<?php echo $property_searched_id ?>").val());
        var multipleSelects = search_get_val($("#search_properties_terms_multipleselect_<?php echo $property_searched_id ?>").val());
        var treecheckboxes = search_get_val($("#search_properties_terms_treecheckbox_<?php echo $property_searched_id ?>").val());
        search_list_radios(radios);
        search_list_tree(trees);
        search_list_selectboxes(selectboxes);
        search_list_multipleselectboxes(multipleSelects);
        search_list_checkboxes(checkboxes);
        search_list_treecheckboxes(treecheckboxes);
    }
</script>
