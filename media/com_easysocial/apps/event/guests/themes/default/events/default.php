<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container app-members app-events" data-es-container data-es-event-guests data-id="<?php echo $event->id; ?>" data-return="<?php echo $returnUrl;?>">
	<div class="es-sidebar">
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					
					<li class="o-tabs__item has-notice <?php echo $active == 'going' ? ' active' : '';?>" data-filter data-type="going">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_EVENT_GUESTS_FILTER_GOING');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['going'];?></div>
					</li>

					<?php if ($event->getParams()->get('allowmaybe', true)) { ?>
					<li class="o-tabs__item has-notice <?php echo $active == 'maybe' ? ' active' : '';?>" data-filter data-type="maybe">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_EVENT_GUESTS_FILTER_MAYBE');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['maybe'];?></div>
					</li>
					<?php } ?>

					<?php if ($event->getParams()->get('allownotgoingguest', true)) { ?>
					<li class="o-tabs__item has-notice <?php echo $active == 'notgoing' ? ' active' : '';?>" data-filter data-type="notgoing">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_EVENT_GUESTS_FILTER_NOTGOING');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['notgoing'];?></div>
					</li>
					<?php } ?>

					<li class="o-tabs__item has-notice <?php echo $active == 'admin' ? ' active' : '';?>" data-filter data-type="admin">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_EVENT_GUESTS_FILTER_ADMINS');?>
						</a>
						<div class="o-tabs__bubble" data-counter><?php echo $counters['admins'];?></div>
					</li>

					<?php if ($event->isClosed() && ($event->isAdmin() || $event->isOwner())) { ?>
					<li class="o-tabs__item <?php echo $active == 'pending' ? ' active' : '';?> <?php echo $counters['pending'] ? 'has-notice' : '';?>" data-filter data-type="pending">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_EVENT_GUESTS_FILTER_PENDING');?>
							<div class="o-tabs__bubble" data-counter><?php echo $counters['pending'];?></div>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="app-contents-wrap">

			<div class="o-input-group">
				<input type="text" class="o-form-control" data-search-input placeholder="<?php echo JText::_('APP_EVENT_GUESTS_SEARCH_GUESTS'); ?>" />
			</div>

			<div class="t-lg-mt--xl" data-wrapper>
				<?php echo $this->html('html.loading'); ?>

				<div data-result>
					<?php echo $this->includeTemplate('apps/event/guests/events/list'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
