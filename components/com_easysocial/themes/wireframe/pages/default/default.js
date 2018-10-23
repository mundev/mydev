
EasySocial
.require()
.script('site/pages/default')
.done(function($){
	$('[data-es-pages]').implement(EasySocial.Controller.Pages.Browser, {
		"userId": "<?php echo $activeUserId ? $activeUserId : '';?>"
	});
});
