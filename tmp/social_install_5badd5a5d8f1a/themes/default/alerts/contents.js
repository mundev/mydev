
EasySocial.require()
.script('https://cdn.onesignal.com/sdks/OneSignalSDK.js')
.done(function($) {

	$('[data-es-alert-item]').on('click', function() {
		var tab = $(this);
		var type = tab.data('type');
		var formAction = $('[data-es-form-action]');

		// Hide the form actions if user wants to configure push notifications
		if (type == 'push') {
			formAction.addClass('t-hidden');
			return;
		}

		formAction.removeClass('t-hidden');
	});

	OneSignal.push(function() {
		OneSignal.isPushNotificationsEnabled(function(isSubscribed) {

			if (isSubscribed !== false) {
				$('[data-alerts-onesignal-push]')
					.attr('checked', true)
					.trigger('change');

			} else {
				$('[data-alerts-onesignal-push]')
					.attr('checked', false)
					.trigger('change');
			}

			$('[data-alerts-onesignal-push]')
				.on('change', function() {
					var checked = $(this).is(':checked');

					// If the user wants to subscribe, we should prompt the registration
					if (checked && !isSubscribed) {

						// For some reason some of the site unable to send subscribe when they re-enabled again from user edit notification page
						OneSignal.push(["setSubscription", true]);

						OneSignal.registerForPushNotifications();
						return;
					}

					if (checked) {
						OneSignal.push(["setSubscription", true]);
						// OneSignal.registerForPushNotifications();

						return;
					}

					// If the user unchecks, we need to send request to onesignal to unsubscribe the user
					OneSignal.push(["setSubscription", false]);
				});
		});
	});
});