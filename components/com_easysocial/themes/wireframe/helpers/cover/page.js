EasySocial.require()
.script('site/avatar/avatar', 'site/cover/cover')
.done(function($){

	$('[data-cover]').implement(EasySocial.Controller.Cover, {
		"uid": "<?php echo $page->id;?>",
		"type": "page"
	});

	$('[data-avatar]').implement(EasySocial.Controller.Avatar, {
		"uid": "<?php echo $page->id;?>",
		"type": "page"
	});

});