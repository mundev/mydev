
<?php if ($params->get('use_maps')) { ?>
EasySocial.require().script('apps/fields/user/address/maps').done(function($) {
	$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Address.Maps', {
		id: <?php echo $field->id; ?>,
		latitude: <?php echo !empty($value->latitude) ? $value->latitude : '""'; ?>,
		longitude: <?php echo !empty($value->longitude) ? $value->longitude : '""'; ?>,
		address: '<?php echo addslashes($value->toString()); ?>',
		zoom: <?php echo !empty($value->zoom) ? $value->zoom : 2; ?>,
		required: <?php echo $required; ?>
	});
});
<?php } else { ?>
EasySocial.require().script('apps/fields/user/address/content').done(function($) {
	$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Address', {
		id: <?php echo $field->id; ?>,
		required: <?php echo $required; ?>,
		show: <?php echo $show; ?>,
		selectCountryText: "<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_COUNTRY_FIRST'); ?>",
		selectStateText: "<?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_STATE'); ?>"
	});
});
<?php }
