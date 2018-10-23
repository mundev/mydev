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
<div class="es-profile" data-es-event data-id="<?php echo $event->id;?>">

	<?php echo $this->html('cover.event', $event, $layout); ?>

	<?php echo $this->html('responsive.toggle'); ?>

	<div class="es-container" data-es-container>
		<div class="es-sidebar" data-sidebar>
			<?php echo $this->render('module', 'es-events-item-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>
			<?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'events', 'sidebarTop', array('uid' => $event->id, 'event' => $event)); ?>

			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_EVENTS_INTRO'); ?>

				<div class="es-side-widget__bd">

					<?php if (!$this->isMobile() && $this->config->get('events.layout.description')) { ?>
						<?php echo $this->html('string.truncate', $event->getDescription(), 120, '', false, false, false, true);?>
						<a href="<?php echo $aboutPermalink;?>"><?php echo JText::_('COM_EASYSOCIAL_READMORE');?></a>
					<?php } ?>

					<ul class="o-nav o-nav--stacked t-lg-mt--sm">
						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<a href="<?php echo $event->getCreator()->getPermalink();?>" class="o-nav__link t-text--muted">
								<i class="fa fa-user"></i>&nbsp;
								<?php echo $event->getCreator()->getName();?>
							</a>
						</li>

						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<a href="<?php echo $event->getAppPermalink('guests');?>" class="o-nav__link t-text--muted">
								<i class="fa fa-users"></i>&nbsp;
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_GUESTS', $event->getTotalGoing()), $event->getTotalGoing()); ?>
							</a>
						</li>

						<?php if ($this->config->get('events.layout.address') && $event->address) { ?>
						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<i class="fa fa-map-marker"></i>&nbsp; <a href="<?php echo $event->getAddressLink(); ?>" target="_blank"><?php echo $event->address;?></a>
						</li>
						<?php } ?>

						<?php if ($this->config->get('events.layout.seatsleft') && $event->seatsLeft() > 0) { ?>
						<li class="o-nav__item t-text--muted t-lg-mb--sm">
							<i class="fa fa-ticket"></i>&nbsp; <?php echo JText::sprintf('COM_EASYSOCIAL_EVENTS_SEATS_REMAINING', '<b>' . $event->seatsLeft() . '</b>', '<b>' . $event->getTotalSeats() . '</b>'); ?>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php if (!$displayFeedsFilter) { ?>
					<input type="hidden" class="active" data-filter-item="feeds" data-type="feeds" data-id="<?php echo $event->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>" />
				<?php } ?>
			</div>

			<?php if ($displayFeedsFilter) { ?>
			<div class="es-side-widget">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_NEWS_FEED', $customFilterActions); ?>
				<div class="es-side-widget__bd">
					<ol class="o-tabs o-tabs--stacked">
						<li class="o-tabs__item <?php echo !$contents && !$filterId && empty($hashtag) ? 'active' : '';?>"
							data-filter-item="feeds" data-type="feeds" data-id="<?php echo $event->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
							<a href="<?php echo $event->getPermalink(false, false, 'item', true, array('page' => 'timeline')); ?>" class="o-tabs__link <?php echo !$contents ? 'active' : '';?>"
								title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_MOST_RECENT_ITEMS'); ?>">
								<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SIDEBAR_MOST_RECENT_ITEMS'); ?>
								<div class="o-tabs__bubble" data-counter>0</div>
							</a>
						</li>

						<?php if (isset($hashtag) && $hashtag) { ?>
							<li class="o-tabs__item active" style="display:none;" data-filter-item="hashtag" data-type="hashtag" data-tag="<?php echo $hashtag ?>" data-id="<?php echo $event->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<div class="o-loader o-loader--sm"></div>
							</li>
						<?php } ?>

						<?php if ($filters && count($filters) > 0) { ?>

							<?php foreach ($filters as $filter) { ?>
							<li class="o-tabs__item <?php echo $filterId == $filter->id ? ' active' : '';?>" class="o-tabs__item" data-filter-item="filters" data-type="filters" data-id="<?php echo $filter->id; ?>" data-stream-identifier="<?php echo $stream->getIdentifier(); ?>">
								<a href="<?php echo $event->getPermalink(false, false, 'item', true, array('filterId' => $filter->getAlias())); ?>" class="o-tabs__link">
									<?php echo $filter->_('title'); ?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ol>
				</div>
			</div>
			<?php } ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'events', 'sidebarMiddle', array('uid' => $event->id, 'event' => $event)); ?>

			<?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'events', 'sidebarBottom', array('uid' => $event->id, 'event' => $event)); ?>

			<?php echo $this->render('module', 'es-events-item-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content" data-wrapper>
			<?php echo $this->html('html.loading'); ?>
			<?php echo $this->render('module', 'es-events-before-contents'); ?>

			<div data-contents>
				<?php if ($contents) { ?>
					<?php echo $contents; ?>
				<?php } ?>

				<?php if (!$contents) { ?>
				<?php echo $this->includeTemplate('site/events/item/feeds'); ?>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-events-after-contents'); ?>
		</div>
	</div>
</div>
