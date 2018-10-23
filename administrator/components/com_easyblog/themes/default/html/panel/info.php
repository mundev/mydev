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
<p class="panel-info mb-20">
	<?php if ($image) { ?>
		<?php if ($link) { ?>
		<a href="<?php echo $link;?>" target="_blank">
		<?php } ?>
			<img src="<?php echo $image;?>" align="left" class="mr-20 mb-20" style="max-width: 120px;" />
		<?php if ($link) { ?>
		</a>
		<?php } ?>
	<?php } ?>

	<?php if ($link) { ?>
	<a href="<?php echo $link;?>" class="btn btn-primary btn-sm ml-10 pull-right" target="_blank"><?php echo $buttonText;?></a>
	<?php } ?>

	<?php echo $text;?>
</p>
