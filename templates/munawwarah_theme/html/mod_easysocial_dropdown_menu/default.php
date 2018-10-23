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
<div id="es" class="mod-es mod-es-dropdown-menu <?php echo $lib->getSuffix();?>">
	<?php if ($lib->my->id) { ?>
	<div class="dropdown_">
		<div class="dropdown-toggle_ fd-cf" data-bs-toggle="dropdown">
			<div class="">
				<?php echo $lib->html('avatar.user', $lib->my, 'md', false, false); ?>
			</div>
		</div>

		<ul class="dropdown-menu dropdown-menu-right">
			<?php if ($params->get('show_my_profile', true)) { ?>
			<li>
				<a href="<?php echo $my->getPermalink();?>">
					<?php echo JText::_('MOD_EASYSOCIAL_DROPDOWN_MENU_MY_PROFILE');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($params->get('show_account_settings', true)) { ?>
			<li>
				<a href="<?php echo ESR::profile(array('layout' => 'edit'));?>">
					<?php echo JText::_('MOD_EASYSOCIAL_DROPDOWN_MENU_ACCOUNT_SETTINGS');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($config->get('friends.invites.enabled')) { ?>
			<li>
				<a href="<?php echo ESR::friends(array('layout' => 'invite'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_INVITE_FRIENDS');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($config->get('badges.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::badges(array('layout' => 'achievements'));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACHIEVEMENTS');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($config->get('points.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::points(array('layout' => 'history' , 'userid' => $my->getAlias()));?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_POINTS_HISTORY');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($config->get('conversations.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::conversations();?>">
					<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_CONVERSATIONS');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($items) { ?>
				<li class="divider"></li>
				<?php foreach ($items as $item) { ?>
				<li class="menu-<?php echo $item->id;?>" style="padding-left: <?php echo $item->padding; ?>px;">
					<a href="<?php echo $item->flink;?>"><?php echo $item->title;?></a>
				</li>
				<?php } ?>
			<?php } ?>

			<li class="divider"></li>

			<li>
				<a href="<?php echo ESR::search(array('layout' => 'advanced'));?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_ADVANCED_SEARCH');?></a>
			</li>

			<?php if ($config->get('points.enabled')){ ?>
			<li>
				<a href="<?php echo ESR::leaderboard();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_LEADERBOARD');?></a>
			</li>
			<?php } ?>

			<?php if ($config->get('apps.browser')) { ?>
			<li>
				<a href="<?php echo ESR::apps();?>"><?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_APPS');?></a>
			</li>
			<?php } ?>

			<?php if ($my->hasCommunityAccess()) { ?>
				<li class="divider"></li>
				<?php if ($config->get('privacy.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::profile(array('layout' => 'editPrivacy'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_MANAGE_PRIVACY');?>
					</a>
				</li>
				<?php } ?>
				<li>
					<a href="<?php echo ESR::profile(array('layout' => 'editNotifications'));?>">
						<?php echo JText::_('COM_EASYSOCIAL_MANAGE_ALERTS');?>
					</a>
				</li>
				<?php if ($config->get('activity.logs.enabled')) { ?>
				<li>
					<a href="<?php echo ESR::activities();?>">
						<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_PROFILE_ACTIVITIES');?>
					</a>
				</li>
				<?php } ?>
			<?php } ?>


			<?php if ($params->get('show_sign_out', true)) { ?>
			<li class="divider"></li>
			<li>
				<a href="javascript:void(0);" onclick="document.getElementById('es-dropdown-logout-form').submit();"><?php echo JText::_('MOD_EASYSOCIAL_DROPDOWN_MENU_SIGN_OUT');?></a>
				<form class="logout-form" id="es-dropdown-logout-form">
					<input type="hidden" name="return" value="<?php echo $logoutReturn;?>" />
					<input type="hidden" name="option" value="com_easysocial" />
					<input type="hidden" name="controller" value="account" />
					<input type="hidden" name="task" value="logout" />
					<?php echo $lib->html('form.token'); ?>
				</form>
			</li>
			<?php } ?>
		</ul>
	</div>
	<?php } else { ?>

		<a class="btn btn-link" href="<?php echo ESR::login(array(), false); ?>">
			<?php echo JText::_('COM_EASYSOCIAL_LOGIN_BUTTON'); ?>
		</a>

		<?php if ($params->get('register_button', true)) { ?>
		<a href="<?php echo ESR::registration(); ?>" class="btn btn-link btn-sm">
			<?php echo JText::_('MOD_EASYSOCIAL_DROPDOWN_MENU_REGISTER'); ?>
		</a>
		<?php } ?>
	<?php } ?>
</div>
