
EasySocial.ready(function($) {

	$(document)
		.on('click.points.history', '[data-pagination]', function() {

			var button = $(this);
			var current = button.data('pagination');

			EasySocial.ajax('site/views/points/getHistory', {
				"limitstart": current,
				"id": "<?php echo $user->id;?>"
			}).done(function(contents, next, done) {

				$('[data-timeline]').append(contents);

				button.data('pagination', next);

				if (done) {
					button.addClass('t-hidden');
				}
			});
		});
});