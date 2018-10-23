<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="stream-discuss">
	<div class="media">
		<div class="media-body">
			<h4 class="blog-title">
				<a href="<?php echo $permalink;?>" class="blog-title"><?php echo $post->title;?></a>
			</h4>

			<div class="discuss-meta">
				<a href="<?php echo $catPermalink;?>"><?php echo $category->title;?></a>
			</div>

			<p class="discuss-description">
				<?php echo $post->content; ?>
			</p>

			<div>
				<a href="<?php echo $permalink;?>" class="mt-5"><?php echo JText::_('APP_EASYDISCUSS_STREAM_VIEW_POST'); ?> &rarr;</a>
			</div>
		</div>
	</div>
</div>
