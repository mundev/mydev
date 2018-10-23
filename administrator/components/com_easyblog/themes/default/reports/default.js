
EasyBlog.require()
.script('admin/grid')
.done(function($) {

	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

	$('[data-delete-post]').on('click', function(){
		var id = $(this).data('id');

		EasyBlog.dialog({
			'content': EasyBlog.ajax('admin/views/reports/confirmDelete', {
							"id": id
						})
		});
	});

	$('[data-unpublish-post]').on('click', function(){
		var id = $(this).data('id');

		EasyBlog.dialog({
			'content': EasyBlog.ajax('admin/views/reports/confirmUnpublish', {
							"id": id
						})
		});
	});

	$.Joomla("submitbutton", function(action){

		$.Joomla("submitform", [action]);
	});
});