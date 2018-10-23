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
<div role="tabpanel" class="tab-pane" id="pending" aria-labelledby="pending-tab">
<?php if ($pending) { ?>
	<?php foreach ($pending as $post) { ?>
		<div class="dash-activity-pending">
			<div class="dash-stream">
				<div class="dash-stream-content">
					<div class="dash-stream-headline pull-left">
						<b><?php echo $post->getAuthor()->getName();?></b>
						<?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_SUBMITTED_FOR_REVIEW');?>
					</div>
					
					<div class="dash-stream-time pull-right">
						<i class="fa fa-clock-o"></i>&nbsp; <?php echo $this->html('string.date', $post->created, JText::_('Y-m-d H:i'));?>
					</div>

					<div class="dash-stream-clip">
						<a href="<?php echo $post->getEditLink();?>" class="dash-stream-post-title"><?php echo $post->title;?></a>
						<div class="dash-stream-post-meta mt-5 mb-5">
							<a href="<?php echo $post->getPrimaryCategory()->getPermalink();?>"><?php echo $post->getPrimaryCategory()->getTitle();?></a>
						</div>
						<div class="dash-stream-post-content">
							<?php echo EBString::substr($post->getIntro(EASYBLOG_STRIP_TAGS), 0, 250) . JText::_('COM_EASYBLOG_ELLIPSES');?>
						</div>
					</div>

					<div class="dash-stream-actions">
						<div>
							<a href="javascript:void(0);" class="btn btn-primary btn-xs" data-id="<?php echo $post->id;?>" data-approve-post><?php echo JText::_('COM_EASYBLOG_APPROVE_POST');?></a>

							<a href="javascript:void(0);" class="btn btn-danger btn-xs" data-id="<?php echo $post->id;?>" data-reject-post><?php echo JText::_('COM_EASYBLOG_REJECT_POST');?></a>
							
							<a href="<?php echo $post->getPreviewLink();?>" class="btn btn-default btn-xs" target="_blank"><?php echo JText::_('COM_EASYBLOG_PREVIEW_BUTTON');?></a>

						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } else { ?>
	<div class="dash-stream empty">
		<?php echo JText::_('COM_EASYBLOG_NO_PENDING_POSTS_CURRENTLY'); ?>
	</div>
<?php } ?>
</div>