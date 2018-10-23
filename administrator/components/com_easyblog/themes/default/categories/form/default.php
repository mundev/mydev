<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-eb-form>
	<div class="app-tabs">
		<ul class="app-tabs-list list-unstyled">
			<li class="tabItem<?php echo $active == 'general' ? ' active' : '';?>">
				<a href="#general" data-form-tabs  data-bp-toggle="tab">
					<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_TITLE');?>
				</a>
			</li>

			<li class="tabItem<?php echo $active == 'access' ? ' active' : '';?>">
				<a href="#access" data-form-tabs data-bp-toggle="tab">
					<?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_PERMISSIONS');?>
				</a>
			</li>

			<li class="tabItem<?php echo $active == 'entry' ? ' active' : '';?>">
				<a href="#entry" data-form-tabs data-bp-toggle="tab">
					<?php echo JText::_('COM_EASYBLOG_CATEGORIES_ENTRY_LAYOUT_TAB');?>
				</a>
			</li>
		</ul>
	</div>

	<div class="tab-content">
		<div id="general" class="tab-pane<?php echo $active == 'general' ? ' active' : '';?>">
			<?php echo $this->output('admin/categories/form/general'); ?>
		</div>

		<div id="entry" class="tab-pane<?php echo $active == 'entry' ? ' active' : '';?>">
			<?php echo $this->output('admin/categories/form/post'); ?>
		</div>

		<div id="access" class="tab-pane<?php echo $active == 'access' ? ' active' : '';?>">
			<?php echo $this->output('admin/categories/form/access'); ?>
		</div>
	</div>

	<?php echo JHTML::_('form.token'); ?>
	<input type="hidden" name="active" value="" data-eb-form-activetab />
	<input type="hidden" name="savenew" value="0" id="savenew" />
	<input type="hidden" name="option" value="com_easyblog" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="id" value="<?php echo $category->id;?>" />
</form>
