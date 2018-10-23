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
	<i class="fa fa-info-circle"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SETTINGS_TAG_LISTINGS_LAYOUT_NOTE');?>
</div>

<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->tag['tag']->label, $fieldsets->tag['tag']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->tag['tag']->fields, 'key' => 'tag')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->tag['theme']->label, $fieldsets->tag['theme']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->tag['theme']->fields, 'key' => 'tag')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->tag['comments']->label, $fieldsets->tag['comments']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->tag['comments']->fields, 'key' => 'tag')); ?>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->tag['limit']->label, $fieldsets->tag['limit']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->tag['limit']->fields, 'key' => 'tag')); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', $fieldsets->tag['post']->label, $fieldsets->tag['post']->info); ?>

			<div class="panel-body">
				<?php echo $this->output('admin/settings/views/fields/renderer', array('fields' => $fieldsets->tag['post']->fields, 'key' => 'tag')); ?>
			</div>
		</div>
	</div>
</div>
