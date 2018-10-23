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

// Since some of the links are hidden on the apps, we need to check if apps should be active
if (!$isAppActive) {

	$activeApps = array('discussions', 'news', 'files', 'polls', 'reviews', 'tasks', 'easydiscuss', 'blog');
	
	if (in_array($active, $activeApps)) {
		$isAppActive = true;
		$active = 'apps.' . $active;
	}

	// On mobile devices, we group up the audio, video and albums under the more dropdown
	if ($this->isMobile()) {
		$activeApps = array_merge($activeApps, array('videos', 'audios', 'albums'));
		if (in_array($active, $activeApps)) {
			$isAppActive = true;
			$active = 'apps.' . $active;
		}
	}
}
?>
<div class="es-profile-header t-lg-mb--lg">
	<div class="es-profile-header__hd with-cover">
		<div class="es-profile-header__cover es-flyout <?php echo $group->hasCover() ? '' : 'no-cover'; ?> <?php echo !empty($newCover) ? "editing" : ""; ?>"
			data-cover <?php echo $cover->photo_id ? 'data-es-photo="' . $cover->photo_id . '"' : '';?>
			style="background-image: url(<?php echo $cover->getSource();?>);background-position: <?php echo $cover->getPosition();?>;">

			<div class="es-cover-container">
				<div class="es-cover-viewport">

					<div class="es-cover-image" data-cover-image
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

					<?php if ($group->isAdmin()) { ?>
					<div class="es-flyout-content">
						<div class="dropdown_ pull-right es-cover-menu" data-cover-menu>
							<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ es-flyout-button">
								<?php echo JText::_('COM_EASYSOCIAL_PHOTOS_EDIT_COVER');?>
							</a>
							<ul class="dropdown-menu es-cover-dropdown-menu">
								<li data-cover-upload-button>
									<a href="javascript:void(0);"><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_UPLOAD_COVER"); ?></a>
								</li>
								<li data-cover-select-button>
									<a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_PHOTOS_SELECT_COVER' ); ?></a>
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

						<a href="javascript:void(0);"
						   class="es-cover-done-button es-flyout-button"
						   data-cover-done-button><i class="fa fa-check"></i><?php echo JText::_("COM_EASYSOCIAL_PHOTOS_COVER_DONE"); ?></a>

						<a href="javascript:void(0);"
						   class="es-cover-cancel-button es-flyout-button"
						   data-cover-cancel-button><i class="fa fa-remove"></i><?php echo JText::_("COM_ES_CANCEL"); ?></a>
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
							<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-cover-edit-button>Update Cover</a>
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

		<div class="es-profile-header__avatar-wrap es-flyout" data-avatar>
			<a href="<?php echo $group->getAvatarPhoto() ? 'javascript:void(0);' : $group->getPermalink();?>"<?php echo $group->getAvatarPhoto() ? 'data-es-photo="' . $group->getAvatarPhoto()->id . '"' : '';?>>
				<img data-avatar-image src="<?php echo $group->getAvatar(SOCIAL_AVATAR_SQUARE);?>" alt="<?php echo $this->html('string.escape' , $group->getTitle());?>" />
			</a>

			<?php if ($group->isAdmin()) { ?>
			<div class="es-flyout-content">
				<div class="dropdown_ es-avatar-menu" data-avatar-menu>
					<a href="javascript:void(0);" class="es-flyout-button dropdown-toggle_" data-bs-toggle="dropdown"><?php echo JText::_('COM_EASYSOCIAL_PHOTOS_EDIT_AVATAR');?></a>
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

						<?php if ($group->hasAvatar() && $group->isAdmin()) { ?>
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

		<?php echo $this->render('widgets', 'group', 'item', 'afterAvatar', array($group)); ?>
	</div>

	<div class="es-profile-header__bd">
		<div class="es-profile-header__info-wrap">
			<?php echo $this->render('module', 'es-groups-before-name'); ?>
			<h1 class="es-profile-header__title">
				<a href="<?php echo $group->getPermalink();?>"><?php echo $group->getName();?></a>
			</h1>
			<?php echo $this->render('module', 'es-groups-after-name'); ?>

			<ul class="g-list-inline g-list-inline--dashed es-profile-header__meta t-lg-mt--md">
				<li>
					<a href="<?php echo $group->getCategory()->getFilterPermalink();?>" class="">
						<i class="fa fa-folder"></i>&nbsp; <?php echo $group->getCategory()->getTitle(); ?>
					</a>
				</li>

				<li>
					<a href="javascript:void(0);" class="">
						<?php echo $this->html('group.type', $group, 'bottom', true); ?>
					</a>
				</li>

				<?php if ($this->config->get('groups.layout.hits')) { ?>
				<li>
					<a href="javascript:void(0);" class="" data-es-provide="tooltip" data-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_VIEWS', $group->hits), $group->hits); ?>">
						<i class="fa fa-eye"></i>&nbsp; <?php echo $group->hits;?>
					</a>
				</li>
				<?php } ?>

				<li>
					<?php echo $this->render('widgets', 'group', 'groups', 'headerMeta', array($group)); ?>
				</li>
			</ul>

			<?php echo $this->render('widgets', 'group', 'groups', 'afterCategory', array($group)); ?>
		</div>

		<div class="es-profile-header__action-wrap">
			<?php echo $this->render('module', 'es-groups-before-actions'); ?>
			<?php echo $this->render('widgets', 'group', 'item', 'beforeActions', array($group)); ?>

			<div class="btn-toolbar" role="toolbar">

				<div class="btn-group">
					<?php echo $this->html('group.action', $group, true); ?>
				</div>

				<?php if ($this->config->get('groups.sharing.showprivate') || (!$this->config->get('groups.sharing.showprivate') && $group->isOpen())) { ?>
				<div class="btn-group">
					<?php echo $this->html('group.bookmark', $group); ?>
				</div>
				<?php } ?>

				<?php if ($group->canAccessActionMenu() || $group->isMember()) { ?>
				<div class="btn-group" role="group">

					<button data-type="button" class="dropdown-toggle_ btn btn-es-default-o btn-sm" data-bs-toggle="dropdown">
						<i class="fa fa-ellipsis-h"></i>
					</button>

					<ul class="dropdown-menu dropdown-menu-right">
						<?php if ($group->canInvite()) { ?>
							<li>
								<a href="javascript:void(0);" data-es-groups-invite data-id="<?php echo $group->id;?>"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_FRIENDS');?></a>
							</li>

							<?php if ($group->canAccessActionMenu()) { ?>
								<li class="divider"></li>
							<?php } ?>
						<?php } ?>

						<?php echo $this->html('group.report', $group); ?>

						<?php echo $this->html('group.adminActions', $group); ?>
					</ul>
				</div>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-groups-after-actions'); ?>
			<?php echo $this->render('widgets', 'group' , 'item' , 'afterActions' , array( $group ) ); ?>
		</div>
	</div>

	<div class="es-profile-header-nav">

		<div class="es-profile-header-nav__item <?php echo $active == 'timeline' ? 'is-active' : '';?>">
			<a href="<?php echo $timelinePermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_TIMELINE');?></span></a>
		</div>
		
		<div class="es-profile-header-nav__item <?php echo $active == 'info' ? 'is-active' : '';?>">
			<a href="<?php echo $aboutPermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_GROUPS_ABOUT');?></span></a>
		</div>

		<div class="es-profile-header-nav__item <?php echo $active == 'members' ? 'is-active' : '';?> <?php echo $pendingMembers > 0 ? ' has-notice' : '';?>">
			<a href="<?php echo $group->getAppPermalink('members');?>" class="es-profile-header-nav__link">
				<span><?php echo JText::_('COM_ES_MEMBERS'); ?></span>
				<span class="es-profile-header-nav__link-bubble"></span>
			</a>
		</div>

		<?php if (!$this->isMobile()) { ?>
			<?php if ($group->allowPhotos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'albums' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::albums(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_ES_ALBUMS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($group->allowVideos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'videos' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::videos(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS'); ?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($group->allowAudios()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'audios' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::audios(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_ES_AUDIOS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($group->canViewEvent()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'events' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::events(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_EASYSOCIAL_EVENTS');?></span>
				</a>
			</div>
			<?php }?>

			<?php if ($showMore && count($apps) > 1) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
				<div class="btn-group">
					<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
						<span><?php echo JText::_('COM_ES_MORE');?></span> &nbsp;<i class="i-chevron i-chevron--down"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<?php if ($apps) { ?>
							<?php foreach ($apps as $app){ ?>
							<li class="<?php echo $active == 'apps.' . $app->element ? 'is-active' : '';?>">
								<a href="<?php echo $group->getAppsPermalink($app->getAlias());?>" class="es-profile-header-nav__dropdown-link">
									<?php echo $app->getAppTitle(); ?>
								</a>
							</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } else if (count($apps) == 1) { ?>
				<div class="es-profile-header-nav__item <?php echo $active == 'apps.' . $apps[0]->element ? 'is-active' : '';?>">
					<a href="<?php echo $event->getAppsPermalink($apps[0]->getAlias());?>" class="es-profile-header-nav__link">
						<span><?php echo $apps[0]->getAppTitle(); ?></span>
					</a>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if ($showMore && $this->isMobile()) { ?>
		<div class="es-profile-header-nav__item <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
			<div class="btn-group">
				<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
				<span><?php echo JText::_('COM_ES_MORE');?></span>
				&nbsp;<i class="i-chevron i-chevron--down"></i>

				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if ($group->allowPhotos()) { ?>
					<li class="<?php echo $active == 'albums' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::albums(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_ALBUMS');?></a>
					</li>
					<?php } ?>

					<?php if ($group->allowVideos()) { ?>
					<li class="<?php echo $active == 'videos' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::videos(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS');?></a>
					</li>
					<?php } ?>

					<?php if ($group->allowAudios()) { ?>
					<li class="<?php echo $active == 'audios' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::audios(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_AUDIOS');?></a>
					</li>
					<?php } ?>

					<?php if ($group->canViewEvent()) { ?>
					<li class="<?php echo $active == 'events' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::events(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_EASYSOCIAL_EVENTS');?></a>
					</li>
					<?php }?>
					
					<?php if ($apps) { ?>
						<?php foreach ($apps as $app) { ?>
						<li class="<?php echo $active == 'apps.' . $app->element ? 'is-active' : '';?>">
							<a href="<?php echo $group->getAppsPermalink($app->getAlias());?>" class="es-profile-header-nav__dropdown-link">
								<?php echo $app->getAppTitle(); ?>
							</a>
						</li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>
</div>