EasySocial.require()
.script('site/avatar/avatar', 'site/cover/cover')
.done(function($){

	$('[data-avatar]').implement(EasySocial.Controller.Avatar, {
		"uid": "<?php echo $event->id;?>",
		"type": "<?php echo SOCIAL_TYPE_EVENT;?>",
		"redirectUrl": "<?php echo base64_encode($event->getPermalink(false));?>"
	});

	$('[data-cover]').implement(EasySocial.Controller.Cover, {
		"uid": "<?php echo $event->id;?>",
		"type": "<?php echo SOCIAL_TYPE_EVENT;?>"
	});

});