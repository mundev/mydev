
EasyBlog.require()
.script('admin/grid')
.done(function($){
    // Implement controller on the form
    $('[data-grid-eb]').implement(EasyBlog.Controller.Grid, {
    	"overrideCheckbox": false
    });
});

EasyBlog.ready(function($){



	$(document).on('click.feed.import', '[data-feed-import]', function(){
		var id = $(this).data('id'),
			log = $(this).parent().find('[data-feed-import-log]');

		EasyBlog.ajax('admin/views/feeds/download', {
			"id" : id
		})
		.done(function(result) {

			var className = result.code == 400 ? 'text-error' : 'text-success';

			log.addClass(className).html(result.message);
		})
		.fail(function(result) {
			log.addClass('text-error').html(result);
		});
	});



	$.Joomla("submitbutton", function(action) {

		if (action == 'feeds.add') {
			window.location = '<?php echo JURI::base();?>index.php?option=com_easyblog&view=feeds&layout=form';
			return false;
		}

		$.Joomla("submitform", [action]);
	});
});
