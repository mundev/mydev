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
<div class="es-stream-apps type-blog">
    <div class="es-stream-apps__hd">
        <a href="<?php echo $blog->getPermalink();?>" class="es-stream-apps__title">
            <?php echo $blog->title; ?>
        </a>

        <div class="es-stream-apps__meta t-fs--sm">
        	<ul class="g-list-inline g-list-inline--dashed">
        		<li>
					<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $blog->getPrimaryCategory()->getPermalink();?>"><?php echo JText::_($blog->getPrimaryCategory()->title);?></a>
				</li>
				<li>
					<i class="fa fa-calendar"></i>&nbsp; <?php echo $blog->getCreationDate()->format(JText::_('DATE_FORMAT_LC1'));?>
				</li>
			</ul>
        </div>
    </div>

    <div class="es-stream-apps__bd es-stream-apps--border">
        <div class="es-stream-apps__desc">
            <?php echo $this->html('string.truncater', $comment->comment, 300); ?>
        </div>

        <ol class="g-list--horizontal has-dividers--right">
            <li class="g-list__item">
                <a href="<?php echo $blog->getPermalink();?>" class="mt-5"><?php echo JText::_('APP_BLOG_VIEW_POST'); ?> &rarr;</a>
            </li>
        </ol>
    </div>
</div>