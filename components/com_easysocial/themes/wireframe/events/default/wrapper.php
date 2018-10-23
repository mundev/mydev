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
<?php if (!empty($activeCategory)) { ?>
<div class="t-lg-mb--xl">
	<?php echo $this->html('miniheader.eventCategory', $activeCategory); ?>
</div>
<?php } ?>

<?php if ($showDateNavigation) { ?>
<div class="btn-group btn-group-justified es-btn-group-date t-lg-mb--xl">
	<a href="<?php echo ESR::events(array('filter' => 'date', 'date' => $navigation->previous));?>" class="btn btn-es-default-o btn-sm" data-navigation-date="<?php echo $navigation->previous;?>">
		<i class="fa fa-chevron-left"></i>
	</a>

	<span class="btn btn-es-default-o btn-sm"><b>
		<?php if ($activeDateFilter == 'today') { ?>
			<?php echo JText::_('COM_EASYSOCIAL_EVENTS_TODAY'); ?> (<?php echo $activeDate->format(JText::_('COM_EASYSOCIAL_DATE_DMY')); ?>)
		<?php } ?>

		<?php if ($activeDateFilter == 'tomorrow') { ?>
			<?php echo JText::_('COM_EASYSOCIAL_EVENTS_TOMORROW'); ?> (<?php echo $activeDate->format(JText::_('COM_EASYSOCIAL_DATE_DMY')); ?>)
		<?php } ?>

		<?php if ($activeDateFilter == 'month') { ?>
			<?php echo $activeDate->format(JText::_('COM_EASYSOCIAL_DATE_MY')); ?>
		<?php } ?>

		<?php if ($activeDateFilter == 'year') { ?>
			<?php echo $activeDate->format(JText::_('COM_EASYSOCIAL_DATE_Y')); ?>
		<?php } ?>

		<?php if ($activeDateFilter == 'normal') { ?>
			<?php echo $activeDate->format(JText::_('COM_EASYSOCIAL_DATE_DMY')); ?>
		<?php } ?>
		</b>
	</span>

	<a href="<?php echo ESR::events(array('filter' => 'date', 'date' => $navigation->next));?>" class="btn btn-es-default-o btn-sm" data-navigation-date="<?php echo $navigation->next;?>">
		<i class="fa fa-chevron-right"></i>
	</a>
</div>
<?php } ?>

<?php if (!empty($featuredEvents)) { ?>

	<?php echo $this->html('html.snackbar', 'COM_EASYSOCIAL_PAGE_TITLE_EVENTS_FILTER_FEATURED'); ?>

	<div class="es-cards es-cards--2">
		<?php foreach($featuredEvents as $event){ ?>
			<?php echo $this->loadTemplate('site/events/default/item', array('event' => $event, 'showDistance' => $showDistance, 'isGroupOwner' => isset($isGroupOwner) ? $isGroupOwner : false, 'browseView' => $browseView)); ?>
		<?php } ?>
	</div>
<?php } ?>

<div>
	<?php if (!$delayed && ($showPastFilter || $showSorting || $showDistanceSorting)) { ?>
	<div class="o-grid">

		<?php if ($showPastFilter || $showDistanceSorting) { ?>
			<div class="o-grid__cell">
				<div class="o-form-inline">
					<?php if ($showDistanceSorting) { ?>
					<div class="o-form-group">
						<?php
							$sortOptions = array();
							$sortOptions[] = $this->html('form.popdownOption', '10', '10 ' . $distanceUnit, '', false, array('data-radius="10"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '25', '25 ' . $distanceUnit, '', false, array('data-radius="25"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '50', '50 ' . $distanceUnit, '', false, array('data-radius="50"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '100', '100 ' . $distanceUnit, '', false, array('data-radius="100"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '200', '200 ' . $distanceUnit, '', false, array('data-radius="200"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '300', '300 ' . $distanceUnit, '', false, array('data-radius="300"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '400', '400 ' . $distanceUnit, '', false, array('data-radius="400"'), '');
							$sortOptions[] = $this->html('form.popdownOption', '500', '500 ' . $distanceUnit, '', false, array('data-radius="500"'), '');
						?>
						<div class="o-grid__cell">
							<div class="es-list-sorting">
								<?php echo $this->html('form.popdown', 'radius', $distance, $sortOptions, 'left'); ?>
							</div>
						</div>
					</div>
					<?php } ?>

					<?php if ($showPastFilter) { ?>
					<div class="form-group">
						<div class="o-checkbox">
							<input type="checkbox" id="es-show-past-event" class="t-lg-pull-right" <?php echo $includePast ? 'checked="checked"' : '';?> data-events-past>
							<label for="es-show-past-event">
								<a href="<?php echo $sortingUrls[$ordering][$includePast ? 'nopast' : 'past']; ?>"
									data-start-past="<?php echo $sortingUrls['start']['past']; ?>"
									data-start-nopast="<?php echo $sortingUrls['start']['nopast']; ?>"

									<?php if ($showSorting) { ?>
									data-created-past="<?php echo $sortingUrls['created']['past']; ?>"
									data-created-nopast="<?php echo $sortingUrls['created']['nopast']; ?>"
									<?php } ?>

									<?php if ($showDistanceSorting) { ?>
									data-distance-past="<?php echo $sortingUrls['distance']['past']; ?>"
									data-distance-nopast="<?php echo $sortingUrls['distance']['nopast']; ?>"
									<?php } ?>

									data-include-past-link>
									<?php echo JText::_('COM_EASYSOCIAL_EVENTS_INCLUDE_PAST_EVENTS'); ?>
								</a>
							</label>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>


		<?php if ($showSorting || $showDistanceSorting) { ?>
			<?php

				$sortOptions = array();

				$url = $sortingUrls['start'][$showPastFilter && $includePast ? 'past' : 'nopast'];
				$sortOptions[] = $this->html('form.popdownOption', 'start', 'COM_ES_SORT_NEAREST_DATE', '', false, array('data-ordering="start"'), $url);

				if ($showSorting) {
					$url = $sortingUrls['created'][$showPastFilter && $includePast ? 'past' : 'nopast'];
					$sortOptions[] = $this->html('form.popdownOption', 'created', 'COM_ES_SORT_BY_RECENTLY_ADDED', '', false, array('data-ordering="created"'), $url);
				}

				if ($showDistanceSorting) {
					$url = $sortingUrls['distance'][$showPastFilter && $includePast ? 'past' : 'nopast'];
					$filterAtt = 'data-filter="' . $activeCategory ? 'category' : $filter . '"';
					$catAtt = 'data-categoryid="' . isset($activeCategory) && $activeCategory ? $activeCategory->id : '' . '"';
					$sortOptions[] = $this->html('form.popdownOption', 'distance', 'COM_EASYSOCIAL_EVENTS_SORTING_EVENT_DISTANCE', '', false, array('data-ordering="distance"', $filterAtt, $catAtt), $url);
				}
			?>
			<div class="o-grid__cell">
				<div class="es-list-sorting">
					<?php echo $this->html('form.popdown', 'ordering', $ordering, $sortOptions); ?>
				</div>
			</div>

		<?php } ?>
	</div>
	<?php } ?>

	<?php if (!$delayed && !empty($events)) { ?>
		<?php echo $this->html('html.snackbar', $title); ?>
	<?php } ?>

	<div class="es-list-result" data-sub-wrapper>

		<?php echo $this->html('html.loading'); ?>

		<div data-events-list>
			<?php echo $this->includeTemplate('site/events/default/items'); ?>
		</div>
	</div>
</div>
