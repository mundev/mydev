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
<div class="eb-box">
	<?php echo $this->html('dashboard.miniHeading', 'COM_EASYBLOG_DASHBOARD_BLOGGER_SETTINGS', 'fa fa-cube'); ?>

	<div class="eb-box-body">
		<div class="form-horizontal">
			<?php if ($this->acl->get('allow_user_blog_title')) { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_TITLE');?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', 'title', $this->escape($profile->title)); ?>
				</div>
			</div>
			<?php } ?>
			<?php if ($this->acl->get('allow_user_blog_description')) { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_BLOGGER_BLOG_DESC');?>

				<div class="col-md-8">
					<textarea name="description" class="form-control"><?php echo $profile->getDescription();?></textarea>
				</div>
			</div>
			<?php } ?>
			<?php if ($this->acl->get('custom_css')) { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_BLOGGER_CUSTOM_CSS');?>

				<div class="col-md-8">
					<textarea name="custom_css" class="hide" data-custom-css></textarea>
					<div class="form-control" id="customcss"><?php echo $profile->custom_css;?></div>
				</div>
			</div>
			<?php } ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_BLOGGER_BIOGRAPHICAL_INFO');?>

				<div class="col-md-8">
					<?php echo $editor->display('biography', $profile->getBiography(), '300', '300', '10', '10', array('image','readmore', 'pagebreak', 'jcommentsoff', 'jcommentson', 'article')); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_BLOGGER_WEBSITE');?>

				<div class="col-md-8">
					<?php echo $this->html('dashboard.text', 'url', $this->escape($profile->url)); ?>
				</div>
			</div>
			<?php if ($this->acl->get('allow_user_permalink', 1)) { ?>
			<div class="form-group">
				<?php echo $this->html('dashboard.label', 'COM_EASYBLOG_DASHBOARD_BLOGGER_PERMALINK');?>

				<div class="col-md-8">
					<?php if (JPluginHelper::isEnabled('system', 'blogurl')) { ?>
						<span style="line-height: 28px;"><?php echo JURI::root(); ?></span>
					<?php } ?>
					<input type="text" id="user_permalink" name="user_permalink" class="form-control" value="<?php echo $this->escape($profile->permalink); ?>" />
					<div class="small"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_NOTICE_PERMALINK_USAGE')?></div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>