<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-group-category">
	<div class="es-container">
		<div class="es-sidebar" data-sidebar>

			<div data-dashboardSidebar-menu data-type="profile" data-id="<?php echo $profile->id;?>" class="active"></div>

			<?php echo $this->render('module', 'es-profiles-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php echo $this->includeTemplate('site/profiles/default/widgets/members'); ?>

			<hr class="es-hr" />

			<?php echo $this->includeTemplate('site/profiles/default/widgets/albums'); ?>

			<?php echo $this->render('module', 'es-profiles-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content">
			<div class="es-snackbar">
				<h1 class="es-snackbar__title"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_RECENT_UPDATES'); ?> - <?php echo $profile->get('title'); ?></h1>
			</div>

			<div class="es-content-wrap" data-es-group-item-content>
				<?php echo $stream->html();?>
			</div>
		</div>        
	</div>
</div>