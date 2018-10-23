<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-panel <?php echo $templateEditor ? 'hide' : 'active'; ?>" data-eb-composer-panel data-id="post-options">

	<div class="eb-composer-panel-content" data-scrolly="y">
		<div data-scrolly-viewport>
			<?php echo $this->output('site/composer/panels/post/general/default'); ?>

			<?php if ($this->acl->get('allow_custom_notifications')) { ?>
			<?php echo $this->output('site/composer/panels/post/notifications/default'); ?>
			<?php } ?>
	
			<?php echo $this->output('site/composer/panels/post/category/default'); ?>

			<?php echo $this->output('site/composer/panels/post/autopost/default'); ?>
			
			<?php if ($this->config->get('layout_composer_tags')) { ?>
				<?php echo $this->output('site/composer/panels/post/tags/default'); ?>
			<?php } ?>

			<?php if (($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) && EB::isAssociationEnabled() && $languages) { ?>
				<?php echo $this->output('site/composer/panels/post/association/default'); ?>
			<?php } ?>

			<?php if (!$templateEditor) { ?>
				<?php echo $this->output('site/composer/panels/post/author/default'); ?>
			<?php } ?>

			<?php if ($post->getType() == 'link') { ?>
			<div class="eb-composer-fieldset" data-name="post_quicklink">
				<div class="eb-composer-fieldset-header">
					<strong><?php echo JText::_('COM_EASYBLOG_QUICKPOST_LINK_LABEL');?></strong>
				</div>
				<div class="eb-composer-fieldset-content o-form-horizontal">
					<input class="form-control" type="text" name="link" value="<?php echo $post->getAsset('link')->getValue();?>" />
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
