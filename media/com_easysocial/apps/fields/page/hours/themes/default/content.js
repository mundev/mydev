EasySocial.ready(function($) {

	$(document)
		.on('change.hours.type', '[data-hours-type]', function() {

			var input = $(this);
			var type = input.val();

			$('[data-hours-selection]').toggleClass('t-hidden', type == 'always' || type == 'disabled');
		});




	$(document)
		.on('change.hours.day', '[data-hours-day]', function() {
			var dayCheckbox = $(this);
			var checked = dayCheckbox.is(':checked');

			// Get the main wrapper for that particular day row
			var wrapper = dayCheckbox.parents('[data-hours-day-wrapper]');
			var timeWrapper = wrapper.find('[data-hours-time]');

			var start = wrapper.find('[data-hours-start]');
			var end = wrapper.find('[data-hours-end]');

			var timeformat = wrapper.data('hours-format');

			// Reset the value
			if (!checked) {
				timeWrapper.addClass('t-hidden');
				start.val('');
				end.val('');

				return;
			}

			timeWrapper.removeClass('t-hidden');

			var startValue = start.val();
			var endValue = end.val();

			var defaultStart = '9:00';
			var defaultEnd = '6:00';

			if (timeformat == '2') {
				var defaultStart = '09:00';
				var defaultEnd = '18:00';
			}

			if (!startValue) {
				start.val(defaultStart);
			}

			if (!endValue) {
				end.val(defaultEnd);
			}
		});

	// When any of the hours input is focused
	$(document)
		.on('focus.hours.period', '[data-hours-start],[data-hours-end]', function() {
			var input = $(this);
			input.select();
		});

	// When any of the hours input loses focus
	$(document)
		.on('blur.hours.period', '[data-hours-start],[data-hours-end]', function() {

			var input = $(this);
			var wrapper = input.parents('[data-hours-day-wrapper]');
			var value = input.val();
			var timeformat = wrapper.data('hours-format');
			var isValid = /^(0?[1-9]|1[012])(:[0-5]\d)$/.test(value);

			// Default values
			var startDefault = '9:00';
			var endDefault = '6:00';

			if (timeformat == '2') {

				// Since this is 24-hour format,
				// we need to make sure there is always a 2-digit for hour eg. 09:00
				var split = value.split(":");

				if (split[0].length == 1) {
					value = '0' + value;

					// update the input value
					input.val(value);
				}

				var isValid = /^([01]\d|2[0-3]):?([0-5]\d)$/.test(value);

				// Default values
				var startDefault = '09:00';
				var endDefault = '18:00';
			}

			if (!isValid && input.hasClass('start-hour')) {
				input.val(startDefault);
			}

			if (!isValid && input.hasClass('end-hour')) {
				input.val(endDefault);
			}
		});

	// When any of the hours input is focused
	$(document)
		.on('click.hours.add', '[data-hours-add]', function() {
			var input = $(this);
			var dayWrapper = $(this).closest('[data-hours-day-wrapper]');
			var timesDiv = $(this).closest('[data-hours-time]');
			var cloneDiv = $(timesDiv).clone();

			// incase this is the 1st timing set, we know the remove icon is hidden.
			// we need to display the remove icon.
			$(cloneDiv).find('[data-hours-remove]').removeClass('t-hidden');
			$(dayWrapper).append(cloneDiv);

		});

	$(document)
		.on('click.hours.remove', '[data-hours-remove]', function() {
			$(this).closest('[data-hours-time]').remove();
		});
});
