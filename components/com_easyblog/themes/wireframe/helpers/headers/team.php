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
<div class="eb-authors-head">
	<?php if ($this->config->get('layout_teamavatar', true)) { ?>
	<div class="col-cell cell-tight">
		<a class="eb-avatar" href="<?php echo $team->getPermalink();?>">
			<img src="<?php echo $team->getAvatar(); ?>" class="eb-authors-avatar" width="40" height="40" alt="<?php echo $team->title;?>" />
		</a>
	</div>
	<?php } ?>

	<div class="col-cell">
		<div class="">
			<h2 class="eb-authors-name reset-heading">
				<a href="<?php echo $team->getPermalink();?>" class="text-inherit"><?php echo $team->title;?></a>
				<small class="eb-authors-featured eb-star-featured<?php echo !$team->isFeatured ? ' hide' : '';?>" data-featured-tag 
					data-eb-provide="tooltip" title="<?php echo JText::_('COM_EASYBLOG_FEATURED_BLOGGER_FEATURED', true);?>"
				>
					<i class="fa fa-star"></i>
				</small>
			</h2>
		</div>

		<div class="eb-authors-subscribe spans-separator">
			<?php if (($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EB::isSiteAdmin()) && $this->config->get('main_teamsubscription')) { ?>
			<span>
				<a href="javascript:void(0);" class="<?php echo $team->isTeamSubscribed ? 'hide' : ''; ?>" 
					data-blog-subscribe data-type="team" data-id="<?php echo $team->id; ?>"
					data-eb-provide="tooltip" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_TEAM', true);?>"
				>
					<i class="fa fa-envelope"></i>
				</a>
				<a href="javascript:void(0);" class="<?php echo $team->isTeamSubscribed ? '' : 'hide'; ?>" 
					data-blog-unsubscribe data-subscription-id="<?php echo $team->subscription_id; ?>"
					data-eb-provide="tooltip" title="<?php echo JText::_('COM_EASYBLOG_UNSUBSCRIBE_TEAM', true);?>"
				>
					<i class="fa fa-envelope"></i>
				</a>
			</span>
			<?php } ?>

			<?php if (($team->access != EBLOG_TEAMBLOG_ACCESS_MEMBER || $team->isMember || EB::isSiteAdmin() ) && ($this->config->get('main_rss'))) { ?>
			<span>
				<a class="link-rss" href="<?php echo $team->getRssLink();?>" data-eb-provide="tooltip" title="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_FEEDS', true); ?>">
					<i class="fa fa-rss"></i>
				</a>
			</span>
			<?php } ?>

			<?php if ($team->canJoin()) { ?>
			<span>
				<a class="link-jointeam" href="javascript:void(0);" data-team-join data-id="<?php echo $team->id;?>"
					data-eb-provide="tooltip" title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_JOIN_TEAM', true);?>"
				>
					<i class="fa fa-user-plus"></i>
				</a>
			</span>
			<?php } else if ($team->isActualMember()) { ?>
			<span>
				<a class="link-jointeam" href="javascript:void(0);" data-team-leave data-id="<?php echo $team->id;?>"
					data-eb-provide="tooltip" title="<?php echo JText::_('COM_EASYBLOG_TEAMBLOG_LEAVE_TEAM', true);?>"
				>
					<i class="fa fa-user-times"></i>
				</a>
			</span>
			<?php } ?>
		</div>
	</div>
	
	<?php if (EB::isSiteAdmin()) { ?>
	<div class="col-cell cell-tight text-right">
		<a href="javascript:void(0);" class="btn btn-sm btn-default<?php echo !$team->isFeatured ? ' hide' : '';?>" data-team-unfeature data-id="<?php echo $team->id;?>">
			<i class="fa fa-star-o"></i>&nbsp; <?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_REMOVE_TEAM'); ?>
		</a>
		<a href="javascript:void(0);" class="btn btn-sm btn-default<?php echo $team->isFeatured ? ' hide' : '';?>" data-team-feature data-id="<?php echo $team->id;?>">
			<i class="fa fa-star"></i>&nbsp; <?php echo Jtext::_('COM_EASYBLOG_FEATURED_FEATURE_THIS_TEAM'); ?>
		</a>
	</div>
	<?php } ?>
</div>

<?php if (!empty($team->description) && $this->config->get('main_includeteamblogdescription')) { ?>
<div class="eb-authors-bio">
	<?php echo $this->html('string.truncater', nl2br(strip_tags($team->description)), 350); ?>
</div>
<?php } ?>
