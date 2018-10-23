<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->render('module', 'es-user-before-cover'); ?>

<div class="es-profile-header t-lg-mb--lg" data-profile-header data-id="<?php echo $user->id;?>" data-name="<?php echo $this->html('string.escape' , $user->getName());?>" data-avatar="<?php echo $user->getAvatar();?>">
	<div class="es-profile-header__hd <?php echo $this->config->get('users.layout.cover') ? ' with-cover' : ' without-cover';?>">
		<?php if ($this->config->get('users.layout.cover') && (!isset($showCover) || $showCover)) { ?>
		<div
			<?php if ($cover->photo_id && $cover->getPhoto()->album_id) { ?>
			data-es-photo-group="album:<?php echo $cover->getPhoto()->album_id;?>"
			<?php } ?>
		>
			<div data-profile-cover
				<?php echo $cover->photo_id ? 'data-es-photo="' . $cover->photo_id . '"' : '';?>
				 class="es-profile-header__cover es-flyout <?php echo $user->hasCover() ? 'has-cover' : 'no-cover'; ?> <?php echo !empty($newCover) ? "editing" : ""; ?> <?php echo $user->id == $this->my->id ? 'is-owner' : '';?>"
				 style="background-image   : url(<?php echo $cover->getSource();?>);background-position: <?php echo $cover->getPosition();?>;">

				<div class="es-cover-container">
					<div class="es-cover-viewport">
						<div
							data-cover-image
							class="es-cover-image"
							<?php if (!empty($newCover)) { ?>
							data-photo-id="<?php echo $newCover->id; ?>"
							style="background-image: url(<?php echo $newCover->getSource('large'); ?>);"
							<?php } ?>

							<?php if ($cover->id) { ?>
							data-photo-id="<?php echo $cover->getPhoto()->id; ?>"
							style="background-image: url(<?php echo $cover->getSource(); ?>);"
							<?php } ?>
						>
						</div>

						<div class="es-cover-hint">
							<span>
								<span class="o-loader o-loader--sm o-loader--inline with-text"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_COVER_LOADING'); ?></span>
								<span class="es-cover-hint-text"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_COVER_DRAG_HINT'); ?></span>
							</span>
						</div>

						<div class="es-cover-loading-overlay"></div>

						<?php if( $user->id == $this->my->id ){ ?>
						<div class="es-flyout-content">


							<div class="dropdown_ pull-right es-cover-menu" data-cover-menu>
								<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ es-flyout-button">
									<?php echo JText::_('COM_EASYSOCIAL_PHOTOS_EDIT_COVER');?>
								</a>
								<ul class="dropdown-menu">
									<li data-cover-upload-button>
										<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_UPLOAD_COVER"); ?></a>
									</li>
									<li data-cover-select-button>
										<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_SELECT_COVER'); ?></a>
									</li>
									<li data-cover-edit-button>
										<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_REPOSITION_COVER'); ?></a>
									</li>
									<li class="divider for-cover-remove-button"></li>
									<li data-cover-remove-button>
										<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_REMOVE_COVER"); ?></a>
									</li>
								</ul>
							</div>

							<a href="javascript:void(0);" class="es-cover-done-button es-flyout-button" data-cover-done-button>
								<i class="fa fa-check"></i>&nbsp; <?php echo JText::_("COM_EASYSOCIAL_PHOTOS_COVER_DONE"); ?>
							</a>

							<a href="javascript:void(0);" class="es-cover-cancel-button es-flyout-button" data-cover-cancel-button>
								<i class="fa fa-remove"></i>&nbsp; <?php echo JText::_("COM_ES_CANCEL"); ?>
							</a>
						</div>

						<div class="es-cover-desktop-hint">
								<a href="javascript:void(0);" class="es-cover-desktop-hint__cancel" data-cover-cancel-button>
									<i class="fa fa-remove"></i>&nbsp; <?php echo JText::_("COM_ES_CANCEL"); ?>
								</a>
								<div class="es-cover-desktop-hint__content">
									<?php echo JText::_('COM_EASYSOCIAL_PHOTOS_COVER_DRAG_HINT'); ?>
								</div>
								<a href="javascript:void(0);" class="es-cover-desktop-hint__save" data-cover-done-button>
									<i class="fa fa-check"></i>&nbsp; <?php echo JText::_("COM_EASYSOCIAL_PHOTOS_COVER_DONE"); ?>
								</a>
							
						</div>
						<div class="es-cover-desktop-action">
							<div class="es-cover-desktop-action__update">
								<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-cover-edit-button><?php echo JText::_('COM_ES_UPDATE_COVER');?></a>
							</div>
							<div class="es-cover-desktop-action__trigger">
								<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-cover-upload-button>
									<?php echo JText::_("COM_EASYSOCIAL_PHOTOS_UPLOAD_COVER"); ?>
								</a>
								<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-cover-select-button>
									<?php echo JText::_('COM_EASYSOCIAL_PHOTOS_SELECT_COVER'); ?>
								</a>
								
								
								<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-cover-remove-button>
									<?php echo JText::_("COM_EASYSOCIAL_PHOTOS_REMOVE_COVER"); ?>
								</a>
							</div>
						</div>

						<?php } ?>

						
					</div>
				</div>
			</div>
		</div>
		<?php } ?>

		<div class="es-profile-header__avatar-wrap es-flyout" data-profile-avatar
			<?php if ($showAvatar) { ?>
			data-es-photo-group="album:<?php echo $user->getAvatarPhoto()->album_id;?>"
			<?php } ?>
		>
			<a href="<?php echo $user->getAvatarPhoto() ? 'javascript:void(0);' : $user->getPermalink();?>" class=""
				<?php if ($showAvatar && $showPhotoPopup) { ?>
				data-es-photo="<?php echo $user->getAvatarPhoto()->id;?>"
				<?php } ?>
			>
				<img src="<?php echo $user->getAvatar(SOCIAL_AVATAR_SQUARE);?>" alt="<?php echo $this->html('string.escape' , $user->getName() );?>" data-avatar-image />

				<?php if ($showOnlineState) { ?>
				<?php echo $this->loadTemplate('site/utilities/user.online.state', array('online' => $user->isOnline(), 'size' => 'small')); ?>
				<?php } ?>
			</a>

			<?php if ($user->isViewer()) { ?>
			<div class="es-flyout-content">
				<div class="dropdown_ es-avatar-menu" data-avatar-menu>
					<a href="javascript:void(0);"
					   class="es-flyout-button dropdown-toggle_"
					   data-bs-toggle="dropdown"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_EDIT_AVATAR');?></a>
					<ul class="dropdown-menu">
						<li data-avatar-upload-button>
							<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_UPLOAD_AVATAR"); ?></a>
						</li>

						<li data-avatar-select-button>
							<a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_SELECT_AVATAR'); ?></a>
						</li>

						<?php if ($this->config->get('users.avatarWebcam') && !$this->isMobile()) { ?>
						<li class="divider"></li>
						<li data-avatar-webcam>
							<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_TAKE_PHOTO"); ?></a>
						</li>
						<?php } ?>

						<?php if ($user->hasAvatar()) { ?>
						<li class="divider"></li>
						<li data-avatar-remove-button>
							<a href="javascript:void(0);">
								<?php echo JText::_("COM_EASYSOCIAL_PHOTOS_REMOVE_AVATAR"); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>

			<?php echo $this->render('module', 'es-profile-avatar'); ?>
		</div>

		<?php echo $this->render('widgets', 'user', 'profile', 'afterAvatar', array($user)); ?>
	</div>

	<div class="es-profile-header__bd">
		<div class="es-profile-header__info-wrap">
			<?php echo $this->render('module', 'es-profile-before-name'); ?>
			<?php echo $this->render('widgets', 'user', 'profile', 'beforeName' , array($user)); ?>

			<h1 class="es-profile-header__title">
				<?php echo $this->html('html.user', $user); ?>
			</h1>

			<?php echo $this->render('widgets', 'user', 'profile', 'afterName', array($user)); ?>
			<?php echo $this->render('module', 'es-profile-after-name'); ?>

			<div class="es-profile-header__meta">
				<?php if ($this->config->get('users.layout.profiletitle')) { ?>
				<span>
					<a href="<?php echo $user->getProfile()->getPermalink();?>" >
						<i class="fa fa-shield"></i>&nbsp; <?php echo $user->getProfile()->get('title');?>
					</a>
				</span>
				<?php } ?>

				<?php if ($this->config->get('users.layout.lastonline')) { ?>
					<?php if ($user->getLastVisitDate() == '0000-00-00 00:00:00') { ?>
						<span>
							<?php echo JText::_('COM_EASYSOCIAL_USER_NEVER_LOGGED_IN');?>
						</span>
					<?php } elseif (!$user->isOnline()) { ?>
						<span>
							<?php echo JText::_('COM_EASYSOCIAL_PROFILE_LAST_SEEN');?>, <strong><?php echo $user->getLastVisitDate('lapsed'); ?></strong>
						</span>
					<?php } ?>
				<?php } ?>
			</div>

			<div class="es-profile-header__bd-widget">
				<?php echo $this->render('widgets', 'user', 'profile', 'afterprofile', array($user)); ?>
			</div>

			<?php if ($this->config->get('users.layout.badges') && $badges && $user->badgesViewable($this->my->id)) { ?>
			<div class="es-profile-header__badges">
				<?php foreach ($badges as $badge) { ?>
				<a data-original-title="<?php echo $badge->getTitle(); ?>" data-placement="top" data-es-provide="tooltip" class="badge-link" href="<?php echo $badge->getPermalink(); ?>">
					<img src="<?php echo $badge->getAvatar(); ?>" alt="<?php echo $badge->getTitle(); ?>">
				</a>
				<?php } ?>
			</div>
			<?php } ?>

		</div>

		<div class="es-profile-header__action-wrap">

			<?php echo $this->render('widgets', 'user', 'profile', 'beforeActions', array($user)); ?>
			<?php echo $this->render('module', 'es-profile-before-actions'); ?>


			<div class="btn-toolbar" role="toolbar">

				<?php if (!$user->isViewer()) { ?>
				<div class="btn-group btn-group--viewer" role="group">
					<?php echo $this->html('user.friends', $user); ?>

					<?php echo $this->html('user.subscribe', $user); ?>

					<?php if ($this->my->getPrivacy()->validate('profiles.post.message', $user->id, SOCIAL_TYPE_USER)) { ?>
						<?php echo $this->html('user.conversation', $user); ?>
					<?php } ?>
				</div>
				<?php } ?>

				<div class="btn-group">
					<?php echo $this->html('user.bookmark', $user); ?>
				</div>

				<?php echo $this->html('user.actions', $user); ?>
			</div>

			<?php echo $this->render('module', 'es-profile-after-actions'); ?>
			<?php echo $this->render('widgets', 'user', 'profile', 'afterActions' , array($user)); ?>
		</div>
	</div>
	
	<div class="es-profile-header-nav">
		<div class="es-profile-header-nav__item <?php echo $active == 'timeline' ? 'is-active' : '';?>">
			<a href="<?php echo $timelinePermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_TIMELINE');?></span></a>
		</div>
		
		<div class="es-profile-header-nav__item <?php echo $active == 'about' ? 'is-active' : '';?>">
			<a href="<?php echo $aboutPermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_ABOUT');?></span></a>
		</div>

		<?php if ($this->config->get('friends.enabled')) { ?>
		<div class="es-profile-header-nav__item <?php echo $active == 'friends' ? 'is-active' : '';?> <?php echo $pendingFriends > 0 ? 'has-notice' : '';?>">
			<a href="<?php echo ESR::friends(array('userid' => $user->getAlias()));?>" class="es-profile-header-nav__link">
				<span><?php echo JText::_('COM_EASYSOCIAL_FRIENDS');?></span>
				<span class="es-profile-header-nav__link-bubble"></span>
			</a>
		</div>
		<?php } ?>

		

		<?php if (!$this->isMobile()) { ?>

			<?php if ($user->canCreateAlbums()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'albums' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::albums(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_ALBUMS');?></span></a>
			</div>
			<?php } ?>

			<?php if ($user->canCreateVideos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'videos' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::videos(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS');?></span></a>
			</div>
			<?php } ?>

			<?php if ($user->canCreateAudios()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'audios' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::audios(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_AUDIOS');?></span></a>
			</div>
			<?php } ?>

			<?php if ($showMore || $showBrowseApps) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
				<div class="btn-group">
					<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
					<span data-text=""><?php echo JText::_('COM_ES_MORE');?></span>
					&nbsp;<i class="i-chevron i-chevron--down"></i>

					</a>
					<ul class="dropdown-menu dropdown-menu-right">

						<?php if ($appsDropdown && $this->config->get('users.layout.sidebarapps')) { ?>
							<?php foreach ($appsDropdown as $app) { ?>
							<li class="<?php echo $active == $app->active ? 'is-active' : '';?>">
								<a href="<?php echo $app->permalink;?>" class="es-profile-header-nav__dropdown-link" title="<?php echo $app->pageTitle; ?>">
									<?php echo $app->title; ?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>

						<?php if ($user->isViewer() && $this->config->get('users.layout.apps')) { ?>
						<li class="divider"></li>
						<li>
							<a href="<?php echo ESR::apps();?>"><?php echo JText::_('COM_EASYSOCIAL_BROWSE'); ?></a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } else if ($appsDropdown && count($appsDropdown) == 1 && $this->config->get('users.layout.sidebarapps')) {  ?>
				<div class="es-profile-header-nav__item <?php echo $active == $appsDropdown[0]->active ? 'is-active' : '';?>">
					<a href="<?php echo $appsDropdown[0]->permalink;?>" class="es-profile-header-nav__link"><span><?php echo $appsDropdown[0]->title; ?></span></a>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if ($this->isMobile()) { ?>
			<?php if ($showMore || $user->canCreateAlbums() || $user->canCreateVideos() || $user->canCreateAudios()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
				<div class="btn-group">
					<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
					<span><?php echo JText::_('COM_ES_MORE');?></span>
					&nbsp;<i class="i-chevron i-chevron--down"></i>

					</a>
					<ul class="dropdown-menu dropdown-menu-right">

						<?php if ($user->canCreateAlbums()) { ?>
						<li class="<?php echo $active == 'albums' ? 'is-active' : '';?>">
							<a href="<?php echo ESR::albums(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_ALBUMS');?></a>
						</li>
						<?php } ?>

						<?php if ($user->canCreateVideos()) { ?>
						<li class="<?php echo $active == 'videos' ? 'is-active' : '';?>">
							<a href="<?php echo ESR::videos(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS');?></a>
						</li>
						<?php } ?>

						<?php if ($user->canCreateAudios()) { ?>
						<li class="<?php echo $active == 'audios' ? 'is-active' : '';?>">
							<a href="<?php echo ESR::audios(array('uid' => $user->getAlias(), 'type' => SOCIAL_TYPE_USER));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_AUDIOS');?></a>
						</li>
						<?php } ?>

						<?php if ($appsDropdown && $this->config->get('users.layout.sidebarapps')) { ?>
							<?php foreach ($appsDropdown as $app) { ?>
							<li class="<?php echo $active == $app->active ? 'is-active' : '';?>">
								<a href="<?php echo $app->permalink;?>" class="es-profile-header-nav__dropdown-link">
									<?php echo $app->title; ?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
	</div>
</div>

<?php echo $this->render('module', 'es-user-after-cover'); ?>
