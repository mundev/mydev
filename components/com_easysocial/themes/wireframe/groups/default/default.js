
EasySocial
.require()
.script('site/groups/browser')
.done(function($){
	$('[data-es-groups]').implement(EasySocial.Controller.Groups.Browser, {
		"filter": "<?php echo $filter;?>",
		
		<?php if ($activeCategory) { ?>
		"categoryId": "<?php echo $activeCategory->id;?>",
		<?php } ?>

		"userId": "<?php echo $activeUserId ? $activeUserId : '';?>",
		"latitude": '<?php echo $hasLocation ? $userLocation['latitude'] : ''; ?>',
		"longitude": '<?php echo $hasLocation ? $userLocation['longitude'] : ''; ?>',
	});
});
