<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($fields) { ?>
<div class="eb-composer-fieldset" data-eb-composer-panel-fields data-panel-field data-group-id="<?php echo $group->id;?>" data-category-id="<?php echo $id;?>">
	<div class="eb-composer-fieldset-header">
		<strong><?php echo JText::_($group->title);?></strong>
	</div>
	<div class="eb-composer-fieldset-content xo-form-horizontal">
		<?php foreach ($fields as $field) {
			$customFieldValue = $field->getDisplay($post);

			// those custom field already have the default value by default
			if ($field->type == 'heading' || $field->type == 'select') {
				$customFieldValue = true;
			}

		 ?>
		<div class="eb-composer-fieldset-form-group-wrapper has-css-field">
			<div class="o-form-group" data-wrapper-field-class>
				<label class="o-control-label">
					<?php if ($field->required) { ?>
					<span class="required">*</span>
					<?php } ?>

					<?php echo $field->getTitle(); ?>
					<i data-html="true" data-placement="bottom" data-title="<?php echo $field->getTitle(); ?>"
						data-content="<?php echo $field->getHelp();?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
				</label>
				<div class="o-control-input">
					<?php echo $field->getForm($post, 'fields');?>
				</div>
			</div>

			<div class="o-form-group <?php echo $customFieldValue ? '' : 'hide' ;?>" data-field-type-<?php echo $field->type; ?>>
				<label class="o-control-label">
					<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CUSTOM_CSS_WRAPPER_CLASS'); ?>
					<i data-html="true" data-placement="bottom" data-title="<?php echo JText::_('COM_EB_COMPOSER_FIELD_CLASS_PREFIX'); ?>"
						data-content="<?php echo JText::_('COM_EB_COMPOSER_FIELD_CLASS_PREFIX_DESC'); ?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
				</label>
				<div class="o-control-input">
					<?php echo $field->getClassForm($post, 'fields');?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
