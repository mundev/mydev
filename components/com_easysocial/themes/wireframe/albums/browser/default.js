EasySocial.require()
.script("site/albums/browser")
.done(function($){

	$("[data-album-browser=<?php echo $uuid; ?>]")
		.addController("EasySocial.Controller.Albums.Browser", {
			"uid": "<?php echo $lib->uid;?>",
			"type": "<?php echo $lib->type; ?>"
		});

	$('[data-album-showall]').on('click', function() {

		var button = $(this);

		EasySocial.ajax('site/views/albums/showMoreAlbums', {
			"totalalbums": "<?php echo $totalAlbums; ?>",
			"startlimit": "<?php echo $startlimit; ?>",
			"userAlbumOwnerId": "<?php echo $lib->uid; ?>",
			"albumType": "<?php echo $lib->type; ?>",
			"albumId": "<?php echo $id; ?>"

		}).done(function(contents) {

			// append the rest of the albums item
			$('[data-album-list-item-container-regular]').append(contents);

			// hide the view all button
			button.hide();
		});
	});			
});
