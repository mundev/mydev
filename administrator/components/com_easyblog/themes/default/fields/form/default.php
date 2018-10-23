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
<form id="adminForm" name="adminForm" method="post" action="index.php">
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_FIELDS_FIELD_DETAILS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_GROUP', 'group_id'); ?>

					<div class="col-md-7">
						<select name="group_id" id="group_id" class="form-control">
							<option value=""><?php echo JText::_('COM_EASYBLOG_FIELDS_SELECT_FIELD_GROUP');?></option>
							<?php foreach ($groups as $group) { ?>
								<option value="<?php echo $group->id;?>"<?php echo $field->group_id == $group->id ? ' selected="selected"' : '';?>><?php echo JText::_($group->title);?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_TYPE', 'type'); ?>
					
					<div class="col-md-7">
						<select name="type" id="type" class="form-control" data-field-type>
							<option value=""><?php echo JText::_('COM_EASYBLOG_FIELDS_SELECT_FIELD_TYPE');?></option>
							<?php foreach ($fields as $fieldItem) { ?>
								<option value="<?php echo $fieldItem->getElement();?>"<?php echo $field->type == $fieldItem->getElement() ? ' selected="selected"' : '';?>><?php echo $fieldItem->getTitle();?></option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_TITLE', 'title'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'title', $this->html('string.escape', $field->title), 'title'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_HELP', 'help'); ?>

					<div class="col-md-7">
						<textarea name="help" id="help" class="form-control"><?php echo $this->html('string.escape', $field->help);?></textarea>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_PUBLISHED', 'state'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'state', is_null($field->state) ? true : $field->state); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('form.label', 'COM_EASYBLOG_FIELDS_FIELD_REQUIRED', 'required'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'required', is_null($field->required) ? false : $field->required); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_FIELDS_FIELD_PROPERTIES'); ?>

			<div class="panel-body" data-field-form>
				<?php echo $form;?>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="id" value="<?php echo $field->id;?>" />
<?php echo $this->html('form.action');?>
</form>
