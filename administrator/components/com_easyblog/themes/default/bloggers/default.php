<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell">
			<?php echo $this->html('filter.search', $search, 'search', 'COM_EB_SEARCH_TOOLTIP_BLOGGERS'); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('filter.published', 'filter_state', $filterState); ?>
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
		<table class="app-table table table-eb table-striped table-hover">
			<thead>
				<tr>
					<?php if (!$browse) { ?>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<?php } ?>

					<th>
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_BLOGGERS_NAME' ), 'a.name', $orderDirection, $order); ?>
					</th>

					<?php if (!$browse) { ?>
					<th width="15%">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_BLOGGERS_USERNAME') , 'a.username', $orderDirection, $order ); ?>
					</th>

					<th width="15%">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_EMAIL' ) , 'a.email', $orderDirection, $order ); ?>
					</th>

					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYBLOG_FEATURED'); ?>
					</th>

					<th class="center" width="10%">
						<?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS');?>
					</th>

					<th class="center" width="15%">
						<?php echo JText::_('COM_EASYBLOG_BLOGGERS_USER_GROUP');?>
					</th>

					<th width="10%">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYBLOG_TABLE_COLUMN_ORDERING'), 'users.ordering', $orderDirection, $order); ?>
					</th>


					<?php } ?>

					<th class="center" width="5%">
						<?php echo JHTML::_('grid.sort', 'ID', 'a.id', $orderDirection, $order ); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($authors) { ?>
					<?php $i = 0; ?>

					<?php foreach ($authors as $author) { ?>
					<tr data-item data-id="<?php echo $author->id;?>" data-title="<?php echo $author->getName();?>">
						<?php if (!$browse) { ?>
						<td class="center">
							<?php echo $this->html('grid.id', $i++, $author->id); ?>
						</td>
						<?php } ?>

						<td>
							<?php if ($browse) { ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $author->id;?>','<?php echo addslashes($this->escape($author->getName()));?>');"><?php echo $author->getName();?></a>
							<?php } else { ?>
								<a href="index.php?option=com_easyblog&view=bloggers&layout=form&id=<?php echo $author->id;?>"><?php echo $author->getName();?></a>
							<?php } ?>
						</td>

						<?php if (!$browse) { ?>
						<td>
							<?php echo $author->user->username; ?>
						</td>

						<td>
							<?php echo $author->user->email; ?>
						</td>

						<td class="nowrap hidden-phone center">
							<?php echo $this->html('grid.featured', $author, 'bloggers', 'featured', array('bloggers.feature', 'bloggers.unfeature')); ?>
						</td>

						<td class="center">
							<?php echo $author->postCount;?>
						</td>

						<td class="center">
							<?php if (count($author->user->groups) > 1) { ?>
								<?php echo JText::_('COM_EB_MULTIPLE_GROUPS'); ?>
							<?php } else { ?>
								<?php echo $author->usergroups;?>
							<?php } ?>
						</td>

						<td class="order">
							<input type="text" name="order[]" value="<?php echo $author->ordering;?>" data-ordering-id class="order-value input-xsmall"/>
							
							<a href="javascript:void(0);" class="btn btn-primary btn-xs btn-author-ordering" data-author-ordering data-id=<?php echo $author->id;?>>
								<?php echo JText::_('COM_EASYBLOG_UPDATE_BUTTON');?>
							</a>				
						</td>


						<?php } ?>

						<td class="center">
							<?php echo $author->id; ?>
						</td>
					</tr>
					<?php } ?>


				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="12" class="text-center">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php if ($browse) { ?>
	<input type="hidden" name="tmpl" value="component" />
	<?php } ?>

	<?php echo $this->html('form.action'); ?>
	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="browsefunction" value="<?php echo $browsefunction;?>" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="view" value="bloggers" />
</form>
