EasySocial.require()
.script("site/photos/browser")
.done(function($){

	$("[data-photo-browser=<?php echo $uuid; ?>]")
		.addController("EasySocial.Controller.Photos.Browser");

	<?php if ($total > $limit) { ?>
	$('[data-es-photos-loadmore]').on('click', function() {
		var button = $(this);
		var id = $(this).data('id');
		var controller = $('[data-photo-browser=<?php echo $uuid; ?>]').controller();

		button.addClass('is-loading');

		EasySocial
			.ajax('site/views/photos/loadSidebarPhotos', {
				"albumId": id
			}).done(function(photos) {

				button.removeClass('is-loading');
				button.remove();

				$('[data-photo-list-item-group]').append(photos);

				controller.setLayout();
			});
	});
	<?php } ?>
});