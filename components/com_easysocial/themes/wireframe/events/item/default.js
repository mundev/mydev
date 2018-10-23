EasySocial
.require()
.script('site/events/item')
.done(function($){

	// Process the title so that there is no double qoute issue.
	var title = "<?php echo addslashes($title);?>"
	
	$('[data-es-event]').addController('EasySocial.Controller.Events.Item', {
		title : title
	});

	// Implement puller on the dashboard
	<?php if ($this->isMobile()) { ?>
	EasySocial.require()
	.script('site/vendors/puller')
	.done(function($) {

		window.initPuller = function() {
			return window.es.puller.init({
									mainElement: '.es-profile-header',
									triggerElement: '.es-profile-header',
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
