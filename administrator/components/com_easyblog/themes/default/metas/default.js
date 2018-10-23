EasyBlog.require()
.script('admin/grid')
.done(function($) {
	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

	$.Joomla('submitbutton', function(action) {

		if (action == 'meta.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog';
			return false;
		}

		if (action == 'meta.restore') {
			EasyBlog.dialog({
				"content": EasyBlog.ajax('admin/views/metas/updateMetaConfirmation')
			});
			
			return false;
		};

		$.Joomla('submitform', [action]);
	});

});
