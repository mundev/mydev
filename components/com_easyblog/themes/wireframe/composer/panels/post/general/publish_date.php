<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-form-group">
	<label for="publish_up" class="o-control-label eb-composer-field-label">
		<?php echo JText::_('COM_EASYBLOG_COMPOSER_PUBLISH_DATE'); ?>
	</label>

	<div class="o-control-input" data-publish>
		<span data-preview>
			<?php if ($post->publish_up && $post->publish_up != '0000-00-00 00:00:00') { ?>
				<?php echo $this->html('string.date', $post->getFormDateValue('publish_up'), JText::_('COM_EASYBLOG_DATE_DMY24H')); ?>
			<?php } else { ?>
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_IMMEDIATELY');?>
			<?php } ?>
		</span>

		<a href="javascript:void(0);" class="btn btn-eb-default-o btn--xs" data-calendar>
			<i class="fa fa-calendar"></i>
		</a>

		<a href="javascript:void(0);" class="btn btn-eb-default-o btn--xs" style="display: none;" data-cancel>
			<i class="fa fa-undo"></i>
		</a>

		<input type="hidden" name="publish_up" data-datetime value="<?php echo $post->getFormDateValue('publish_up'); ?>" />
	</div>
</div>