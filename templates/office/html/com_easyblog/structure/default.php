<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="eb" class="eb-component eb-<?php echo $this->theme;?> eb-view-<?php echo $view;?> eb-layout-<?php echo $layout;?> <?php echo $suffix;?> 
	<?php echo $this->isIphone() ? ' is-iphone' : '';?>
	<?php echo $this->isMobile() ? ' is-mobile' : '';?>
	<?php echo $rtl ? ' is-rtl' : '';?>"
>
	<?php echo $jsToolbar; ?>

	<?php echo $toolbar; ?>

	<?php echo EB::info()->html();?>

	<?php if ($loadImageTemplates) { ?>
		<?php echo $this->output('site/layout/image/popup'); ?>
		<?php echo $this->output('site/layout/image/container'); ?>
	<?php } ?>

	<?php echo $contents; ?>

	<?php if ($jscripts) { ?>
	<div>
		<?php echo $jscripts;?>
	</div>
	<?php } ?>
</div>