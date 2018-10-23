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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell">
			<?php echo $this->html('filter.search', $search); ?>
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
		<table class="app-table table table-striped table-eb table-hover" data-table-grid>
			<thead>
				<?php if( !$browse ){ ?>
				<th class="center" width="1%">
					<?php echo $this->html('grid.checkAll'); ?>
				</th>
				<?php }?>
				
				<th style="text-align: left;">
					<?php echo JHTML::_('grid.sort', 'COM_EASYBLOG_TEAMBLOGS_TEAM_NAME', 'a.title', $orderDirection, $order ); ?>
				</th>

				<?php if( !$browse ){ ?>
				<th width="1%" class="center nowrap">
					<?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>
				</th>
				<?php } ?>

				<th width="15%" class="center">
					<?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_ACCESS' ); ?>
				</th>
				<th width="10%" class="center">
					<?php echo JText::_( 'COM_EASYBLOG_TEAMBLOGS_MEMBERS' ); ?>
				</th>
				<th width="5%" class="center">
					<?php echo JHTML::_('grid.sort', 'ID', 'a.id', $orderDirection, $order ); ?>
				</th>
			</thead>
			<tbody>
				<?php if( $teams ){ ?>
					<?php $i = 0; ?>
					<?php foreach ($teams as $team) { ?>
					<tr>
						<?php if( !$browse ){ ?>
						<td width="1%" class="center nowrap">
							<?php echo $this->html('grid.id', $i, $team->id); ?>
						</td>
						<?php } ?>

						<td>
							<?php if ($browse) { ?>
								<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $team->id;?>','<?php echo addslashes($this->escape($team->title));?>');">
							<?php } else {?>
								<a href="index.php?option=com_easyblog&view=teamblogs&layout=form&id=<?php echo $team->id;?>">
							<?php } ?><?php echo $team->title;?></a>
						</td>

						<?php if( !$browse ){ ?>
						<td class="center nowrap">
							<?php echo $this->html('grid.published', $team, 'teamblogs', 'published'); ?>
						</td>
						<?php } ?>

						<td class="center">
							<?php if ($team->access == EBLOG_TEAMBLOG_ACCESS_MEMBER) { ?>
								<?php echo JText::_('COM_EASYBLOG_TEAM_MEMBER_ONLY');?>
							<?php } ?>

							<?php if ($team->access == EBLOG_TEAMBLOG_ACCESS_REGISTERED) { ?>
								<?php echo JText::_('COM_EASYBLOG_ALL_REGISTERED_USERS');?>
							<?php } ?>

							<?php if ($team->access == EBLOG_TEAMBLOG_ACCESS_EVERYONE) { ?>
								<?php echo JText::_('COM_EASYBLOG_EVERYONE'); ?>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo $team->getMembersCount();?>
						</td>
						<td class="center">
							<?php echo $team->id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr>
						<td colspan="6" class="empty">
							<?php echo JText::_('COM_EASYBLOG_NO_TEAM_BLOGS_CREATED_YET');?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">
						<?php echo $pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>

	<?php if ($browse) { ?>
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="browseFunction" value="<?php echo $browsefunction;?>" />
	<?php } ?>

	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="view" value="teamblogs" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
