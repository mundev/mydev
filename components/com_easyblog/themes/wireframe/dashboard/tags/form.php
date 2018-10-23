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
<form method="post" action="<?php echo JRoute::_('index.php');?>" enctype="multipart/form-data">
	<?php echo $this->html('dashboard.heading', (!$tag->id) ? 'COM_EASYBLOG_DIALOG_CREATE_TAG_TITLE' : 'COM_EASYBLOG_DIALOG_EDIT_TAG_TITLE', 'fa fa-tag'); ?>

	<div class="eb-box">
		<div class="eb-box-body">
			<div class="form-horizontal clear">
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_NAME'); ?></label>
					<div class="col-md-7">
						<input type="text" id="tags" name="tags" class="form-control" value="<?php echo $this->escape($tag->title);?>" placeholder="<?php JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW_PLACEHOLDER'); ?>" />

						<?php if (!$tag->id) { ?>
						<div>
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAG_CREATE_NEW_HELP');?>
						</div>
						<?php } ?>
					</div>
				</div>


				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo JText::_('COM_EASYBLOG_COMPOSER_POST_LANGUAGE');?></label>

					<div class="col-md-7">
						<select name="tag_language" class="form-control" data-composer-language>
							<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $tag->language);?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="form-actions">
		<div class="pull-left">
			<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=tags');?>" class="btn btn-default">
				<?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?>
			</a>
		</div>

		<div class="pull-right">
			<button class="btn btn-primary" data-submit-button>
				<?php echo ($tag->id) ? JText::_('COM_EASYBLOG_UPDATE_BUTTON') : JText::_('COM_EASYBLOG_CREATE_BUTTON'); ?>
			</button>
		</div>
	</div>

	<?php if ($tag->id) { ?>
		<input type="hidden" name="id" value="<?php echo $tag->id;?>" />
		<?php echo $this->html('form.action', 'tags.save'); ?>
	<?php } else { ?>
		<?php echo $this->html('form.action', 'tags.create'); ?>
	<?php } ?>
</form>
