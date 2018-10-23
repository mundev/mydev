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
<div class="es-profile" data-es-group data-id="<?php echo $group->id;?>">

	<?php echo $this->html('cover.group', $group, $layout); ?>
	
	<?php echo $this->html('responsive.toggle'); ?>

	<div class="es-container" data-es-container>

		<div class="es-sidebar" data-sidebar>
			<?php echo $this->render('module', 'es-groups-about-item-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>
			
			<div class="es-side-widget" data-type="info">
				<?php echo $this->html('widget.title', 'COM_ES_USER_STATS'); ?>

				<div class="es-side-widget__bd">

					<ul class="o-nav o-nav--stacked">
						<li class="o-nav__item t-lg-mb--sm">
							<a href="<?php echo $group->getAppPermalink('members');?>" class="o-nav__link t-text--muted">
								<i class="es-side-widget__icon fa fa-users t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS_MINI', $group->getTotalMembers()), '<b>'. $group->getTotalMembers() . '</b>'); ?>
							</a>
						</li>

						<?php if ($group->allowPhotos()) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<a href="<?php echo ESR::albums(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="o-nav__link t-text--muted">
								<i class="es-side-widget__icon fa fa-photo t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_ALBUMS', $group->getTotalAlbums()) , '<b>' . $group->getTotalAlbums() . '</b>'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($group->allowVideos()) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<a href="<?php echo ESR::videos(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="o-nav__link t-text--muted">
								<i class="es-side-widget__icon fa fa-film t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_EVENTS_VIDEOS', $group->getTotalVideos()), '<b>' . $group->getTotalVideos() . '</b>'); ?>
							</a>
						</li>
						<?php } ?>
						
						<?php if ($group->allowAudios()) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<a href="<?php echo ESR::audios(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="o-nav__link t-text--muted">
								<i class="es-side-widget__icon fa fa-music t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_CLUSTERS_AUDIO' , $group->getTotalAudios()), '<b>' . $group->getTotalAudios() . '</b>'); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($group->canViewEvent()) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<a href="<?php echo ESR::events(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>" class="o-nav__link t-text--muted">
								<i class="es-side-widget__icon fa fa-calendar t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_EVENTS', $group->getTotalEvents()), '<b>' . $group->getTotalEvents() . '</b>'); ?>
							</a>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<?php echo $this->render('module', 'es-groups-about-item-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content">

			<?php echo $this->render('module', 'es-groups-about-before-contents'); ?>

			<div class="es-profile-info">
				<?php if ($steps) { ?>
					<?php echo $this->output('site/fields/about/default', array('steps' => $steps, 'canEdit' => $group->isAdmin(), 'objectId' => $group->id, 'routerType' => 'groups')); ?>
				<?php } ?>
			</div>

			<?php echo $this->render('module', 'es-groups-about-after-contents'); ?>
		</div>
	</div>
</div>
