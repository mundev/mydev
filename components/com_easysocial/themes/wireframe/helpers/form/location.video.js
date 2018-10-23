EasySocial.require()
.script("site/locations/locations")
.done(function($){
	$('<?php echo $selector; ?>').addController(EasySocial.Controller.Locations, {
		latitude: <?php echo !empty($location->latitude) ? $location->latitude : '""'; ?>,
		longitude: <?php echo !empty($location->longitude) ? $location->longitude : '""'; ?>
	});
});
