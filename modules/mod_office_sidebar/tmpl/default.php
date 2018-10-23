<?php
/**
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="office-side-menu">
	<?php if ($params->get('show_easysocial_toolbar', true)) { ?>
	<div class="es-side-menu__title"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_MENU_TITLE'); ?></div>
		<ul class="es-side-menu-tabs">
			<?php if ($config->get('pages.enabled') && $loggedin) { ?>
			<li>
				<a href="<?php echo ESR::dashboard();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_DASHBOARD');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('pages.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::pages();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PAGES');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('groups.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::groups();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_GROUPS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('events.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::events();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_EVENTS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('friends.enabled') && $loggedin) { ?>
			<li>
				<a href="<?php echo ESR::friends();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_FRIENDS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('followers.enabled') && $loggedin) { ?>
			<li>
				<a href="<?php echo ESR::followers();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_FOLLOWERS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('video.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::videos();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_VIDEOS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('audio.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::audios();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_AUDIOS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('photos.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::albums();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_PHOTOS');?></a>
			</li>
			<?php } ?>

			<li>
				<a href="<?php echo ESR::users();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PEOPLE');?></a>
			</li>

			<?php if ($config->get('polls.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::polls();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_POLLS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('points.enabled') && $loggedin) { ?>
			<li>
				<a href="<?php echo ESR::leaderboard();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_LEADERBOARD');?></a>
			</li>
			<?php } ?>
		</ul>
	<?php } ?>

	<?php if ($params->get('show_account_settings', true)  && $loggedin) { ?>
	<div class="es-side-menu__title"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_HEADING_ACCOUNT'); ?></div>
		<ul class="es-side-menu-tabs">

			<li>
				<a href="<?php echo ESR::profile(array('layout' => 'edit'));?>"><?php echo JText::_('COM_EASYSOCIAL_PROFILE_UPDATE_PROFILE');?></a>
			</li>

			<?php if ($config->get('friends.invites.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::friends(array('layout' => 'invite'));?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('badges.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::badges(array('layout' => 'achievements'));?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACHIEVEMENTS');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('points.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::points(array('layout' => 'history' , 'userid' => $lib->my->getAlias()));?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_POINTS_HISTORY');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('conversations.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::conversations();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_CONVERSATIONS');?></a>
			</li>
			<?php } ?>
		</ul>
	<?php } ?>

	<?php if ($params->get('show_account_preferences', true)  && $loggedin) { ?>
	<div class="es-side-menu__title"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_HEADING_PREFERENCES'); ?></div>
		<ul class="es-side-menu-tabs">

			<?php if ($lib->my->hasCommunityAccess() && $config->get('privacy.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::profile(array('layout' => 'editPrivacy'));?>"><?php echo JText::_('COM_EASYSOCIAL_MANAGE_PRIVACY');?></a>
			</li>
			<?php } ?>

			<li>
				<a href="<?php echo ESR::profile(array('layout' => 'editNotifications'));?>"><?php echo JText::_('COM_EASYSOCIAL_MANAGE_ALERTS');?></a>
			</li>

			<?php if ($config->get('activity.logs.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::activities();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACTIVITIES');?></a>
			</li>
			<?php } ?>
		</ul>
	<?php } ?>

	<?php if ($params->get('show_account_discover', true)  && $loggedin) { ?>
	<div class="es-side-menu__title"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_DISCOVER'); ?></div>
		<ul class="es-side-menu-tabs">
			<li>
				<a href="<?php echo ESR::search(array('layout' => 'advanced'));?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ADVANCED_SEARCH');?></a>
			</li>

			<?php if ($config->get('apps.browser')) { ?>
			<li>
				<a href="<?php echo ESR::apps();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_APPS');?></a>
			</li>
			<?php } ?>
		</ul>	
	<?php } ?>
</div>