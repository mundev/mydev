
EasySocial.require()
.script('site/audios/list')
.done(function($){

	// Implement audios listing controller
	$('[data-audios-listing]').implement(EasySocial.Controller.Audios.List, {
		"uid": "<?php echo $uid;?>",
		"type": "<?php echo $type;?>",
		"active": "<?php echo !$filter ? 'all' : $filter;?>"
	});
});