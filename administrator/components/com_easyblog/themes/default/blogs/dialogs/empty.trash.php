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
	<height>120</height>
	<selectors type="json">
	{
		"{submit}": "[data-submit-button]",
		"{cancel}": "[data-cancel-button]",
		"{form}": "[data-empty-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancel} click": function() {
			this.parent.close();
		},

		"{submit} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYBLOG_EMPTY_TRASH_DIALOG_TITLE');?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-empty-form>
			<p><?php echo JText::_('COM_EASYBLOG_EMPTY_TRASH_CONFIRMATION'); ?></p>

			<?php echo $this->html('form.action', 'blogs.emptyTrash'); ?>
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
		<button data-submit-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYBLOG_EMPTY_TRASH_BUTTON'); ?></button>
	</buttons>
</dialog>
