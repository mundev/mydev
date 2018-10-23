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
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<div class="row">
		<div class="col-lg-5">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_THEMES_EDITOR_SELECT_FILES'); ?>

				<div class="panel-body">
					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_THEMES_EDITOR_FILE', 'file'); ?>

						<div class="col-md-7">
							<select name="file" id="file" class="form-control" data-files-selection>
								<option value=""><?php echo JText::_('COM_EASYBLOG_THEMES_DROPDOWN_SELECT_FILE'); ?></option>
								<?php foreach ($files as $group => $files) { ?>
									<optgroup label="<?php echo ucfirst($group);?>">
										<?php foreach ($files as $file) { ?>
										<option value="<?php echo $file->id;?>" <?php echo $id == $file->id ? 'selected="selected"' : '';?>
										<?php echo $file->modified ? 'style="background-color: #d3f1d7;"' : '';?>
										>
											<?php echo $file->title;?> <?php echo $file->modified ? JText::_('(Modified)') : ''; ?>
										</option>
										<?php } ?>
									</optgroup>
								<?php } ?>
							</select>
						</div>
					</div>

					<?php if ($item) { ?>
					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_THEMES_EDITOR_MODIFIED', 'modified'); ?>

						<div class="col-md-7">
							<?php if ($item->modified) { ?>
								<span class="text-success">
									<i class="fa fa-pencil"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_THEMES_EDIT_FILE_MODIFIED'); ?>
								</span>
							<?php } else {  ?>
								<span><?php echo JText::_('COM_EASYBLOG_THEMES_EDIT_FILE_NOT_MODIFIED'); ?></span>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_THEMES_PATH', 'path'); ?>

						<div class="col-md-7">
							<input type="text" id="path" class="form-control disabled" value="<?php echo $item->absolute;?>" />
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_THEMES_OVERRIDE_PATH', 'override'); ?>

						<div class="col-md-7">
							<input type="text" id="override" class="form-control disabled" value="<?php echo $item->override;?>" />
						</div>
					</div>

					<div class="form-group">
						<?php echo $this->html('form.label', 'COM_EASYBLOG_THEMES_CUSTOM_NOTES', 'notes'); ?>

						<div class="col-md-7">
							<textarea id="notes" name="notes" class="form-control"><?php echo $table->notes;?></textarea>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-lg-7">
			<div class="panel">
				<?php echo $this->html('panel.heading', 'COM_EASYBLOG_THEMES_EDITOR_FILE_EDITOR'); ?>

				<div class="panel-body">
					<?php if ($item) { ?>
						<?php echo $editor->display('contents', $item->contents, '100%', '450px', 80, 30, false, null, null, null, array('syntax' => 'php', 'filter' => 'raw')); ?>
					<?php } else { ?>
						<div class="text-center">
							<?php echo JText::_('COM_EASYBLOG_THEMES_PLEASE_SELECT_FILE_FIRST'); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="element" value="<?php echo $element;?>" />
	<input type="hidden" name="id" value="<?php echo $id;?>" />
	<?php echo $this->html('form.action'); ?>
</form>