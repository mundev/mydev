<?php if ($this->config->get('layout_composer_autopostdate')) { ?>
EasyBlog.require()
.library(
	'datetimepicker',
	'moment/<?php echo $momentLanguage;?>'
)
.script('shared/datetime')
.done(function($) {

	$('[data-autopost]').implement('EasyBlog.Controller.Post.Datetime', {
		format: "<?php echo JText::_('COM_EASYBLOG_MOMENTJS_DATE_DMY24H'); ?>",
		emptyText: "<?php echo JText::_('COM_EB_COMPOSER_IMMEDIATELY_AUTOPOST'); ?>",
		language: "<?php echo $momentLanguage;?>"
	});
});
<?php } ?>