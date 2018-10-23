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
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_DISPLAY_OPTIONS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DISPLAY_NAME_FORMAT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.displayName', $this->config->get('users.displayName'), array(
							array('value' => 'username', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_USERNAME'),
							array('value' => 'realname', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_REAL_NAME')
						));?>
					</div>
				</div>


				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_ADVANCED_SEARCH_SORTING'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.advancedsearch.sorting', $this->config->get('users.advancedsearch.sorting'), array(
							array('value' => 'default', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_DEFAULT'),
							array('value' => 'lastvisitDate', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_RECENT_LOGGED_IN'),
							array('value' => 'registerDate', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_RECENT_JOINED'),
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_INCLUDE_SITE_ADMINISTRATORS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.listings.admin', $this->config->get('users.listings.admin')); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DEFAULT_SORTING_METHOD'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.listings.sorting', $this->config->get('users.listings.sorting'), array(
							array('value' => 'latest', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_LATEST'),
							array('value' => 'alphabetical', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_ALPHABETICALLY'),
							array('value' => 'lastlogin', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_LASTLOGIN')
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_SETTINGS_PROFILES_COUNT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.listings.profilescount', $this->config->get('users.listings.profilescount')); ?>

					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_SETTINGS_ONLINE_STATE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.online.state', $this->config->get('users.online.state')); ?>

					</div>
				</div>


				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_INCLUDE_SITE_ADMINISTRATORS_IN_LEADERBOARD'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'leaderboard.listings.admin', $this->config->get('leaderboard.listings.admin')); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DEFAULT_START_ITEM'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.dashboard.start', $this->config->get('users.dashboard.start'), array(
							array('value' => 'me', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_ME_AND_FRIENDS'),
							array('value' => 'everyone', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_EVERYONE'),
							array('value' => 'following', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_FOLLOWING'),
						)); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_DASHBOARD_SIDEBAR'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.dashboard.sidebar', $this->config->get('users.dashboard.sidebar'), array(
							array('value' => 'hidden', 'text' => 'COM_ES_HIDDEN'),
							array('value' => 'left', 'text' => 'COM_ES_LEFT'),
							array('value' => 'right', 'text' => 'COM_ES_RIGHT')
						));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_SETTINGS_DASHBOARD_FOR_GUEST'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.guest', $this->config->get('users.dashboard.guest'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_SHOW_EVERYONE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.everyone', $this->config->get('users.dashboard.everyone'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_SHOW_APP_FILTERS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.appfilters', $this->config->get('users.dashboard.appfilters'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_SHOW_CUSTOM_FILTERS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.customfilters', $this->config->get('users.dashboard.customfilters'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_SHOW_GROUPS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.groups', $this->config->get('users.dashboard.groups'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_GROUPS_LIMIT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.inputbox', 'users.dashboard.groupslimit', $this->config->get('users.dashboard.groupslimit'), '', array('class' => 'input-short text-center'));?>
						<?php echo JText::_('COM_EASYSOCIAL_GROUPS'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_SHOW_EVENTS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.events', $this->config->get('users.dashboard.events'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_EVENTS_LIMIT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.inputbox', 'users.dashboard.eventslimit', $this->config->get('users.dashboard.eventslimit'), '', array('class' => 'input-short text-center'));?>
						<?php echo JText::_('COM_EASYSOCIAL_EVENTS'); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_SHOW_PAGES'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.dashboard.pages', $this->config->get('users.dashboard.pages'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_DASHBOARD_PAGES_LIMIT'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.inputbox', 'users.dashboard.pageslimit', $this->config->get('users.dashboard.pageslimit'), '', array('class' => 'input-short text-center'));?>
						<?php echo JText::_('COM_EASYSOCIAL_PAGES'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ES_TEXT_BASED_AVATARS'); ?>

			<div class="panel-body">
				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_NAMED_BASED_PROFILE_PICTURES'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.avatarUseName', $this->config->get('users.avatarUseName'), array()); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_NAMED_BASED_BACKGROUND_COLOURS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'users.avatarColors', 'users.avatarColors', $this->config->get('users.avatarColors'), array()); ?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_NAMED_BASED_FONT_COLOUR'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.text', 'users.avatarFontColor', 'users.avatarFontColor', $this->config->get('users.avatarFontColor'), array()); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYSOCIAL_USERS_SETTINGS_PROFILE'); ?>

			<div class="panel-body">

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_SETTINGS_DEFAULT_AVATAR'); ?>

					<div class="col-md-7">
						<div class="mb-20">
							<div class="es-img-holder">
								<div class="es-img-holder__remove <?php echo !ES::hasOverride('user_avatar') ? 't-hidden' : '';?>">
									<a href="javascript:void(0);" data-image-restore data-type="user_avatar">
										<i class="fa fa-times"></i>
									</a>
								</div>
								<img src="<?php echo ES::getDefaultAvatar('user', 'medium'); ?>" width="64" height="64" data-image-source data-default="<?php echo ES::getDefaultAvatar('user', 'medium', true);?>" />
							</div>
						</div>
						<div style="clear:both;" class="t-lg-mb--xl">
							<input type="file" name="user_avatar" id="user_avatar" class="input" style="width:265px;" data-uniform />
						</div>

						<br />

						<div class="help-block">
							<?php echo JText::_('COM_ES_SETTINGS_DEFAULT_AVATAR_SIZE_NOTICE'); ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_SETTINGS_DEFAULT_COVER'); ?>

					<div class="col-md-7">
						<div class="mb-20">
							<div class="es-img-holder">
								<div class="es-img-holder__remove <?php echo !ES::hasOverride('user_cover') ? 't-hidden' : '';?>">
									<a href="javascript:void(0);" data-image-restore data-type="user_cover">
										<i class="fa fa-times"></i>
									</a>
								</div>
								<img src="<?php echo ES::getDefaultCover('user'); ?>" width="256" height="98" data-image-source data-default="<?php echo ES::getDefaultCover('user', true);?>" />
							</div>
						</div>

						<div style="clear:both;" class="t-lg-mb--xl">
							<input type="file" name="user_cover" id="user_cover" class="input" style="width:265px;" data-uniform />
						</div>

						<br />

						<div class="help-block">
							<?php echo JText::_('COM_ES_SETTINGS_DEFAULT_COVER_SIZE_NOTICE'); ?>
						</div>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_USERS_SETTINGS_PROFILE_DEFAULT_DISPLAY'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.profile.display', $this->config->get('users.profile.display'), array(
							array('value' => 'timeline', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_PROFILE_DISPLAY_TIMELINE'),
							array('value' => 'about', 'text' => 'COM_EASYSOCIAL_USERS_SETTINGS_PROFILE_DISPLAY_ABOUT')
						));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_PROFILE_SIDEBAR'); ?>

					<div class="col-md-7">
						<?php echo $this->html('grid.selectlist', 'users.profile.sidebar', $this->config->get('users.profile.sidebar'), array(
							array('value' => 'hidden', 'text' => 'COM_ES_HIDDEN'),
							array('value' => 'left', 'text' => 'COM_ES_LEFT'),
							array('value' => 'right', 'text' => 'COM_ES_RIGHT')
						));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DISPLAY_PROFILE_COVER'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.cover', $this->config->get('users.layout.cover'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_DISPLAY_PROFILE_TITLE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.profiletitle', $this->config->get('users.layout.profiletitle'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_USERS_SETTINGS_DISPLAY_LASTONLINE_TITLE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.lastonline', $this->config->get('users.layout.lastonline'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DISPLAY_BADGES'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.badges', $this->config->get('users.layout.badges'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DISPLAY_AGE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.age', $this->config->get('users.layout.age'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DISPLAY_ADDRESS'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.address', $this->config->get('users.layout.address'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_FIELD_DISPLAY_GENDER'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.gender', $this->config->get('users.layout.gender'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_EASYSOCIAL_THEMES_WIREFRAME_DASHBOARD_SHOW_APP_BROWSE'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.apps', $this->config->get('users.layout.apps'));?>
					</div>
				</div>

				<div class="form-group">
					<?php echo $this->html('panel.label', 'COM_ES_DISPLAY_APPS_SIDEBAR'); ?>

					<div class="col-md-7">
						<?php echo $this->html('form.toggler', 'users.layout.sidebarapps', $this->config->get('users.layout.sidebarapps'));?>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
