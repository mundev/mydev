EasySocial.module('site/api/floatlabels', function($) {

	var module = this;

	var inputGroups = '.o-form-group';
	var inputSelectors = '.o-form-control';

	$(document).on('focus.o.form.group change.o.form.group', inputSelectors, function() {
		var self = $(this);
		var label = self.closest(inputGroups);

		label.addClass('is-focused');
	});


	$(document).on('blur.o.form.group', inputSelectors, function() {
		var self = $(this);
		var label = self.closest(inputGroups);
		var value = self.val();

		// When there is a value, we should inject is-filled.
		if ($.trim(value) !== '') {
			label.addClass('is-filled');
			label.removeClass('is-focused');
			return;
		}

		label.removeClass('is-filled');
		label.removeClass('is-focused');
	});
	
	$(inputSelectors).trigger('blur.o.form.group');

	module.resolve();
});