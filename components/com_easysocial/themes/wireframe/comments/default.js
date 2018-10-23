<?php if ($loadScripts) { ?>
EasySocial.require()
.script('site/comments/frame','site/vendors/lightbox')
.done(function($) {
	$('[data-es-comments]').addController('EasySocial.Controller.Comments', {
		'attachments': <?php echo $this->config->get('comments.attachments') ? 'true' : 'false';?>,
		'errorMessage': "<?php echo JText::_('COM_ES_COMMENT_ERROR_MESSAGE'); ?>"
	});
});
<?php } ?>