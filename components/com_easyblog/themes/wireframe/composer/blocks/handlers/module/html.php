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
<div class="eb-composer-placeholder eb-composer-link-placeholder text-center" data-module-form>
	<i class="eb-composer-placeholder-icon fa fa-cube"></i>
	<b class="eb-composer-placeholder-title" data-module-placeholder-title><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_MODULES');?></b>
	<p class="eb-composer-placeholder-brief" data-module-placeholder-info><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_MODULES_INFO');?></p>
	<p class="eb-composer-placeholder-brief hidden" style="color: #3c763d;" data-module-placeholder-notice><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_MODULES_NOTICE');?></p>

	<p class="eb-composer-placeholder-error text-error hidden" data-module-error>
		<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MODULES_EMPTY'); ?>
	</p>

	<?php if (isset($installedModules) && $installedModules) { ?>
	<div class="input-group" data-module-selection-content style="width: 70%; margin: 0 auto;">
		<select data-module-selection data-flag="1">
			<option><?php echo JText::_('Select Module');?></b></option>
			<?php foreach($installedModules as $module) { ?>
				<option value="<?php echo $module->module; ?>-<?php echo $module->id; ?>" data-id="<?php echo $module->id; ?>"><?php echo $module->title; ?></option>
			<?php } ?>
		</select>
	</div>
	<?php } else { ?>
		<p class="eb-composer-placeholder-error text-error">
			<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_NO_MODULES'); ?>
		</p>
	<?php } ?>
</div>
<div class="eb-composer-placeholder eb-composer-video-placeholder text-center hidden" data-module-loader>
	<i class="fa fa-refresh fa-spin mr-5"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_MODULES_LOADING');?>
</div>
