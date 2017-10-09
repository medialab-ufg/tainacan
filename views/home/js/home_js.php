<script type="text/javascript">
  var responsive_slider_settings = [];
  var breakpoints = [ 1500, 1265, 1075, 860, 670, 480 ];
  var total_breakpoints = breakpoints.length;
  $(breakpoints).each(function(index, element) {
    var num_slides = total_breakpoints - index;
    var infinite = ( num_slides > 1) ;
    var obj = {
      breakpoint: element,
      settings: {
        slidesToShow: num_slides,
        slidesToScroll: num_slides,
        infinite: infinite
      }
    };
    responsive_slider_settings.push(obj);
  });

  function getSlickSettings() {
    return {
      infinite: true,
      slidesToShow: total_breakpoints + 1,
      slidesToScroll: total_breakpoints + 1,
      responsive: responsive_slider_settings
    }
  }

$('.carousel-home').slick( getSlickSettings() );
$('.carousel-home-ajax').slick( getSlickSettings() );

  var item_types = ['video', 'image', 'pdf', 'text', 'audio'];
  var ajax_carousel = function() {
    $('.carousel-home-ajax').slick('unslick').slick(getSlickSettings());
  };

  $(item_types).each( function(idx, type) {
      $.ajax({
          url: '<?php echo get_template_directory_uri() ?>' + '/controllers/home/home_controller.php',
          type: 'POST',
          data: { operation: 'load_item_type', item_type: type },
          complete: ajax_carousel,
          error: function (jqXHR, textStatus, errorThrown) {
          }
      }).done( function(result) {
          var element = JSON.parse(result);

          $(element).each( function(index, el) {
              var thumb = el.thumbnail;
              var item_url = '<?php echo home_url('/') ?>' + el.collection_name + '/' + el.object.post_name;

              var item_html = '<div class="item-individual-box item-box-container"> <div class="panel panel-default"> <div class="panel-body">';
              item_html = item_html + '<a href="' + item_url + '">' + thumb + '</a></div>';
              item_html = item_html + '<div class="panel-footer home-title" style="padding:3px;"> <a href="' + item_url + '">';
              item_html = item_html + '<span class="collection-name"> </span>' + el.object.post_title + ' </a> </div> </div> </div>';

              var target = $('.type-container.' + el.type + ' .carousel-home-ajax' );
              $(target).append( item_html );

              var items_count = $('.' + el.type + ' .item-box-container').length;
              if ( items_count >= 1  ) {
                  $('.type-container.' + el.type).css('display', 'block');
              }
          });
      });
  });
  
  function showModalCreateCollection() {
        $('#newCollection').modal('show');
    }
</script>