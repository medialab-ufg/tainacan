<script>
     $(function () {
        init_dynatre_contest('#default_search_dynatree','#default_search_select');
        init_dynatre_contest('#exclude_search_dynatree','#exclude_search_select');
    });
    
    function init_dynatre_contest(seletor_dynatree,seletor_select) {
             $(seletor_dynatree).dynatree({
                    selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                    checkbox: true,
                    initAjax: {
                        url: $('#src').val() + '/modules/<?php echo MODULE_CONTEST ?>/controllers/item/item_controller.php',
                        data: {
                            collection_id: $("#collection_id").val(),
                            operation: 'initDynatreeConfigurationContest'
                        }
                        , addActiveKey: true
                    },
                    onLazyRead: function (node) {
                        node.appendAjax({
                            url: $('#src').val() + '/controllers/collection/collection_controller.php',
                            data: {
                                collection: $("#collection_id").val(),
                                key: node.data.key,
                                classCss: node.data.addClass,
                                //operation: 'findDynatreeChild'
                                operation: 'expand_dynatree'
                            }
                        });
                    },
                    onClick: function (node, event) {
                        // Close menu on click

                    },
                    onKeydown: function (node, event) {
                    },
                    onCreate: function (node, span) {$('.dropdown-toggle').dropdown();
                    },
                    onPostInit: function (isReloading, isError) {
                    },
                    onActivate: function (node, event) {
                    },
                    onSelect: function (flag, node) {
                        var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                            return node;
                        });
                        $(seletor_select).html('');
                        $.each(selKeys,function(index,key){
                            $(seletor_select).append('<option selected="selected" value="'+key.data.key+'" >'+key.data.title+'</option>')
                        });
                    },
                    dnd: {
                    }
                });
    }
</script>    