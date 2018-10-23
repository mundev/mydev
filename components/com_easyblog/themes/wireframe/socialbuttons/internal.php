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
<div class="eb-shares <?php echo $this->config->get('social_button_internal_size') == 'small' ? 'eb-shares--without-name' : '';?> eb-shares--without-counter mt-20" data-eb-bookmarks>
	<?php foreach ($buttons as $button) { ?>
		<a href="javascript:void(0);" class="btn btn-eb--<?php echo $button->getName();?>" data-bookmarks-button data-url="<?php echo $button->getPermalink();?>">
			<i class="<?php echo $button->getIcon();?>"></i>
			<span class="eb-shares__name"><?php echo $button->getTitle();?></span>
		</a>
	<?php } ?>
</div>
