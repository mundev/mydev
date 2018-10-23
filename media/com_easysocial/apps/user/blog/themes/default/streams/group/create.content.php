<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="stream-blog">
	<div class="media">
		<div class="media-body">
			<h4 class="blog-title">
				<a href="<?php echo $post->getPermalink();?>" class="blog-title"><?php echo $post->title; ?></a>
			</h4>
			
			<div class="blog-meta">
				<?php echo JText::sprintf('APP_USER_BLOG_STREAM_META', '<a href="' . $post->getPrimaryCategory()->getPermalink() . '">' . JText::_($post->getPrimaryCategory()->title) . '</a>', $post->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'))); ?>
			</div>

			<p class="blog-description clearfix">
				<?php if ($post->hasImage()) { ?>
					<a href="<?php echo $post->getPermalink();?>" class="blog-image <?php echo $alignment;?>">
						<img src="<?php echo $post->getImage('thumbnail');?>" align="right" width="96" />
					</a>
				<?php } ?>
				
				<?php echo strip_tags($content); ?>
			</p>

			<div>
				<a href="<?php echo $post->getPermalink();?>" class="mt-5"><?php echo JText::_('APP_USER_BLOG_CONTINUE_READING'); ?> &rarr;</a>
			</div>
		</div>
	</div>
</div>
