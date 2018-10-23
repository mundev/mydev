
EasySocial.require()
.script('site/dashboard/default')
.done(function($){

	$('[data-es-dashboard]').implement(EasySocial.Controller.Dashboard, {
		title: "<?php echo $title;?>"
	});

	// Implement puller on the dashboard
	<?php if ($this->isMobile()) { ?>
	EasySocial.require()
	.script('site/vendors/puller')
	.done(function($) {

		var targetElement = '[data-story-form]';

		// If story form not available, we use the first stream item
		if ($(targetElement).length == 0) {
			targetElement = '[data-stream-item]:first-child';
		}

		window.initPuller = function() {
			return window.es.puller.init({
									mainElement: targetElement,
									triggerElement: targetElement,
									onRefresh: function (done) {
										setTimeout(function () {
											var controller = $('body').controller(EasySocial.Controller.System.Notifier);

											controller.check(true, true);
											done();

										}, 150);
									}
								});
		};

		var puller = this.initPuller();
	});
	<?php } ?>
});
