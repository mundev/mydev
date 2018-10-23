
EasySocial.require()
.script('site/apps/discussions/discussions')
.done(function($) {
	$('[data-es-discussions]').implement(EasySocial.Controller.Apps.Discussions, {
		"id": "<?php echo $cluster->id;?>"
	});
})