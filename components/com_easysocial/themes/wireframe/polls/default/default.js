EasySocial.require()
.script('site/polls/default')
.done(function($) {
	$('[data-es-polls]').implement(EasySocial.Controller.Polls, {
		"clusterId" : '<?php echo $cluster ? $cluster->id : false ?>',
		"clusterType" : '<?php echo $cluster ? $cluster->getType() : false ?>'
	});
});