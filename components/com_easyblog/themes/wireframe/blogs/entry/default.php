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
<div data-eb-posts>
	<div data-eb-posts-wrapper>
	<?php if (isset($output) && $output) { ?>
		<?php echo $output; ?>
	<?php } else { ?>
		<?php echo $this->output('site/blogs/entry/default.posts'); ?>
	<?php } ?>
	</div>

	<?php if ($prevId) { ?>
	<div>
		<a class="btn btn-default btn-block" href="javascript:void(0);" data-eb-pagination-loadmore data-post-id="<?php echo $prevId; ?>">
			<i class="fa fa-refresh"></i>&nbsp;<?php echo JText::_('COM_EB_LOADMORE'); ?>
		</a>
	</div>
	<input type="hidden" name="pagination_exclude" data-eb-pagination-exclusion value="<?php echo $exclude; ?>" />
<?php } ?>
</div>