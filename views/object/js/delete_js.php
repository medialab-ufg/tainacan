<script> 
$(function(){  
    var src = $('#src').val();
	$( '#submit_form' ).submit( function( e ) {
	 $.ajax( {
		  url: src+'/controllers/object/object_controller.php',
		  type: 'POST',
		  data: new FormData( this ),
		  processData: false,
		  contentType: false
		} ).done(function( result ) {
			$('#remove').hide();
			showList(src);
			$('#create_button').show();
		}); 
		e.preventDefault();
	});
});
</script>
            