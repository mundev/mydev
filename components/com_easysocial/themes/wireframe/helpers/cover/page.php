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
<?php echo $this->render('module', 'es-page-before-cover'); ?>

<div class="es-profile-header t-lg-mb--lg">
	<div class="es-profile-header__hd with-cover">
		<div class="es-profile-header__cover es-flyout <?php echo $page->hasCover() ? 'has-cover' : 'no-cover'; ?> <?php echo !empty($newCover) ? "editing" : ""; ?> <?php echo $page->isAdmin() ? 'is-owner' : '';?>"
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

					<?php if ($page->isAdmin()) { ?>
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
			<a href="<?php echo $page->getAvatarPhoto() ? 'javascript:void(0);' : $page->getPermalink();?>"<?php echo $page->getAvatarPhoto() ? 'data-es-photo="' . $page->getAvatarPhoto()->id . '"' : '';?>>
				<img data-avatar-image src="<?php echo $page->getAvatar(SOCIAL_AVATAR_SQUARE);?>" alt="<?php echo $this->html('string.escape', $page->getTitle());?>" />
			</a>

			<?php if ($page->isAdmin()) { ?>
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

						<?php if ($page->hasAvatar() && $page->isAdmin()) { ?>
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

		<?php if (!$this->isMobile()) { ?>
		<div class="es-profile-header__label-wrap">
			<?php echo $this->trigger('fields', 'page', 'item', 'coverFooter', $page); ?>
		</div>
		<?php } ?>

		<?php echo $this->render('widgets', 'page', 'item', 'afterAvatar', array($page)); ?>
	</div>

	<div class="es-profile-header__bd">
		<div class="es-profile-header__info-wrap">
			<?php echo $this->render('module', 'es-pages-before-name'); ?>
			<h1 class="es-profile-header__title">
				<a href="<?php echo $page->getPermalink();?>"><?php echo $page->getName();?></a>
			</h1>
			<?php echo $this->render('module', 'es-pages-after-name'); ?>

			<?php if ($this->isMobile()) { ?>
			<div class="es-profile-header__label-wrap">
				<?php echo $this->trigger('fields', 'page', 'item', 'coverFooter', $page); ?>
			</div>
			<?php } ?>

			<ul class="g-list-inline g-list-inline--dashed es-profile-header__meta t-lg-mt--md">
				<li>
					<a href="<?php echo $page->getCategory()->getFilterPermalink();?>" class="">
						<i class="fa fa-folder"></i>&nbsp; <?php echo $page->getCategory()->getTitle(); ?>
					</a>
				</li>

				<li>
					<a href="javascript:void(0);" class="">
						<?php echo $this->html('page.type', $page); ?>
					</a>
				</li>

				<li>
					<a href="<?php echo $page->getAppPermalink('followers');?>" class="">
						<i class="fa fa-thumbs-o-up"></i>&nbsp; <?php echo JText::sprintf(ES::string()->computeNoun('COM_ES_LIKES_COUNT', $page->getTotalMembers()), $page->getTotalMembers()); ?>
					</a>
				</li>

				<?php if ($this->config->get('pages.hits.display')) { ?>
				<li>
					<a href="javascript:void(0);" class="" data-es-provide="tooltip" data-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_PAGES_VIEWS', $page->hits), $page->hits); ?>">
						<i class="fa fa-eye"></i>&nbsp; <?php echo $page->hits;?>
					</a>
				</li>
				<?php } ?>

				<li>
					<?php echo $this->render('widgets', 'page', 'pages', 'headerMeta', array($page)); ?>
				</li>
			</ul>

			<?php echo $this->render('widgets', 'page', 'pages', 'afterCategory', array($page)); ?>
		</div>

		<div class="es-profile-header__action-wrap">
			<?php echo $this->render('module', 'es-pages-before-actions'); ?>
			<?php echo $this->render('widgets', 'page', 'item', 'beforeActions', array($page)); ?>

			<div class="btn-toolbar" role="toolbar">

				<div class="btn-group">
					<?php echo $this->html('page.action', $page, true); ?>
				</div>

				<?php if ($this->config->get('pages.sharing.showprivate') || (!$this->config->get('pages.sharing.showprivate') && $page->isOpen())) { ?>
				<div class="btn-group">
					<?php echo $this->html('page.bookmark', $page); ?>
				</div>
				<?php } ?>

				<?php if ($page->canAccessActionMenu() || $page->isMember()) { ?>
				<div class="btn-group" role="group">

					<button type="button" class="dropdown-toggle_ btn btn-es-default-o btn-sm" data-bs-toggle="dropdown">
						<i class="fa fa-ellipsis-h"></i>
					</button>

					<ul class="dropdown-menu dropdown-menu-right">
						<?php if ($page->canInvite()) { ?>
							<li>
								<a href="javascript:void(0);" data-es-pages-invite data-id="<?php echo $page->id;?>"><?php echo JText::_('COM_EASYSOCIAL_PAGES_INVITE_FRIENDS');?></a>
							</li>

							<?php if ($page->canAccessActionMenu()) { ?>
							<li class="divider"></li>
							<?php } ?>
						<?php } ?>

						<?php echo $this->html('page.report', $page); ?>

						<?php echo $this->html('page.adminActions', $page); ?>
					</ul>
				</div>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-pages-after-actions'); ?>
			<?php echo $this->render('widgets', 'page', 'item', 'afterActions', array($page)); ?>
		</div>
	</div>

	<div class="es-profile-header-nav">

		<div class="es-profile-header-nav__item <?php echo $active == 'timeline' ? 'is-active' : '';?>">
			<a href="<?php echo $timelinePermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_TIMELINE');?></span></a>
		</div>
		
		<div class="es-profile-header-nav__item <?php echo $active == 'info' ? 'is-active' : '';?>">
			<a href="<?php echo $aboutPermalink;?>" class="es-profile-header-nav__link"><span><?php echo JText::_('COM_ES_PAGES_ABOUT');?></span></a>
		</div>

		<div class="es-profile-header-nav__item <?php echo $active == 'members' ? 'is-active' : '';?> <?php echo $pendingFollowers > 0 ? ' has-notice' : '';?>">
			<a href="<?php echo $page->getAppPermalink('followers');?>" class="es-profile-header-nav__link">
				<span><?php echo JText::_('COM_EASYSOCIAL_FOLLOWERS'); ?></span>
				<span class="es-profile-header-nav__link-bubble"></span>
			</a>
		</div>

		<?php if (!$this->isMobile()) { ?>
			<?php if ($page->allowPhotos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'albums' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::albums(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_ES_ALBUMS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($page->allowVideos()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'videos' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::videos(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_EASYSOCIAL_VIDEOS'); ?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($page->allowAudios()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'audios' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::audios(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_ES_AUDIOS');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($page->canViewEvent()) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'events' ? 'is-active' : '';?>">
				<a href="<?php echo ESR::events(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__link">
					<span><?php echo JText::_('COM_EASYSOCIAL_EVENTS');?></span>
				</a>
			</div>
			<?php }?>

			<?php if ($showMore && count($apps) > 1) { ?>
			<div class="es-profile-header-nav__item <?php echo $active == 'apps' || $isAppActive ? 'is-active' : '';?>">
				<div class="btn-group">
					<a href="javascript:void(0);" class="es-profile-header-nav__link dropdown-toggle_" data-bs-toggle="dropdown" data-button="">
						<span data-text=""><?php echo JText::_('COM_ES_MORE');?></span>
						&nbsp;<i class="i-chevron i-chevron--down"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<?php foreach ($apps as $app) { ?>
						<li class="<?php echo $active == 'apps.' . $app->element ? 'is-active' : '';?>">
							<a href="<?php echo $page->getAppsPermalink($app->getAlias());?>" class="es-profile-header-nav__dropdown-link" title="<?php echo $app->getPageTitle(); ?>">
								<?php echo $app->getAppTitle(); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php } else if (count($apps) == 1) { ?>
				<div class="es-profile-header-nav__item <?php echo $active == 'apps.' . $apps[0]->element ? 'is-active' : '';?>">
					<a href="<?php echo $page->getAppsPermalink($apps[0]->getAlias());?>" class="es-profile-header-nav__link">
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
					<?php if ($page->allowPhotos()) { ?>
					<li class="<?php echo $active == 'albums' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::albums(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_ALBUMS');?></a>
					</li>
					<?php } ?>

					<?php if ($page->allowVideos()) { ?>
					<li class="<?php echo $active == 'videos' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::videos(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_EASYSOCIAL_VIDEOS'); ?></a>
					</li>
					<?php } ?>

					<?php if ($page->allowAudios()) { ?>
					<li class="<?php echo $active == 'audios' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::audios(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_ES_AUDIOS');?></a>
					</li>
					<?php } ?>

					<?php if ($page->canViewEvent()) { ?>
					<li class="<?php echo $active == 'events' ? 'is-active' : '';?>">
						<a href="<?php echo ESR::events(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE));?>" class="es-profile-header-nav__dropdown-link"><?php echo JText::_('COM_EASYSOCIAL_EVENTS');?></a>
					</li>
					<?php }?>
					
					<?php if ($apps) { ?>
						<?php foreach ($apps as $app) { ?>
						<li class="<?php echo $active == 'apps.' . $app->element ? 'is-active' : '';?>">
							<a href="<?php echo $page->getAppsPermalink($app->getAlias());?>" class="es-profile-header-nav__dropdown-link">
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

<?php echo $this->render('module', 'es-page-before-cover'); ?>
