
EasyBlog.require()
.script('site/dashboard/quickpost/form')
.done(function($) {

	$('[data-eb-quickpost]').implement(EasyBlog.Controller.Quickpost.Form);

	<?php if ($active == 'photo') { ?>
		var controller = $('[data-eb-quickpost]').controller();
		var photoController = controller.options.photo;

		photoController.initializeUploader();
	<?php } ?>


	$(document).on('change.quickpost.autopost', '[data-autopost-item]', function() {

		var element = $(this),
			checked = element.is(':checked');

		if (checked) {
			element.parent().addClass('checked');
		} else {
			element.parent().removeClass('checked');
		}

	});
});