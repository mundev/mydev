<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell">
			<?php echo $this->html('filter.search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.custom', $filterState); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.limit', $limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table table-striped table-eb table-hover">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<th><?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_COMMENT');?></th>

					<th width="30%">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_COMMENTS_BLOG_TITLE'), 'b.title', $orderDirection, $order ); ?>
					</th>

					<th width="1%"><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_PUBLISHED' ); ?></th>
					<th class="center" width="10%">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_COMMENTS_DATE'), 'a.created', $orderDirection, $order ); ?>
					</th>
					<th class="center" width="10%">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_COMMENTS_AUTHOR') , 'a.created_by', $orderDirection, $order ); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($comments) { ?>
					<?php $i = 0; ?>
					<?php foreach ($comments as $comment) { ?>
						<tr>
							<td class="center nowrap">
								<?php echo $this->html('grid.id', $i++, $comment->id); ?>
							</td>
							<td>
								<a href="index.php?option=com_easyblog&view=comments&layout=form&id=<?php echo $comment->id;?>"><?php echo EBString::substr($comment->comment, 0, 60);?></a><?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
							</td>
							<td>
								<a href="<?php echo JURI::root() . 'index.php?option=com_easyblog&amp;view=entry&amp;id=' . $comment->post_id; ?>" target="_blank">
									<?php echo EBString::substr($comment->blog_name, 0, 50); ?><?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
								</a>
							</td>
							<td class="center">
								<?php if ($comment->isModerate) { ?>
									<?php echo $this->html('grid.moderation', $comment, 'comment', 'published'); ?>
								<?php } else { ?>
									<?php echo $this->html('grid.published', $comment, 'comment', 'published'); ?>
								<?php } ?>
							</td>

							<td class="center">
								<?php echo EB::date($comment->created, true)->format();?>
							</td>

							<td class="center">
								<span>
								<?php if ($comment->created_by) { ?>
									<?php echo $comment->getAuthor()->getName(); ?>
								<?php } ?>

								<?php if (!$comment->created_by && $comment->name) { ?>
									<?php echo $comment->name; ?>
								<?php } ?>

								<?php if (!$comment->created_by && !$comment->name) { ?>
									<?php echo JText::_('COM_EASYBLOG_GUEST'); ?>
								<?php } ?>
								</span>
							</td>
							<td class="center"><?php echo $comment->id; ?></td>
						</tr>
					<?php } ?>

				<?php } else { ?>
				<tr>
					<td colspan="7" align="center" class="empty">
						<?php echo JText::_('COM_EASYBLOG_COMMENTS_NO_COMMENT_ON_SITE_YET');?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="11">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="view" value="comments" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
