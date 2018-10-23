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
<div class="eb-search">
	<form class="form-horizontal" action="<?php echo JRoute::_('index.php');?>" method="post">
		<div class="eb-search__wrapper row-table">
			<div class="eb-search__item col-cell">
				<div class="input-group">
					<input type="text" class="form-control" name="query" value="<?php echo $this->html('string.escape', $query);?>" />
					<div class="input-group-btn">
						<button class="btn btn-default" type="submit">
							<i class="fa fa-search"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON'); ?>
						</button>
					</div>
				</div>
			</div>
			<div class="eb-search__filter col-cell">
				<div class="eb-filter-select-group">
					<?php echo $categoryDropdown; ?>
					<div class="eb-filter-select-group__drop"></div>
				</div>
			</div>
		</div>

		<br />

		<?php if ($query) { ?>
		<div class="form-control-static <?php echo !$posts ? ' eb-empty' : '';?>">

			<?php if ($posts) { ?>
				<?php echo JText::sprintf('COM_EASYBLOG_SEARCH_RESULTS_TOTAL_RESULT', $pagination->get('pages.current'), $pagination->get('pages.total'), $pagination->get('total'), $query); ?>
			<?php } else { ?>
				<?php echo JText::sprintf('COM_EASYBLOG_SEARCH_RESULTS_EMPTY', $query); ?>
			<?php } ?>

		</div>
		<?php } ?>

		<?php echo $this->html('form.action', 'search.query'); ?>
	</form>
</div>

<hr />

<?php if ($posts) { ?>
<div class="eb-posts eb-posts-search">
	<?php foreach ($posts as $post) { ?>
	<div class="eb-post clearfix">
		<div class="eb-post-content">
			<h2 class="eb-post-title reset-heading">
				<a href="<?php echo $post->getPermalink();?>" class="text-inherit"><?php echo $post->title;?></a>
			</h2>

			<div class="eb-post-article">
				<?php echo $post->content;?>
			</div>

			<div class="eb-post-meta text-muted">
				<div class="eb-post-date">
					<i class="fa fa-clock-o"></i>
					<time><?php echo $this->html('string.date', $post->created, JText::_('DATE_FORMAT_LC'));?></time>
				</div>

				<div class="eb-post-author">
					<i class="fa fa-user"></i>
					<a href="<?php echo $post->getAuthor()->getPermalink();?>"><?php echo $post->getAuthor()->getName(); ?></a>
				</div>

				<?php foreach ($post->categories as $category) { ?>
				<div>
					<div class="eb-post-category comma-seperator">
						<i class="fa fa-folder-open"></i>

						<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<div class="eb-pagination">
	<div>
		<?php echo $pagination->getPagesLinks();?>
	</div>
</div>
<?php } ?>
