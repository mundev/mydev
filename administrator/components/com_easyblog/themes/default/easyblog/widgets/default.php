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
<div class="dash-activity">
	<div class="dash-activity-head">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_RECENT_ACTIVITIES');?></b>
	</div>

	<ul class="dash-activity-filter list-unstyled">
		<li>
			<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FILTERS');?>:</b>
		</li>
		<li class="active">
			<a href="#posts" id="posts-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAB_POSTS');?></a>
		</li>
		<li>
			<a href="#comments" role="tab" id="comments-tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAB_COMMENTS');?></a>
		</li>
		<li>
			<a href="#pending" role="tab" id="pending-tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAB_PENDING');?></a>
		</li>
		<li>
			<a href="#reactions" role="tab" id="reactions-tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_REACTIONS');?></a>
		</li>
	</ul>

	<div class="tab-content">
		<?php echo $this->output('admin/easyblog/widgets/posts'); ?>

		<?php echo $this->output('admin/easyblog/widgets/comments'); ?>

		<?php echo $this->output('admin/easyblog/widgets/pending'); ?>

		<?php echo $this->output('admin/easyblog/widgets/reactions'); ?>
	</div>
</div>
