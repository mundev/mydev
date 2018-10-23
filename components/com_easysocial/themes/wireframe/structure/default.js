
<?php if (!$this->my->guest) { ?>
EasySocial.require()
.script('site/system/notifier', 'site/vendors/gritter', 'site/system/notifications')
.done(function($) {

	<?php if ($this->config->get('notifications.broadcast.popup')) { ?>
	$('body').on('notifier.updates', function(event, data) {

		if (data.broadcasts == undefined || data.broadcasts == false) {
			return;
		}

		// Own data
		if (data.broadcasts.length <= 0) {
			return;
		}

		var period = "<?php echo $this->config->get('notifications.broadcast.period');?>";
		var sticky = <?php echo $this->config->get('notifications.broadcast.sticky') ? 'true' : 'false'; ?>;

		// Means something to do
		$(data.broadcasts).each(function(i, item) {

			var info = {
				title: item.title,
				raw_title: item.raw_title,
				text: item.content,
				image: item.authorAvatar,
				sticky: sticky,
				time: period * 1000,
				class_name: 'es-broadcast'
			};

			$.gritter.add(info);
		});

	});
	<?php } ?>

	$('body').implement(EasySocial.Controller.System.Notifier, {
		"interval": <?php echo ES_NOTIFIER_POLLING_INTERVAL; ?>,
		"guest": <?php echo $this->my->guest ? 'true' : 'false'; ?>
	});

	$('body').implement(EasySocial.Controller.System.Notifications, {
		"interval": <?php echo $this->config->get('notifications.polling.interval');?>,
		"userId": "<?php echo $this->my->id;?>"
	});
});
<?php } ?>