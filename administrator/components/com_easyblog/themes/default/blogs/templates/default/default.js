EasyBlog.require()
.script('admin/grid')
.done(function($)
{
	// Implement controller on the form
	$('[data-grid-eb]').implement(EasyBlog.Controller.Grid);

	$.Joomla("submitbutton", function(action) {

		if (action == 'blogs.createTemplate') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=templates&tmpl=component';
			return false;
		}

		$.Joomla('submitform', [action]);
	});
});