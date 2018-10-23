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
<form name="adminForm" id="adminForm" action="index.php" method="post" data-grid-eb>
	<div class="panel-table">
		<table class="app-table table table-striped table-eb">
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_FILENAME'); ?>
					</th>
					<th width="35%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_FILE_DESCRIPTION'); ?>
					</th>
					<th width="40%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_LOCATION'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_PREVIEW'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_MODIFIED'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($files) { ?>
					<?php $i = 0; ?>
					<?php foreach ($files as $file) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i, base64_encode($file->relative)); ?>
						</td>
						<td>
							<a href="index.php?option=com_easyblog&view=spools&layout=editfile&file=<?php echo urlencode($file->relative);?>"><?php echo $file->name; ?></a>
						</td>
						<td>
							<?php echo $file->desc;?>
						</td>
						<td>
							<?php echo str_ireplace(JPATH_ROOT, '', $file->path);?>
						</td>
						<td class="center">
							<?php if ($file->relative == '/template.php') { ?>
								&mdash;
							<?php } else { ?>
								<a href="javascript:void(0);" data-mail-preview="<?php echo urlencode($file->relative);?>"><i class="fa fa-eye"></i></a>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $file, 'files', 'override', array(), array(), true); ?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="view" value="spools" />
	<input type="hidden" name="controller" value="spools" />
</form>