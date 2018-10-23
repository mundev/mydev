<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<b><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TED_URL'); ?></b>
	</div>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div style="margin: 0 auto;" class="o-input-group">
				<input type="text" value="" class="o-form-control" data-ted-fieldset-url />
				<span class="o-input-group__btn">
					<a href="javascript:void(0);" class="btn btn-eb-default-o" data-ted-fieldset-update-url><?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON'); ?></a>
				</span>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset">
	<div class="eb-composer-fieldset-header">
		<b><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_FLUID_LAYOUT'); ?></b>
	</div>

	<div class="eb-composer-fieldset-content">
		<div class="text-center">
			<?php echo $this->html('form.toggler', 'youtube_fluid', true, 'youtube_fluid', 'data-ted-fieldset-fluid'); ?>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset hide" data-ted-fieldset-width-fieldset>
	<div class="eb-composer-fieldset-header">
		<b><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_WIDTH'); ?></b>
	</div>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div class="col-lg-6 col-lg-offset-3">
				<div class="o-input-group">
					<input type="text" data-ted-fieldset-width value="" class="o-form-control text-center" />
					<span class="o-input-group__addon"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PIXELS');?></span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="eb-composer-fieldset hide" data-ted-fieldset-height-fieldset>
	<div class="eb-composer-fieldset-header">
		<b><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_EMBED_VIDEO_HEIGHT'); ?></b>
	</div>

	<div class="eb-composer-fieldset-content">
		<div class="o-form-group">
			<div class="col-lg-6 col-lg-offset-3">
				<div class="o-input-group">
					<input type="text" data-ted-fieldset-height value="" class="o-form-control text-center" />
					<span class="o-input-group__addon"><?php echo JText::_('COM_EASYBLOG_COMPOSER_PIXELS');?></span>
				</div>
			</div>
		</div>
	</div>
</div>