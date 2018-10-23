<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

if ($this->my->isSiteAdmin() && $user->isBlock()) {
	ES::info()->set('', JText::_('COM_EASYSOCIAL_PROFILE_USER_IS_BANNED'), "es-user-banned alert alert-danger");
}

?>
<div class="es-profile userProfile" data-id="<?php echo $user->id;?>" data-es-profile>

	<?php echo $this->render('widgets', 'user', 'profile', 'aboveHeader', array($user)); ?>

	<?php echo $this->render('module', 'es-profile-about-before-header'); ?>

	<?php echo $this->render('module', 'es-profile-about-after-header'); ?>

	<?php echo $this->html('responsive.toggle'); ?>

	<div class="es-container" data-es-container>
		<div class="es-sidebar" data-sidebar>
			<?php echo $this->render('module', 'es-profile-about-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php echo $this->output('site/profile/about/stats', array('user' => $user)); ?>

			<?php echo $this->render('module', 'es-profile-about-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>


		<div class="es-content">

			<?php echo $this->html('cover.user', $user, 'about'); ?>
			
			<div class="es-profile-info">
				<?php echo $this->output('site/fields/about/default', array('steps' => $steps, 'canEdit' => $user->id == $this->my->id, 'routerType' => 'profile')); ?>
			</div>

			<?php echo $this->render('module', 'es-profile-about-after-contents'); ?>
		</div>
	</div>
</div>
