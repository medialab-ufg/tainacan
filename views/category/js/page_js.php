<script>
    $(function () {
         $('.dropdown-toggle').dropdown();
          var src = $('#src').val();  
          get_link(<?php echo $term->term_id ?>);
          var stateObj = {foo: "bar"};
          history.replaceState(stateObj, "page 2", '?category=<?php echo $term->slug ?>');
         // showCategoryDynatreePage(src);
        $('#category_button_links').click(function (e) {
           var posX = $(this).position().left;
           var posY = $(this).position().top;
            $('#category_ul_links').css('left', posX);
            $('#category_ul_links').css('top', posY+22);
            e.preventDefault();
        });
        list_comments_term('comments_term',<?php echo $term->term_id ?>); 
      });
    
    /**
     * 
     */
    function get_link(term_id){
        $.ajax({
                url: $('#src').val() + '/controllers/category/category_controller.php',
                type: 'POST',
                data: { 
                    operation: 'get_link_individuals', 
                    collection_id: $("#collection_id").val(),
                    term_id:term_id
                }
            }).done(function (result) {
              elem = jQuery.parseJSON(result);
              $('#link-individuals').attr('href',elem.url);
            });
    }
    
    
</script>