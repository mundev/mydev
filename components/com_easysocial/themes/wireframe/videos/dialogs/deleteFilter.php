<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{deleteFilter}": "[data-delete-button]",
		"{form}" : "[data-filter-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		init: function()
		{
		},
		"{closeButton} click": function()
		{
			this.parent.close();
		},
		"{deleteFilter} click": function()
		{
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('Delete Filter'); ?></title>
	<content>
		<form data-filter-form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
			<p class="t-lg-mt--md">
				<?php echo JText::_('Are you sure you want to delete this filter?');?>
			</p>

			<?php echo $this->html('form.token'); ?>
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="controller" value="tag" />
			<input type="hidden" name="task" value="deleteFilter" />
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="filterType" value="videos" />
			<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
			<input type="hidden" name="clusterType" value="<?php echo $clusterType; ?>" />
		</form>

	</content>
	<buttons>
		<button data-close-button type="button" class="btn btn-es-default btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CLOSE_BUTTON'); ?></button>
		<button data-delete-button type="button" class="btn btn-es-danger btn-sm"><?php echo JText::_('Delete Filter'); ?></button>
	</buttons>
</dialog>
