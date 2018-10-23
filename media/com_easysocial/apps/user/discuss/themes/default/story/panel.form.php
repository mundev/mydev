<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-story-discuss-form" data-story-discuss-form>
	<div class="o-form-group">
		<?php echo $nestedCategories; ?>
	</div>

	<div data-story-discuss-form class="t-lg-mt--md">
		<div class="o-form-group">
			<input type="text" class="o-form-control" placeholder="<?php echo JText::_('APP_USER_EASYDISCUSS_STORY_TITLE_PLACEHOLDER');?>" data-discuss-title />
		</div>

		<div class="o-form-group">
			<textarea name="content" id="content" class="o-form-control" style="height:150px;" placeholder="<?php echo JText::_('APP_USER_EASYDISCUSS_STORY_CONTENT_PLACEHOLDER');?>" data-discuss-content></textarea>
		</div>

		<?php if ($params->get('story_attachment', true)) { ?>
			<div class="es-story-files-content" data-discuss-canvas>
				<div class="es-story-files-dropsite" data-discuss-dropsite>
					<div class="es-story-files-upload" data-discuss-upload>
						<span>
							<b class="add-hint"><i class="fa fa-upload"></i><?php echo JText::_('APP_USER_FILES_STORY_ADD_FILES'); ?></b>
							<b class="drop-hint"><i class="fa fa-upload"></i><?php echo JText::_("APP_USER_FILES_DROP_FILES_CANVAS"); ?></b>
						</span>
					</div>
					<div class="es-story-files-items" data-discuss-items>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<div class="hide" data-discuss-templates>
	<div class="file-item" data-discuss-template="item">
		<div class="file-icon">
			<i class="fa fa-archive"></i>
			<div class="file-name" data-name></div>
		</div>
		<div class="remove-button" data-discuss-item-remove>
			<i class="fa fa-trash"></i> <?php echo JText::_('APP_USER_FILES_STORY_REMOVE_FILE');?>
		</div>
	</div>

	<div id="" class="es-story-files-progress" data-discuss-template="progress">
		<table>
			<tr class="upload-status">
				<td>
					<div class="upload-progress progress progress-striped active">
						<div class="upload-progress-bar bar progress-bar-info" style="width: 0%"><span class="upload-percentage"></span></div>
					</div>

					<div class="upload-remove-button"><i class="fa fa-remove"></i></div>
				</td>
			</tr>
		</table>
	</div>
</div>