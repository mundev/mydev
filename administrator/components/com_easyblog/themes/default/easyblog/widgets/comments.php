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
<div role="tabpanel" class="tab-pane" id="comments" aria-labelledby="comments-tab">
	<div class="dash-stream dash-stream-graph">
		<div data-chart-comments style="height: 200px; width: 100%;"></div>
		<div data-chart-comments-legend></div>
	</div>

	<?php if ($comments) { ?>
		<?php foreach ($comments as $comment) { ?>
		<div class="dash-stream">
			<div class="dash-stream-content">
				<div class="dash-stream-headline pull-left">
					<b><a><?php echo $comment->getAuthorName();?></a></b>
					<?php echo JText::_('COM_EASYBLOG_DASHBOARD_POSTED_COMMENT_IN'); ?>
					<b>
						<a><?php echo $comment->getBlog()->title;?></a>
					</b>
				</div>
				<div class="dash-stream-time pull-right">
					<i class="fa fa-clock-o"></i>&nbsp; <?php echo $this->html('string.date', $comment->created, JText::_('Y-m-d H:i'));?>
				</div>

				<div class="dash-stream-clip">
					<?php echo $comment->getContent();?>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } else { ?>
	<div class="dash-stream empty">
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_COMMENTS_YET');?>
	</div>
	<?php } ?>
</div>