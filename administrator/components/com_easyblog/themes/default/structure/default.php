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
<div id="eb" class="eb-component eb-admin <?php echo $prefix;?>">

	<?php if ($this->config->get('show_outdated_message')) { ?>
	<div class="app-alert alert alert-danger hidden" style="margin-bottom: 0;padding: 15px 24px;font-size: 12px;" data-outdated-banner>
		<div class="row-table">
			<div class="col-cell cell-tight">
				<i class="fa fa-bolt"></i>
			</div>
			<div class="col-cell pl-10 pr-10"><?php echo JText::_('COM_EASYBLOG_OUTDATED_VERSION');?></div>
			<div class="col-cell cell-tight">
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&task=system.upgrade');?>" class="btn btn-danger">
					<b><?php echo JText::_('COM_EASYBLOG_UPDATE_NOW');?></b>
				</a>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($fbTokenExpiring) { ?>
	<div class="app-alert alert alert-info" style="margin-bottom: 0;padding: 15px 24px;font-size: 12px;" data-outdated-banner>
		<div class="row-table">
			<div class="col-cell cell-tight">
				<i class="fa fa-facebook"></i>
			</div>
			<div class="col-cell pl-10 pr-10"><?php echo JText::_('COM_EASYBLOG_FACEBOOK_TOKEN_EXPIRING');?></div>
			<div class="col-cell cell-tight">
				<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=autoposting&layout=facebook');?>" class="btn btn-default">
					<b><i class="fa fa-facebook"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_FACEBOOK_RENEW_TOKEN');?></b>
				</a>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if ($tmpl != 'component') { ?>
	<div class="app">
		<?php echo $sidebar; ?>

		<div class="app-content">
			<?php if ($heading || $desc) { ?>
			<div class="app-head">
				<h2><?php echo JText::_($heading); ?></h2>
				<p><?php echo JText::_($desc); ?></p>
			</div>
			<?php } ?>

			<?php echo $info->html();?>

			<div class="app-body">
				<?php echo $output; ?>
			</div>
		</div>
	</div>

	<div class="btn-float-wrap">
		<a href="<?php echo JURI::base();?>index.php?option=com_easyblog&view=categories&layout=form" class="btn-float btn-float--category">
			<i class="fa fa-folder-open-o"></i>
			<span><?php echo JText::_('COM_EASYBLOG_NEW_CATEGORY');?></span>
		</a>
		<a href="<?php echo EB::composer()->getComposeUrl(); ?>" class="btn-float btn-float--post">
			<i class="fa fa-file-text-o"></i>
			<span><?php echo JText::_('COM_EASYBLOG_NEW_POST');?></span>
		</a>
		<a href="#" class="btn-float btn-float--default">
			<i class="fa fa-plus"></i>
		</a>
	</div>
	<?php } else { ?>
		<?php echo $output; ?>
	<?php } ?>

	<?php if ($jscripts) { ?>
	<div data-eb-scripts>
		<?php echo $jscripts;?>
	</div>
	<?php } ?>
</div>
<script>
EasyBlog.ready(function($){
	// inject is-joomla-xx class into body
	$(document).ready(function(){
		var joomlaClass = "<?php echo EB::isJoomla40() ? 'is-joomla-4' : 'is-joomla-3'; ?>";
		$('body').addClass('com_easyblog ' + joomlaClass);
	});
});
</script>
