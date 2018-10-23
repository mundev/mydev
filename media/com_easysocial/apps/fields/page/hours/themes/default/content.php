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
<div class="o-control-input">
	<div class="o-radio">
		<input type="radio" name="<?php echo $inputName;?>[hours_type];?>" id="hours-always" value="always" data-hours-type <?php echo $data->hours_type == 'always' ? 'checked' : '';?> />
		<label for="hours-always"><?php echo JText::_('APP_FIELD_PAGES_HOURS_ALWAYS_OPEN');?></label>
	</div>
	<div class="o-radio">
		<input type="radio" name="<?php echo $inputName;?>[hours_type];?>" id="hours-selected" value="selected" data-hours-type <?php echo $data->hours_type == 'selected' ? 'checked' : '';?> />
		<label for="hours-selected"><?php echo JText::_('APP_FIELD_PAGES_HOURS_SELECTED_HOURS');?></label>
	</div>
	<div class="o-radio">
		<input type="radio" name="<?php echo $inputName;?>[hours_type];?>" id="hours-disabled" value="disabled" data-hours-type <?php echo $data->hours_type == 'disabled' ? 'checked' : '';?> />
		<label for="hours-disabled"><?php echo JText::_('APP_FIELD_PAGES_HOURS_NOT_AVAILABLE');?></label>
	</div>
</div>

<div class="o-control-input t-lg-mt--xl <?php echo in_array($data->hours_type, array('always', 'disabled')) ? 't-hidden' : '';?>" data-hours-selection>
	<div class="<?php echo $timeFormat == 1 ? 'es-form-working-hour-wrap' : 'es-form-working-hour es-form-working-hour--24'; ?>">
		<div>
			<b><?php echo JText::_('APP_FIELD_PAGES_HOURS_WORKING_DAYS');?>:</b>
		</div>

		<?php foreach ($days as $day) { ?>

			<div class="es-form-working-hour__day"
				data-hours-day-wrapper data-hours-format="<?php echo $timeFormat; ?>">
				<div class="o-checkbox">
					<input type="checkbox" id="<?php echo $day->id;?>" name="<?php echo $inputName;?>[days][]" <?php echo in_array($day->value, array_keys($data->days)) ? 'checked="checked"' : '';?>
						value="<?php echo $day->value;?>" data-hours-day />
					<label for="<?php echo $day->id;?>"><?php echo $day->title;?></label>
				</div>

				<?php
					$hasDay = in_array($day->value, array_keys($data->days));

					// general data required in item theme
					$itemData = array('inputName' => $inputName, 'hasDay' => $hasDay, 'day' => $day, 'timeFormat' => $timeFormat, 'allowMultiple' => $allowMultiple);
				?>

				<?php if ($hasDay && $data->days[$day->value] && is_array($data->days[$day->value]->start_hour)) { ?>
					<?php
						$dataCount = count($data->days[$day->value]->start_hour);
					?>

					<?php if ($dataCount > 1) { ?>
						<?php for ($i=0; $i < $dataCount; $i++) { ?>
							<?php
								$startHour = $data->days[$day->value]->start_hour[$i];
								$startPeriod = isset($data->days[$day->value]->start_period) ? $data->days[$day->value]->start_period[$i] : '';
								$endHour = $data->days[$day->value]->end_hour[$i];
								$endPeriod = isset($data->days[$day->value]->end_period) ? $data->days[$day->value]->end_period[$i] : '';

								$startData = array('hour' => $startHour, 'period' => $startPeriod);
								$endData = array('hour' => $endHour, 'period' => $endPeriod);

								$itemData['index'] = $i;
								$itemData['start'] = $startData;
								$itemData['end'] = $endData;
							?>
							<?php echo $this->output('fields/page/hours/item', $itemData); ?>
						<?php } ?>
					<?php } else { ?>
						<?php
							$startHour = $data->days[$day->value]->start_hour[0];
							$startPeriod = isset($data->days[$day->value]->start_period) ? $data->days[$day->value]->start_period[0] : '';
							$endHour = $data->days[$day->value]->end_hour[0];
							$endPeriod = isset($data->days[$day->value]->end_period) ? $data->days[$day->value]->end_period[0] : '';

							$startData = array('hour' => $startHour, 'period' => $startPeriod);
							$endData = array('hour' => $endHour, 'period' => $endPeriod);

							$itemData['index'] = 0;
							$itemData['start'] = $startData;
							$itemData['end'] = $endData;
						?>
						<?php echo $this->output('fields/page/hours/item', $itemData); ?>
					<?php } ?>

				<?php } else { ?>

					<?php
						$startHour = isset($data->days[$day->value]) ? $data->days[$day->value]->start_hour : '';
						$startPeriod = isset($data->days[$day->value]) && isset($data->days[$day->value]->start_period) ? $data->days[$day->value]->start_period : '';
						$endHour = isset($data->days[$day->value]) ? $data->days[$day->value]->end_hour : '';
						$endPeriod = isset($data->days[$day->value]) && isset($data->days[$day->value]->end_period) ? $data->days[$day->value]->end_period : '';

						$startData = array('hour' => $startHour, 'period' => $startPeriod);
						$endData = array('hour' => $endHour, 'period' => $endPeriod);

						$itemData['index'] = 0;
						$itemData['start'] = $startData;
						$itemData['end'] = $endData;
					?>

					<?php echo $this->output('fields/page/hours/item', $itemData); ?>
				<?php } ?>

			</div>

		<?php } ?>
	</div>
</div>
