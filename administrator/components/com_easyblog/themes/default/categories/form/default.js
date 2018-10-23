EasyBlog.require()
.script('admin/tabs')
.done(function($) {

	$('[data-eb-form]').implement(EasyBlog.Controller.Admin.Tabs);

	$('[data-repost-social]').on('change', function() {
		var checked = $(this).val() == 1;
		var dependents = $('[data-repost-social-days]');

		if (checked) {
			dependents.removeClass('hide');
			return;
		}
		
		dependents.addClass('hide');
	});

	$.Joomla('submitbutton', function(task) {

		if (task == 'category.cancel') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easyblog&view=categories';
			return false;            
		}

		if (task == 'saveNew') {
			$('#savenew').val('1');
			task = 'save';
		}

		$.Joomla('submitform', [task]);
	});


	$('#private').on('change', function() {
		var val = $(this).val(),
			el = $('[data-category-access]');

		if (val == 2) {
			$(el).removeClass('hide');
		} else {
			$(el).addClass('hide');
		}
	});

	$('[data-category-inherit]').on('change', function() {
		var checked = $(this).is(':checked');
		var form = $('[data-category-post-options]');

		form.toggleClass('hide', checked);
	});

	$('[data-category-avatar-remove-button]').on('click', function() {
		var id = $(this).data('id');

		EasyBlog.dialog({
			content: EasyBlog.ajax('admin/views/categories/confirmRemoveAvatar'),
			bindings: {
				'{removeButton} click': function() {

					EasyBlog.ajax('admin/controllers/category/removeAvatar', {'id' : id})
					.done(function() {
						
						$('[data-category-avatar-image]').remove();

						EasyBlog.dialog().close();
					});
				}
			}
		});
	});
});