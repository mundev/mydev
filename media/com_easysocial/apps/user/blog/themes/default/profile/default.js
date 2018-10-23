EasySocial.require()
.done(function($) {
	$('[data-post-delete]').on('click', function(){
		var id = $(this).data('id');
		var form = $('[data-post-trash=' + id + ']');
		
		form.submit();
	});
})