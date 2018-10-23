<script type="text/javascript">
EasySocial.require()
.script('https://cdn.onesignal.com/sdks/OneSignalSDK.js')
.done(function($) {
	var OneSignal = window.OneSignal || [];

	OneSignal.push(["init", {
		appId: "<?php echo $params->get('app_id');?>",
		<?php if ($subdomain) { ?>
		subdomainName: '<?php echo $subdomain;?>',
		<?php } ?>
		<?php if ($params->get('safari_id')) { ?>
		safari_web_id: "<?php echo $params->get('safari_id');?>",
		<?php } ?>
		autoRegister: <?php echo $params->get('autoregister') ? 'true' : 'false'; ?>,
		notifyButton: {
			enable: false
		},
		welcomeNotification: {
			<?php if ($params->get('show_welcome', true)) { ?>
				"title": "<?php echo JText::_('APP_USER_ONESIGNAL_WELCOME_TITLE', true);?>",
				"message": "<?php echo JText::_('APP_USER_ONESIGNAL_WELCOME_MESSAGE', true);?>"
			<?php } else { ?>
				disable: true
			<?php } ?>
		}
	}]);


	OneSignal.push(function() {

		OneSignal.getTags(function(tags) {

			OneSignal.push(['sendTags', {
				"id": "<?php echo $this->my->id;?>",
				"email": "<?php echo $this->my->email;?>",
				"type": "user"
			}]);
		});
	});
});
</script>
