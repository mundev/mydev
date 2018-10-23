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
	<p class="mb-10 mt-10 comment-description"><?php echo $comment->comment; ?></p>

	<a href="<?php echo $permalink;?>#comments"><?php echo JText::_('APP_USER_BLOG_VIEW_BLOG_POST'); ?> &rarr;</a>
</div>
