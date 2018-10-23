<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div data-field-joomla_fullname data-error-empty="<?php echo JText::_('PLG_FIELDS_JOOMLA_FULLNAME_VALIDATION_EMPTY_NAME');?>">
	
	<div class="o-grid o-grid--gutters">
		<?php if ($params->get('format') != 5) { ?>
			<div class="o-grid__cell">
				<input type="text" class="o-form-control " id="first_name" name="first_name"
					placeholder="<?php echo JText::_( $params->get( 'format' ) == 3 ? 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_YOUR_NAME' : 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME' , true );?>"
					<?php if ($params->get('format') != 3) { ?>
					
					<?php } ?>
					data-field-jname-first
				/>
			</div>

			<?php if ($params->get('format') != 3) { ?>
			<div class="o-grid__cell">
				<input type="text" class="o-form-control" id="last_name" name="last_name" placeholder="<?php echo JText::_('PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME', true);?>" />
			</div>
			<?php } ?>
		<?php } ?>

		<?php if ($params->get('format') == 5) { ?>
		<div class="o-grid__cell">
			<input type="text" class="o-form-control" id="last_name" name="last_name" placeholder="<?php echo JText::_('PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_LAST_NAME', true);?>" />
		</div>

		<div class="o-grid__cell">
			<input type="text" name="first_name" class="o-form-control" id="first_name"
				placeholder="<?php echo JText::_($params->get('format') == 3 ? 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_YOUR_NAME' : 'PLG_FIELDS_JOOMLA_FULLNAME_PLACEHOLDER_FIRST_NAME', true);?>" data-field-jname-first
			/>
		</div>
		<?php } ?>
	</div>

	<div class="es-fields-error-note" data-field-error></div>
</div>
