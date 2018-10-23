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
<?php if ($cluster) { ?>
<div class="t-lg-mb--md">
	<a href="<?php echo $cluster->getPermalink();?>" class="btn btn-es-default-o">&larr; <?php echo JText::_('COM_ES_BACK_TO_' . strtoupper($cluster->getType()));?></a>
</div>
<?php } ?>

<?php echo $this->render('module', 'es-event-before-cover'); ?>

<div class="es-profile-header t-lg-mb--lg">
	<div class="es-profile-header__hd with-cover">
		<div class="es-profile-header__cover es-flyout <?php echo $event->hasCover() ? 'has-cover' : 'no-cover'; ?> <?php echo !empty($newCover) ? "editing" : ""; ?> <?php echo $event->isAdmin() ? 'is-owner' : '';?>"
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

					<?php if ($event->isAdmin()) { ?>
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

		<div class="es-profile-header__avatar-wrap es-flyout" data-avatar>
			<a href="<?php echo $event->getAvatarPhoto() ? 'javascript:void(0);' : $event->getPermalink();?>"<?php echo $event->getAvatarPhoto() ? 'data-es-photo="' . $event->getAvatarPhoto()->id . '"' : '';?>>
				<img src="<?php echo $event->getAvatar(SOCIAL_AVATAR_SQUARE);?>" alt="<?php echo $this->html('string.escape' , $event->getTitle());?>" data-avatar-image />
			</a>

			<?php if ($event->isAdmin()) { ?>
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

						<?php if ($event->hasAvatar() && $event->isAdmin()) { ?>
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

		<?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'item', 'afterAvatar', array($event)); ?>
	</div>
	<div class="es-profile-header__bd">
		<div class="es-profile-header__info-wrap">
			<?php echo $this->render('module', 'es-events-before-name'); ?>
			<h1 class="es-profile-header__title">
				<a href="<?php echo $event->getPermalink();?>"><?php echo $event->getName();?></a>
			</h1>
			<?php echo $this->render('module', 'es-events-after-name'); ?>

			<ul class="g-list-inline g-list-inline--dashed es-profile-header__meta t-lg-mt--md">
				<li>
					<a href="<?php echo $event->getCategory()->getFilterPermalink();?>" class="">
						<i class="fa fa-folder"></i>&nbsp; <?php echo $event->getCategory()->getTitle(); ?>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" class="">
						<?php echo $this->html('event.type', $event); ?>
					</a>
				</li>
				
				<?php if ($this->config->get('events.layout.hits')) { ?>
				<li>
					<a href="javascript:void(0);" class="" data-es-provide="tooltip" data-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_TOTAL_VIEWS', $event->hits), $event->hits); ?>">
						<i class="fa fa-eye"></i>&nbsp; <?php echo $event->hits;?>
					</a>
				</li>
				<?php } ?>

				<li>
					<span class="">
						<i class="fa fa-calendar"></i> <?php echo $event->getStartEndDisplay(); ?>
					</span>
				</li>

				<li>
					<?php echo $this->render('widgets', 'event', 'events', 'headerMeta', array($event)); ?>
				</li>
			</ul>
		</div>

		<div class="es-profile-header__action-wrap">
			<?php echo $this->render('module', 'es-events-before-actions'); ?>
			<?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'item', 'beforeActions', array($event)); ?>

			<div class="btn-toolbar" role="toolbar">

				<div class="btn-group">
					<?php echo $this->html('event.action', $event, 'right', true); ?>
				</div>

				<?php if ($this->config->get('events.ical', true)) { ?>
				<div class="btn-group">
					<a href="<?php echo $event->getCalendarLink(); ?>" class="btn btn-es-default-o btn-sm" data-es-provide="tooltip" data-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_EXPORT_TO_ICAL', true);?>">
						<i class="fa fa-calendar"></i>
					</a>
				</div>
				<?php } ?>

				<?php if ($this->config->get('events.sharing.showprivate') || (!$this->config->get('events.sharing.showprivate') && $event->isOpen())) { ?>
				<div class="btn-group">
					<?php echo $this->html('event.bookmark', $event); ?>
				</div>
				<?php } ?>

				<?php if ($event->canAccessActionMenu() || $event->canInvite()) { ?>
				<div class="btn-group">

					<button type="button" class="dropdown-toggle_ btn btn-es-default-o btn-sm" data-bs-toggle="dropdown">
						<i class="fa fa-ellipsis-h"></i>
					</button>

					<ul class="dropdown-menu dropdown-menu-right">
						<?php if ($event->canInvite()) { ?>
							<li>
								<a href="javascript:void(0);" data-es-events-invite data-id="<?php echo $event->id;?>" data-return="<?php echo $returnUrl;?>" ><?php echo JText::_('COM_EASYSOCIAL_GROUPS_INVITE_FRIENDS');?></a>
							</li>
							<?php if ($event->canAccessActionMenu()) { ?>
							<li class="divider"></li>
							<?php } ?>
						<?php } ?>

						<?php echo $this->html('event.report', $event); ?>

						<?php echo $this->html('event.adminActions', $event); ?>
					</ul>
				</div>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-events-after-actions'); ?>
			<?php echo $this->render('widgets', SOCIAL_TYPE_EVENT, 'item', 'afterActions', array($event)); ?>
		</div>
	</div>
	
	<div class="es-profile-header-nav">
		<div class="es-profile-header-nav__item <?php echo $active == 'timeline' ? 'is-active' : '';?>">
			<a href="<?php echo $timelinePermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_TIMELINE');?></span></a>
		</div>

		<div class="es-profile-header-nav__item <?php echo $active == 'info' ? 'is-active' : '';?>">
			<a href="<?php echo $aboutPermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_EVENTS_ABOUT');?></span></a>
		</div>

		<div class="es-profile-header-nav__item <?php echo $active == 'guests' ? 'is-active' : '';?> <?php echo $totalPendingGuests > 0 ? ' has-notice' : '';?>">
			<a href="<?php echo $event->getAppPermalink('guests');?>" class="es-profile-header-nav__link">
				<span><?php echo JText::_('COM_ES_ATTENDEES'); ?></span>
				<span class="es-profile-header-nav__link-bubble"></span>
			</a>
		</div>

		<?php if (!$this->isMobile()) { ?>
			<?php if ($event->allowPhotos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'albums' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::albums(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_ES_ALBUMS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($event->allowVideos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'videos' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::videos(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS'); ?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($event->allowAudios()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'audios' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::audios(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_ES_AUDIOS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showMore && count($apps) > 1) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
				<div class="btn-group">
					<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
						<span data-text=""><?php echo JText::_('COM_ES_MORE');?></span> &nbsp;<i class="i-chevron i-chevron--down"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<?php foreach ($apps as $app){ ?>
						<li class="<?php echo $active == 'apps.' . $app->element ? 'is-active' : '';?>">
							<a href="<?php echo $event->getAppsPermalink($app->getAlias());?>" class="es-profile-header-nav__dropdown-link">
								<?php echo $app->getAppTitle(); ?>
							</a>
						</li>
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
		<div class="es-profile-header-nav__item  <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
			<div class="btn-group">
				<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
				<span><?php echo JText::_('COM_ES_MORE');?></span>
				&nbsp;<i class="i-chevron i-chevron--down"></i>

				</a>
				<ul class="dropdown-menu dropdown-menu-right">
					<?php if ($event->allowPhotos()) { ?>
					<li class="<?php echo $active == 'albums' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::albums(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_ALBUMS');?></a>
					</li>
					<?php } ?>

					<?php if ($event->allowVideos()) { ?>
					<li class="<?php echo $active == 'videos' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::videos(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS'); ?></a>
					</li>
					<?php } ?>

					<?php if ($event->allowAudios()) { ?>
					<li class="<?php echo $active == 'audios' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::audios(array('uid' => $event->getAlias(), 'type' => SOCIAL_TYPE_EVENT));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_AUDIOS');?></a>
					</li>
					<?php } ?>
					
					<?php if ($apps) { ?>
						<?php foreach ($apps as $app) { ?>
						<li class="<?php echo $active == 'apps.' . $app->element ? 'is-active' : '';?>">
							<a href="<?php echo $event->getAppsPermalink($app->getAlias());?>" class="es-profile-header-nav__dropdown-link">
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

<?php echo $this->render('module', 'es-event-before-cover'); ?>
