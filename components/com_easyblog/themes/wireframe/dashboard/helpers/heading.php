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
<?php
	// do not show the info here as affix sticky will cause this info to 'float' #289
	//echo $info->html();
?>

<div class="eb-head">
	<h2 class="eb-head-title reset-heading pull-left">
		<span><i class="<?php echo $icon;?>"></i></span>&nbsp; <?php echo $title;?>
	</h2>

	<?php if ($action) { ?>
	<div class="eb-head-form form-inline pull-right">
		<a href="<?php echo $action->link;?>" class="btn btn-primary">
			<?php if ($action->icon) { ?>
			<i class="<?php echo $action->icon;?>"></i>&nbsp;
			<?php } ?>
			<?php echo $action->text;?>
		</a>
	</div>
	<?php } ?>
</div>
