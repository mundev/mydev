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
<div role="tabpanel" class="tab-pane in active" id="posts" aria-labelledby="posts-tab">
	<div class="dash-stream dash-stream-graph">
		<div  data-chart-posts style="height: 200px; width: 100%;"></div>
		<div data-chart-posts-legend></div>
	</div>

	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>
		<div class="dash-stream">
			<div class="dash-stream-content">
				<div class="dash-stream-headline pull-left">
					<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->uid . '.' . $post->revision_id));?>" class="dash-stream-post-title"><?php echo $post->title;?></a>
				</div>
				<div class="dash-stream-time pull-right">
					<span>
						<i class="fa fa-user"></i>&nbsp; <?php echo $post->getAuthor()->getName();?>
					</span>
					<span class="ml-10">
						<i class="fa fa-clock-o"></i>&nbsp; <?php echo $this->html('string.date', $post->created, JText::_('Y-m-d H:i'));?>
					</span>
				</div>
			</div>
		</div>
		<?php } ?>
	<?php } else { ?>
	<div class="dash-stream empty">
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_POSTS_YET');?>
	</div>
	<?php } ?>
</div>