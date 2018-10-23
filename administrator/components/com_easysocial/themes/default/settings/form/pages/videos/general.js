EasySocial.ready(function($) {

	$('[data-video-uploads]').on('change', function() {
		var checkbox = $(this);
		var checked = checkbox.is(':checked');

		$('[data-video-encoding]').toggleClass('t-hidden', !checked);
	});
});