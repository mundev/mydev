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

<div class="eb-mag eb-mag-side-list">
	<h6 class="eb-mag-header-title">
		<?php echo JText::_('COM_EASYBLOG_RECENT_NEWS'); ?>
	</h6>
	<div class="eb-mag-container">
		<?php if ($leadingArticle) { ?>
		<div class="eb-mag-content">
			<div class="eb-mag-post">
				<div class="eb-mag-head">
					<a class="eb-mag-blog-image" href="<?php echo $leadingArticle->getPermalink(); ?>" style="background-image: url('<?php echo $leadingArticle->getImage('large');?>');"></a>
				</div>
				<div class="eb-mag-body">
					<h1 class="eb-mag-post-title">
						<a href="<?php echo $leadingArticle->getPermalink(); ?>"><?php echo $leadingArticle->title; ?></a>
					</h1>
					<p><?php echo $leadingArticle->getIntro();?></p>

					<?php if ($this->params->get('magazine_leading_article_readmore', true)) { ?>
						<a class="magazine-btn magazine-btn-more" href="<?php echo $leadingArticle->getPermalink();?>"><?php echo JText::_('COM_EB_CONTINUE_READING');?></a>
					<?php } ?>
				</div>
				<div class="eb-mag-date">
					<time class="eb-mag-meta-date">
						<?php echo $leadingArticle->getDisplayDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
					</time>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if ($posts) { ?>
		<div class="eb-mag-side">
			<?php foreach ($posts as $post) { ?>
			<div class="eb-mag-table eb-mag-cell-top" itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting">
				<div class="eb-mag-cell">

					<?php if (!$this->params->get('magazine_hide_cover', true)) { ?>
					<div class="eb-mag-thumb">
						<a class="eb-mag-blog-image" href="<?php echo $post->getPermalink(); ?>" style="background-image: url('<?php echo $post->getImage('medium');?>');"></a>
					</div>
					<?php } ?>
				</div>
				<div class="eb-mag-cell">
					<div class="eb-mag-title" itemprop="name headline">
						<a href="<?php echo $post->getPermalink(); ?>"><?php echo $post->title; ?></a>

						<?php if ($this->params->get('list_article_readmore', true)) { ?>
						<div class="">
							<a class="magazine-btn magazine-btn-more" href="<?php echo $post->getPermalink();?>"><?php echo JText::_('COM_EB_CONTINUE_READING');?></a>
						</div>
						<?php } ?>
					</div>
					<div class="eb-mag-foot">
						<time class="eb-mag-meta-date" itemprop="datePublished" content="<?php echo $post->getDisplayDate($this->params->get('post_date_source', 'created'))->format(JText::_('DATE_FORMAT_LC4'));?>">
							<?php echo $post->getDisplayDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
						</time>
					</div>
				</div>
			</div>
			<?php } ?>

			<div class="eb-more">
				<a href="<?php echo $viewAll;?>" class="eb-more__btn"><?php echo JTexT::_('COM_EASYBLOG_VIEW_ALL_POSTS');?> <i class="fa fa-chevron-right"></i></a>
			</div>

			<meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="https://google.com/article"/>

			<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject" class="hidden">
				<img src="<?php echo $post->getImage($this->config->get('cover_size', 'large'));?>" alt="<?php echo $this->html('string.escape', $post->getImageTitle());?>"/>
				<meta itemprop="url" content="<?php echo $post->getImage($this->config->get('cover_size', 'large'), true, true);?>">
				<meta itemprop="width" content="<?php echo $this->config->get('cover_width');?>">
				<meta itemprop="height" content="<?php echo $this->config->get('cover_height');?>">
			</div>

			<meta itemprop="dateModified" content="<?php echo $post->getFormDateValue('modified');?>"/>
		</div>
		<?php } ?>
	</div>
</div>