EasySocial.ready(function($) {
	
	// Create recaptcha task
	var task = [
		'recaptcha_<?php echo $uid;?>', {
			'sitekey': '<?php echo $key;?>',
			'theme': '<?php echo $theme;?>'
		}
	];

	<?php if ($invisible) { ?>
	window.recaptchaDfd = $.Deferred();

	window.getResponse = function() {

		var token = grecaptcha.getResponse();
		var responseField = $('[data-recaptcha-response]');

		if (token) {
			responseField.val(token);

			window.recaptchaDfd.resolve();

			return;
		}

		grecaptcha.reset();

		window.recaptchaDfd.reject();
	};

	$('[data-field-<?php echo $elementId;?>]').on('onSubmit', function(event, register) {
		register.push(window.recaptchaDfd);

		grecaptcha.execute();
	});
	<?php } ?>

	// Render recaptcha form
	var runTask = function() {

		// Only run if the task really exists
		if (task) {
			<?php if (!$invisible) { ?>
			// Captcha input
			grecaptcha.render.apply(grecaptcha, task);
			<?php } ?>

			<?php if ($invisible) { ?>
			// Invisible captcha
			grecaptcha.render($('[data-recaptcha-invisible]')[0], {
						"sitekey": "<?php echo $key;?>"
			});
			<?php } ?>
		}
	}

	// If grecaptcha is not ready, add to task queue
	if (!window.grecaptcha) {
		var tasks = window.recaptchaTasks || (window.recaptchaTasks = []);
		tasks.push(task);
	} else {
		runTask(task);
	}

	// If recaptacha script is not loaded
	if (!window.recaptchaScriptLoaded) {
		EasySocial.require().script("//www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=<?php echo $language;?>");
		window.recaptchaScriptLoaded = true;
	}

	window.recaptchaCallback = function() {

		if (tasks) {
			while (task = tasks.shift()) {
				runTask(task);
			}

			runTask(task);
		}
	};
});