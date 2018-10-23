
EasySocial
.require()
.script('site/followers/default').done(function($) {

	$('[data-es-followers]').implement(EasySocial.Controller.Followers, {
		"active": "<?php echo $active;?>"
	});

});
