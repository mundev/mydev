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
<?php echo $this->output('site/blogs/latest/featured'); ?>

<div data-blog-listings>
	<div class="eb-posts <?php echo $this->isMobile() ? 'is-mobile' : '';?>" data-blog-posts>
		<?php echo $this->output('site/blogs/latest/posts', array('posts' => $posts, 'return' => $return, 'currentPageLink' => $currentPageLink)); ?>
	</div>

	<?php if ($pagination || $showLoadMore) { ?>
		<?php echo EB::renderModule('easyblog-before-pagination'); ?>

		<?php if (!$showLoadMore && $this->config->get('listing_pagination_style') != 'autoload') { ?>
			<?php echo $pagination;?>
		<?php } else if ($showLoadMore) { ?>
		<div>
			<a class="btn btn-default btn-block" href="javascript:void(0);" data-eb-pagination-loadmore data-limitstart="<?php echo $limitstart; ?>">
				<i class="fa fa-refresh"></i>&nbsp;<?php echo JText::_('COM_EB_LOADMORE'); ?>
			</a>
		</div>
		<?php } ?>

		<?php echo EB::renderModule('easyblog-after-pagination'); ?>
	<?php } ?>
</div>