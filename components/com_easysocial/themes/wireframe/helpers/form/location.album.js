EasySocial.require()
.script("site/locations/locations")
.done(function($){
	$('<?php echo $selector; ?>').addController(EasySocial.Controller.Locations);
});
