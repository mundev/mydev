<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ES_BUTTON_COLOURS'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.colorpicker', 'buttons.primary', 'COM_ES_PRIMARY_BUTTON', '', '#428bca'); ?>
				<?php echo $this->html('settings.colorpicker', 'buttons.success', 'COM_ES_SUCCESS_BUTTON', '', '#39b54a'); ?>
				<?php echo $this->html('settings.colorpicker', 'buttons.standard', 'COM_ES_STANDARD_BUTTON', '', '#333333'); ?>
				<?php echo $this->html('settings.colorpicker', 'buttons.danger', 'COM_ES_DANGER_BUTTON', '', '#d9534f'); ?>
			</div>
		</div>
		
	</div>

	<div class="col-md-6">

	</div>
</div>
