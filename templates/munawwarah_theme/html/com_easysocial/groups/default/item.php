<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="si-group-item <?php echo $group->isFeatured() ? 'is-featured' : '';?>">
	<div class="si-group-item__avatar">
		<?php echo $this->html('card.avatar', $group); ?>
	</div>
	<div class="si-group-item__content">
		
		<div class="">
			<?php echo $this->html('card.title', $group->getTitle(), $group->getPermalink()); ?>
		</div>
		<?php if ($this->config->get('groups.layout.listingdesc')) { ?>
		<div class="si-group-item__desc">
			<?php if ($group->description) { ?>
				<?php echo $this->html('string.truncate', $group->getDescription(), 200, '', false, false, false, true);?>
			<?php } else { ?>
				<?php echo JText::_('COM_EASYSOCIAL_GROUPS_NO_DESCRIPTION_YET'); ?>
			<?php }?>
		</div>
		<?php } ?>
		<div class="">
			<ol class="g-list-inline g-list-inline--delimited">
				<li>
					<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $group->getCategory()->getFilterPermalink();?>"><?php echo $group->getCategory()->getTitle();?></a>
				</li>
				
				<li>
					<?php echo $this->html('group.type', $group, 'top'); ?>
				</li>
				
				<?php if (isset($showDistance) && $showDistance && isset($group->distance)) { ?>
				<li>
					<i class="fa fa-compass"></i> <?php echo JText::sprintf('COM_ES_DISTANCE_AWAY', $group->distance, $this->config->get('general.location.proximity.unit', 'mile')); ?>
				</li>
				<?php } ?>

				<li>
					<a href="<?php echo $group->getAppPermalink('members');?>" data-es-provide="tooltip"
						data-original-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS', $group->getTotalMembers()), $group->getTotalMembers() ); ?>"
					>
						<i class="fa fa-users"></i>&nbsp; <span data-group-join-count-<?php echo $group->id; ?> ><?php echo $group->getTotalMembers();?></span>
					</a>
				</li>
			</ol>
		</div>
	</div>
	<div class="si-group-item__action">
		<?php echo $this->html('card.icon', 'featured', 'COM_EASYSOCIAL_GROUPS_FEATURED_GROUPS'); ?>

		<?php echo $this->html('group.action', $group); ?>

		<?php if ($group->canAccessActionMenu()) { ?>
			<div class="pull-right dropdown_">
				<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
					<i class="fa fa-ellipsis-h"></i>
				</a>

				<ul class="dropdown-menu">
					<?php echo $this->html('group.adminActions', $group); ?>

					<?php if ($this->html('group.report', $group)) { ?>
					<li>
						<?php echo $this->html('group.report', $group); ?>
					</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>

		
	</div>
</div>
