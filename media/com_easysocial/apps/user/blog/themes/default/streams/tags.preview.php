<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<li>
	<?php foreach ($tags as $tag) { ?>
	<a href="<?php echo ESR::dashboard(array('layout' => 'hashtag', 'tag' => $tag->title)); ?>" class="mentions-hashtag">#<?php echo $tag->title; ?></a><?php echo $currentTotal == $totalTags ? '' : ','; ?>
	<?php $currentTotal++; ?>
	<?php } ?>
</li>