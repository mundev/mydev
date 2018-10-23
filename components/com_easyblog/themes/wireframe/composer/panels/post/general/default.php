<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) ||
		  ($this->config->get('layout_composer_creationdate')) ||
		  ($this->config->get('layout_composer_publishingdate')) ||
		  ($this->config->get('layout_composer_unpublishdate')) ||
		  ($this->config->get('layout_composer_autopostdate') && EB::oauth()->isAutopostEnabled()) ||
		  ($this->config->get('main_copyrights')) ||
		  ($this->config->get('main_password_protect') && !$post->isFeatured()) ||
		  ($this->acl->get('contribute_frontpage')) ||
		  ($this->acl->get('change_setting_subscription') && $this->config->get('main_subscription')) ||
		  ($this->config->get('layout_composer_comment') && $this->config->get('main_comment') && $this->acl->get('change_setting_comment'))
		) { ?>
<div class="eb-composer-fieldset" data-name="post_properties">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_('COM_EASYSOCIAL_COMPOSER_GENERAL');?></strong>
	</div>
	<div class="eb-composer-fieldset-content o-form-horizontal">

		<?php if ($this->config->get('layout_composer_privacy') && $this->acl->get('enable_privacy')) { ?>
			<?php echo $this->output('site/composer/panels/post/general/privacy'); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_author_alias', false)) { ?>
			<?php echo $this->output('site/composer/panels/post/general/alias'); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_creationdate')) { ?>
			<?php echo $this->output('site/composer/panels/post/general/creation_date'); ?>
		<?php } else { ?>
			<input type="hidden" name="created" data-datetime value="<?php echo $post->getFormDateValue('created');?>" />
		<?php } ?>

		<?php if ($this->config->get('layout_composer_publishingdate')) { ?>
			<?php echo $this->output('site/composer/panels/post/general/publish_date'); ?>
		<?php } else { ?>
			<input type="hidden" name="publish_up" data-datetime value="<?php echo $post->getFormDateValue('publish_up'); ?>" />
		<?php } ?>

		<?php if ($this->config->get('layout_composer_unpublishdate')) { ?>
			<?php echo $this->output('site/composer/panels/post/general/unpublish_date'); ?>
		<?php } else { ?>
			<input type="hidden" name="publish_down" data-datetime value="<?php echo $post->publish_down != EASYBLOG_NO_DATE ? $post->getFormDateValue('publish_down') : ''; ?>" />
		<?php } ?>

		<?php if ($this->config->get('layout_composer_autopostdate') && EB::oauth()->isAutopostEnabled()) { ?>
			<?php echo $this->output('site/composer/panels/post/general/autoposting_date'); ?>
		<?php } else { ?>
			<input type="hidden" name="autopost_date" data-datetime value="<?php echo $post->autopost_date != EASYBLOG_NO_DATE ? $post->getFormDateValue('autopost_date') : ''; ?>" />
		<?php } ?>

		<?php if ($this->config->get('main_password_protect') && !$post->isFeatured()) { ?>
			<?php echo $this->output('site/composer/panels/post/general/password'); ?>
		<?php } ?>
		
		<?php if ($this->config->get('main_multi_language') && $this->config->get('layout_composer_language')) { ?>
			<?php echo $this->output('site/composer/panels/post/general/language'); ?>
		<?php } ?>

		<?php if ($this->config->get('main_copyrights')) { ?>
			<?php echo $this->output('site/composer/panels/post/general/copyright'); ?>
		<?php } ?>

		<?php if ($this->acl->get('contribute_frontpage')) { ?>
			<?php if ($this->config->get('layout_composer_frontpage')) { ?>
				<?php echo $this->html('composer.field', 'form.toggler', 'frontpage', 'COM_EASYBLOG_COMPOSER_FRONTPAGE', $post->frontpage); ?>
			<?php } else { ?>
				<input type="hidden" name="frontpage" value="<?php echo $post->frontpage ? 1 : 0;?>" />
			<?php } ?>
		<?php } ?>

		<?php if (! $post->blogpassword && $this->config->get('layout_composer_feature') && $this->acl->get('feature_entry')) { ?>
			<?php echo $this->html('composer.field', 'form.toggler', 'isfeatured', 'COM_EASYBLOG_COMPOSER_FEATURE_POST', $post->isfeatured); ?>
		<?php } ?>

		<?php if ($this->config->get('layout_composer_comment') && $this->config->get('main_comment') && $this->acl->get('change_setting_comment')) { ?>
			<?php echo $this->html('composer.field', 'form.toggler', 'allowcomment', 'COM_EASYBLOG_COMPOSER_ALLOW_COMMENTS', $post->allowcomment); ?>
		<?php } ?>

		<?php if ($this->acl->get('change_setting_subscription') && $this->config->get('main_subscription')) { ?>
			<?php echo $this->html('composer.field', 'form.toggler', 'subscription', 'COM_EASYBLOG_COMPOSER_ALLOW_SUBSCRIPTION', $post->subscription); ?>
			<?php echo $this->html('composer.field', 'form.toggler', 'send_notification_emails', 'COM_EASYBLOG_COMPOSER_NOTIFY_SUBSCRIBERS', $post->send_notification_emails); ?>
		<?php } ?>
	</div>
</div>
<?php } else { ?>
	<input type="hidden" name="created" data-datetime value="<?php echo $post->getFormDateValue('created');?>" />
	<input type="hidden" name="publish_up" data-datetime value="<?php echo $post->getFormDateValue('publish_up'); ?>" />
	<input type="hidden" name="publish_down" data-datetime value="<?php echo $post->publish_down != EASYBLOG_NO_DATE ? $post->getFormDateValue('publish_down') : ''; ?>" />
	<input type="hidden" name="frontpage" value="<?php echo $post->frontpage ? 1 : 0;?>" />
<?php } ?>
