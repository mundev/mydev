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

// timeFormat 1 == 12H
//			  2 == 24H

?>
<div class="es-form-working-hour__grid t-lg-pl--xl<?php echo !$hasDay ? ' t-hidden' : '';?>" data-hours-time>
	<div class="es-form-working-hour__cell">
		<?php echo $timeFormat == 1 ? '<div class="o-input-group t-lg-mt--sm">' : ''; ?>
			<input type="text" name="<?php echo $inputName;?>[<?php echo $day->id;?>][start_hour][]" class="o-form-control input-sm es-form-working-hour__time start-hour"
				placeholder="10:00" value="<?php echo $start ? $start['hour'] : '';?>" data-hours-start />
			<?php if ($timeFormat == '1') { ?>
			<div class="o-input-group__select">
				<div class="o-select-group">
					<select id="<?php echo $day->id;?>-start-period" name="<?php echo $inputName;?>[<?php echo $day->id;?>][start_period][]" class="o-form-control input-sm">
						<option value="am" <?php echo !$start || ($start && $start['period'] == 'am') ? 'selected="selected"' : '';?>>AM</option>
						<option value="pm" <?php echo $start && $start['period'] == 'pm' ? 'selected="selected"' : '';?>>PM</option>
					</select>
					<label for="<?php echo $day->id;?>-start-period" class="o-select-group__drop"></label>
				</div>
			</div>
			<?php } ?>
		<?php echo $timeFormat == 1 ? '</div>' : ''; ?>
	</div>

	<div class="es-form-working-hour__cell es-form-working-hour__cell--divider">&#8211;</div>

	<div class="es-form-working-hour__cell">
		<?php echo $timeFormat == 1 ? '<div class="o-input-group t-lg-mt--sm">' : ''; ?>
			<input type="text" name="<?php echo $inputName;?>[<?php echo $day->id;?>][end_hour][]" class="o-form-control input-sm es-form-working-hour__time end-hour"
				placeholder="7:00" value="<?php echo $end ? $end['hour'] : '';?>" data-hours-end />
			<?php if ($timeFormat == '1') { ?>
			<div class="o-input-group__select">
				<div class="o-select-group">
					<select id="<?php echo $day->id;?>-end-period" name="<?php echo $inputName;?>[<?php echo $day->id;?>][end_period][]" class="o-form-control input-sm">
						<option value="am" <?php echo $end && $end['period'] == 'am' ? 'selected="selected"' : '';?>>AM</option>
						<option value="pm" <?php echo (!$end || ($end && ($end['period'] == 'pm' || $end['period'] == ''))) ? 'selected="selected"' : '';?>>PM</option>
					</select>
					<label for="<?php echo $day->id;?>-end-period" class="o-select-group__drop"></label>
				</div>
			</div>
			<?php } ?>
		<?php echo $timeFormat == 1 ? '</div>' : ''; ?>
	</div>

	<?php if ($allowMultiple) { ?>
	<div class="es-form-working-hour__cell es-form-working-hour__cell--action">
		<a href="javascript:void(0);" class="es-form-working-hour__action-link" data-hours-add>
			<i class="fa fa-plus-circle t-icon--success"></i>
		</a>
		<a href="javascript:void(0);" class="es-form-working-hour__action-link<?php echo $index == 0 ? ' t-hidden' : '';?>" data-hours-remove>
			<i class="fa fa-minus-circle t-icon--danger"></i>
		</a>
	</div>
	<?php } ?>

</div>
