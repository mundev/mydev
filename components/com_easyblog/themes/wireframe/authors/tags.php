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
<?php $i = 0; ?>
<?php foreach ($tags as $tag) { ?>
<a class="btn btn-default<?php echo $i >= $limit ? ' hide' : '';?>" href="<?php echo $tag->getPermalink(); ?>" data-tag-item>
	<i class="fa fa-tag text-muted"></i> &nbsp; <?php echo $tag->getTitle(); ?>
</a>
<?php $i++; ?>
<?php } ?>
<?php if (count($tags) > $limit) { ?>
	<a href="javascript:void(0);" class="btn btn-default btn-block btn-show-all mt-10" data-show-all data-type="tag">
		<?php echo JText::_('COM_EB_VIEW_ALL_TAGS'); ?>&nbsp;<i class="fa fa-chevron-right"></i>
	</a>
<?php } ?>
