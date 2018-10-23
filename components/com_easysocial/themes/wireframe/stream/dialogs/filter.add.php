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
<dialog>
	<width>400</width>
	<height>200</height>
	<selectors type="json">
	{
		"{saveButton}"		: "[data-save-button]",
		"{cancelButton}"	: "[data-cancel-button]",
		"{inputTitle}"		: "[data-filter-name]",
		"{inputWarning}" 	: "[filter-form-notice]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_ADD_DIALOG_TITLE' ); ?></title>
	<content>
		<p><?php echo JText::sprintf( 'COM_EASYSOCIAL_STREAM_FILTER_ADD_CONFIRMATION', '<span class="label label-info">#' . $tag . '</span>' ); ?></p>


		<div class="alert" filter-form-notice style="display:none;"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_WARNING_TITLE_EMPTY_SHORT' ); ?></div>

		<div class="control-group mt-15">
			<label class="control-label"><?php echo JText::_( 'COM_EASYSOCIAL_STREAM_FILTER_TITLE' ); ?>:</label>
			<div class="controls">
				<input type="text" name="title" value="" data-filter-name class="o-form-control" />
			</div>
		</div>

	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-es-default btn-sm"><?php echo JText::_('COM_ES_CANCEL'); ?></button>
		<button data-save-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_SAVE_BUTTON'); ?></button>
	</buttons>
</dialog>
