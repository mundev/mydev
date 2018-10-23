<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="si-group-item <?php echo $event->isFeatured() ? 'is-featured' : ''; ?>">
	<div class="si-group-item__avatar">
		<?php echo $this->html('card.avatar', $event); ?>
	</div>
	<div class="si-group-item__content">
		<div class="">
			<?php echo $this->html('card.title', $event->getTitle(), $event->getPermalink()); ?>
		</div>
		<?php if ($this->config->get('events.layout.listingdesc')) { ?>
			<div class="es-card__meta">
				<?php if ($event->description) { ?>
					<?php echo $this->html('string.truncate', $event->getDescription(), 200, '', false, false, false, true);?>
				<?php } else { ?>
					<?php echo JText::_('COM_EASYSOCIAL_GROUPS_NO_DESCRIPTION_YET'); ?>
				<?php }?>
			</div>
		<?php } ?>

		<div class="">
			<ol class="g-list-inline g-list-inline--delimited">
				<li>
					<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $event->getCategory()->getFilterPermalink();?>"><?php echo $event->getCategory()->getTitle();?></a>
				</li>

				<li>
					<?php echo $this->html('event.type', $event, 'top'); ?>
				</li>

				<?php if ($this->config->get('events.layout.seatsleft', true) && $event->seatsLeft() >= 0) { ?>
				<li>
					<a href="<?php echo $event->getAppPermalink('guests');?>"
						data-es-provide="tooltip"
						data-original-title="<?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_REMAINING_SEATS_SHORT', $event->seatsLeft()); ?>"
					>
						<i class="fa fa-ticket"></i>&nbsp; <?php echo $event->seatsLeft();?> / <?php echo $event->getTotalSeats();?>
					</a>
				</li>
				<?php } else { ?>
				<li>
					<a href="<?php echo $event->getAppPermalink('guests');?>" 
						data-es-provide="tooltip"
						data-original-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_GUESTS', $event->getTotalGoing()), $event->getTotalGoing()); ?>"
					>
						<i class="fa fa-users"></i>&nbsp; <?php echo $event->getTotalGoing();?>
					</a>
				</li>
				<?php } ?>
			</ol>
		</div>
	</div>
	<div class="si-group-item__action">
		<?php if ($event->isPassed()) { ?>
			<?php echo $this->html('card.icon', 'passed', 'COM_EASYSOCIAL_EVENTS_PAST_EVENT'); ?>
		<?php } else { ?>
			<?php echo $this->html('card.icon', 'featured', 'COM_EASYSOCIAL_EVENTS_FEATURED_EVENT'); ?>
		<?php } ?>

		<?php if (!$event->isPassed()) { ?>
			<?php echo $this->html('event.action', $event); ?>
		<?php } ?>

		<?php if ($event->canAccessActionMenu()) { ?>
			<div class="pull-right dropdown_">
				<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
					<i class="fa fa-ellipsis-h"></i>
				</a>
				<ul class="dropdown-menu">
					<?php echo $this->html('event.adminActions', $event); ?>

					<?php if ($this->html('event.report', $event)) { ?>
					<li>
						<?php echo $this->html('event.report', $event); ?>
					</li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
	</div>
</div>