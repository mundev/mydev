<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-cancel-button]",
		"{form}": "[data-blogger-resetOrdering-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {

			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EB_BLOGGER_RESET_ORDERING_DIALOG_INFO');?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-blogger-resetOrdering-form>
			<p><?php echo JText::_('COM_EB_BLOGGER_RESET_ORDERING_DIALOG_CONFIRMATION'); ?></p>

			<?php echo $this->html('form.action', 'bloggers.resetOrdering'); ?>
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
		<button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_RESET_BUTTON'); ?></button>
	</buttons>
</dialog>
