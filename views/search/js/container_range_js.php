<script>
     function remove_range(id) {
        $('#range_'+id).hide();
        $('input[name=range_'+id+'_1').val('');
        $('input[name=range_'+id+'_2').val('');
     }

     //verifco se o input eh numero
function isNumber(id, event) {
    var element = $("#" + id);
    var len = element.val().length + 1;
    var max = element.attr("maxlength");

    var cond = (46 < event.which && event.which < 58) || (46 < event.keyCode && event.keyCode < 58);

    if (!(cond && len <= max)) {
        event.preventDefault();
        return false;
    }
}
</script>
