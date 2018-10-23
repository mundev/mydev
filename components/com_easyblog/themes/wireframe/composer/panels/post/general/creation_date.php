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
	<label for="created" class="o-control-label eb-composer-field-label">
		<?php echo JText::_('COM_EASYBLOG_COMPOSER_CREATION_DATE'); ?>
	</label>

	<div class="o-control-input" data-created>
		<span data-preview>
			<?php if ($post->created) { ?>
				<?php echo $this->html('string.date', $post->getFormDateValue('created'), JText::_('COM_EASYBLOG_DATE_DMY24H')); ?>
			<?php } else { ?>
				<?php echo JText::_('COM_EASYBLOG_COMPOSER_NOW');?>
			<?php } ?>
		</span>

		<a href="javascript:void(0);" class="btn btn-eb-default-o btn--xs" data-calendar>
			<i class="fa fa-calendar"></i>
		</a>

		<a href="javascript:void(0);" class="btn btn-eb-default-o btn--xs" data-cancel style="display: none;">
			<i class="fa fa-undo"></i>
		</a>

		<input type="hidden" name="created" data-datetime value="<?php echo $post->getFormDateValue('created');?>" />        
	</div>
</div>
