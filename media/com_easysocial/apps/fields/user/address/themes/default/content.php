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
<div data-field-address
	data-error-maps="<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_ENTER_MAPS_ADDRESS', true);?>"
	data-error-address1="<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_ENTER_ADDRESS', true);?>"
	data-error-address2="<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_ENTER_ADDRESS', true);?>"
	data-error-state="<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_ENTER_STATE', true);?>"
	data-error-city="<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_ENTER_CITY', true);?>"
	data-error-country="<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_ENTER_COUNTRY', true);?>"
	data-error-map-permission="<?php echo JText::_('COM_EASYSOCIAL_LOCATION_PERMISSION_ERROR', true);?>"
	data-error-map-timeout="<?php echo JText::_('COM_EASYSOCIAL_LOCATION_TIMEOUT_ERROR', true);?>"
	data-error-map-unavailable="<?php echo JText::_('COM_EASYSOCIAL_LOCATION_UNAVAILABLE_ERROR', true);?>"
>
<?php if ($params->get('use_maps')) { ?>
	<?php $hideLocationRemoveButton = (JFactory::getApplication()->isAdmin() && ($this->input->get('view', '', 'cmd') == 'profiles')) ? true : false; ?>
	<div class="es-locations" data-location-base>
		<div class="es-location-map" data-location-map>
			<div>
				<div class="es-location-map-image" data-location-map-image></div>
				<div class="es-location-map-actions">
					<button class="btn btn-es-primary-o btn-sm es-location-detect-button" type="button" data-location-detect><i class="fa fa-flash"></i> <?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION', true); ?></button>
				</div>
			</div>
		</div>

		<div class="es-location-form es-field-location-form has-border" data-location-form>
			<div class="es-location-textbox" data-location-textbox data-language="<?php echo FD::config()->get('general.location.language'); ?>">
				<input type="text" placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_SET_A_LOCATION'); ?>" autocomplete="off" data-location-textfield disabled <?php $fulladdress = !empty($value->address) ? $value->address : $value->toString(); if (!empty($fulladdress)) { ?>value="<?php echo $fulladdress; ?>"<?php } ?> />
				<div class="es-location-autocomplete has-shadow is-sticky" data-location-autocomplete>
					<b><b></b></b>
					<div class="es-location-suggestions" data-location-suggestions>
					</div>
				</div>
			</div>
			<div class="es-location-buttons<?php echo ($hideLocationRemoveButton) ? ' hide' : '';?>">
				<div class="o-loader o-loader--sm"></div>
				<a class="es-location-remove-button" href="javascript: void(0);" data-location-remove><i class="fa fa-remove"></i></a>
			</div>
		</div>

		<input type="hidden" name="<?php echo $inputName; ?>" data-location-source value="<?php echo FD::string()->escape($value->toJson()); ?>" />
	</div>
<?php } else { ?>
	<div class="">
		<?php if ($params->get('show_address1')) { ?>
		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<input type="text" class="o-form-control validation keyup length-4"
				placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ADDRESS1_PLACEHOLDER', true);?>"
				name="<?php echo $inputName;?>[address1]"
				value="<?php echo FD::string()->escape($value->address1);?>"
				data-field-address-address1
				/>
			</div>
		</div>
		<?php } ?>

		<?php if ($params->get('show_address2')) { ?>
		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<input type="text" class="o-form-control validation keyup length-4"
				placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ADDRESS2_PLACEHOLDER', true);?>"
				name="<?php echo $inputName;?>[address2]"
				value="<?php echo FD::string()->escape($value->address2);?>"
				data-field-address-address2
				/>
			</div>
		</div>
		<?php } ?>

		<?php if ($params->get('show_city') && $params->get('show_zip')) { ?>
		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<input type="text" class="o-form-control validation keyup length-4"
				placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_CITY_PLACEHOLDER', true);?>"
				name="<?php echo $inputName;?>[city]"
				value="<?php echo FD::string()->escape($value->city);?>"
				data-field-address-city
				/>
			</div>
			<div class="o-grid__cell u-1of3">
				<input type="text" class="o-form-control validation keyup length-4"
				placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ZIP_PLACEHOLDER', true);?>"
				name="<?php echo $inputName;?>[zip]"
				value="<?php echo FD::string()->escape($value->zip);?>"
				data-field-address-zip
				/>
			</div>
		</div>
		<?php } ?>

		<?php if ($params->get('show_city') xor $params->get('show_zip')) { ?>
		<div class="o-grid o-grid--gutters">
			<?php if ($params->get('show_city')) { ?>
			<div class="o-grid__cell">
				<input type="text" class="o-form-control validation keyup length-4"
				placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_CITY_PLACEHOLDER', true);?>"
				name="<?php echo $inputName;?>[city]"
				value="<?php echo FD::string()->escape($value->city);?>"
				data-field-address-city
				/>
			</div>
			<?php } ?>

			<?php if ($params->get('show_zip')) { ?>
			<div class="o-grid__cell">
				<input type="text" class="o-form-control validation keyup length-4"
				placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ZIP_PLACEHOLDER', true);?>"
				name="<?php echo $inputName;?>[zip]"
				value="<?php echo FD::string()->escape($value->zip);?>"
				data-field-address-zip
				/>
			</div>
			<?php } ?>
		</div>
		<?php } ?>


		<?php if ($params->get('show_state') && $params->get('show_country')) { ?>
		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<select class="o-form-control" name="<?php echo $inputName;?>[country]"
				data-field-address-country>
					<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_SELECT_A_COUNTRY'); ?></option>
					<?php foreach($countries as $code => $title){ ?>
					<option value="<?php echo $title;?>"<?php echo $title == $value->country ? ' selected="selected"' : '';?>><?php echo $title;?></option>
					<?php } ?>
				</select>
			</div>

			<?php if ($params->get('data_source') === 'regions') { ?>
				<div class="o-grid__cell o-grid__cell--1of3">
					<select
						class="o-form-control"
						name="<?php echo $inputName;?>[state]"
						data-field-address-state
					>
					<?php if (!empty($states)) { ?>
						<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_STATE'); ?></option>
					<?php foreach ($states as $code => $title) { ?>
						<option value="<?php echo $title; ?>" <?php if ($value->state === $title) { ?>selected="selected"<?php } ?>><?php echo $title; ?></option>
					<?php } ?>
					<?php } else { ?>
						<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER'); ?></option>
						<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_COUNTRY_FIRST'); ?></option>
					<?php } ?>
					</select>
				</div>
			<?php } else { ?>
				<div class="o-grid__cell o-grid__cell--1of3">
					<input
						type="text"
						class="o-form-control validation keyup length-4"
						placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER', true);?>"
						name="<?php echo $inputName;?>[state]"
						value="<?php echo FD::string()->escape($value->state);?>"
						data-field-address-state
					/>
				</div>
			<?php } ?>
		</div>
		<?php } ?>

		<?php if ($params->get('show_state') xor $params->get('show_country')) { ?>
		<div class="o-grid o-grid--gutters">
			<?php if ($params->get('show_country')) { ?>
				<div class="o-grid__cell">
					<select class="o-form-control" name="<?php echo $inputName;?>[country]"
					data-field-address-country>
						<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_SELECT_A_COUNTRY'); ?></option>
						<?php foreach($countries as $code => $title){ ?>
						<option value="<?php echo $title;?>"<?php echo $title == $value->country ? ' selected="selected"' : '';?>><?php echo $title;?></option>
						<?php } ?>
					</select>
				</div>
			<?php } ?>

			<?php if ($params->get('show_state')) { ?>
				<?php if ($params->get('data_source') === 'regions') { ?>
					<div class="o-grid__cell">
						<select
							class="o-form-control"
							name="<?php echo $inputName;?>[state]"
							data-field-address-state
						>
						<?php if (!empty($states)) { ?>
						<?php foreach ($states as $code => $title) { ?>
							<option value="<?php echo $title; ?>" <?php if ($value->state === $state) { ?>selected="selected"<?php } ?>><?php echo $title; ?></option>
						<?php } ?>
						<?php } else { ?>
							<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER'); ?></option>
							<option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_COUNTRY_FIRST'); ?></option>
						<?php } ?>
						</select>
					</div>
				<?php } else { ?>
					<div class="o-grid__cell">
					<input type="text" name="<?php echo $inputName;?>[state]" class="o-form-control validation keyup length-4"
						value="<?php echo FD::string()->escape($value->state);?>"  placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER', true);?>"
						data-field-address-state />
					</div>
				<?php } ?>
			<?php } ?>
		</div>
		<?php } ?>
	</div>

<?php } ?>

<div class="es-fields-error-note" data-field-error></div>
</div>
