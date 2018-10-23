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
<div class="row">
	<div class="col-lg-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_CATEGORIES_EDIT_GENERAL', 'COM_EASYBLOG_CATEGORIES_EDIT_GENERAL_INFO'); ?>
			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PRIVACY'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_CATEGORIES_PRIVACY'); ?>" 
							data-content="<?php echo JText::_('COM_EASYBLOG_CATEGORY_PRIVACY_TIPS');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php if ($this->config->get('main_category_privacy')) { ?>
							<?php echo JHTML::_('select.genericlist' , EB::privacy()->getOptions('category') , 'private' , 'class="form-control"' , 'value' , 'text', $category->private);?>
						<?php } else { ?>
							<?php echo JText::_('Category privacy has been disabled in the settings. You may turn it on in the settings. Do take note that having a complex category structure with privacy may slow down the site.'); ?>
							<div>
								<a href="index.php?option=com_easyblog&view=settings" class="btn btn-sm btn-default">View Settings</a>
							</div>
							<input type="hidden" name="private" value="0" />
						<?php }?>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="col-lg-6">
		<div class="panel<?php echo $category->private != 2 ? ' hide' : '';?>" data-category-access>
			<?php echo $this->html('panel.heading', 'COM_EASYBLOG_CATEGORIES_ASSIGNED_PERMISSIONS', 'COM_EASYBLOG_CATEGORIES_ASSIGNED_PERMISSIONS_INFO'); ?>

			<div class="panel-body">
				<?php foreach ($rules as $rule) { ?>
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo $rule->title;?>

						<i data-html="true" data-placement="top" data-title="<?php echo $rule->title;?>" 
							data-content="<?php echo $rule->desc;?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select multiple="multiple" name="category_acl_<?php echo $rule->action; ?>[]" class="form-control" style="height: 150px;">
							<?php foreach ($groups[$rule->id] as $group) { ?>
							<option value="<?php echo $group->groupid; ?>" style="padding:2px;" <?php echo ($group->status) ? 'selected="selected"' : ''; ?> ><?php echo $group->groupname; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

	</div>
</div>