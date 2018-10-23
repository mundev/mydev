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
<div class="eb-settings-info">
	<i class="fa fa-info-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SETTINGS_CATEGORY_LISTINGS_LAYOUT_NOTE');?>
</div>

<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->category['category']->label, $fieldsets->category['category']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->category['category']->fields, 'key' => 'category')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->category['theme']->label, $fieldsets->category['theme']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->category['theme']->fields, 'key' => 'category')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->category['comments']->label, $fieldsets->category['comments']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->category['comments']->fields, 'key' => 'category')); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->category['limit']->label, $fieldsets->category['limit']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->category['limit']->fields, 'key' => 'category')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->category['post']->label, $fieldsets->category['post']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->category['post']->fields, 'key' => 'category')); ?>
			</div>
		</div>
	</div>
</div>
