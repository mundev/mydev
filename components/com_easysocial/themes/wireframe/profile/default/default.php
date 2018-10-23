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
<div class="es-profile userProfile" data-id="<?php echo $user->id;?>" data-es-profile>

	<?php echo $this->render('widgets', 'user', 'profile', 'aboveHeader', array($user)); ?>
	<?php echo $this->render('module', 'es-profile-before-header'); ?>

	<?php if ($this->my->isSiteAdmin() && $user->isBlock()) { ?>
	<div class="es-user-banned alert alert-danger">
		<?php echo JText::_('COM_EASYSOCIAL_PROFILE_USER_IS_BANNED');?>
	</div>
	<?php } ?>

	<?php echo $this->html('cover.user', $user, $layout); ?>

	<?php echo $this->render('module', 'es-profile-after-header'); ?>

	<div class="es-container <?php echo $this->config->get('users.profile.sidebar') == 'right' ? 'es-sidebar-right' : '';?>" data-es-container>

		<?php if (!$this->isMobile() && $layout == 'timeline' && $this->config->get('users.profile.sidebar') != 'hidden') { ?>
		<div class="es-sidebar" data-sidebar>

			<div class="es-side-widget" data-type="info">
				<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_USER_INTRO'); ?>

				<div class="es-side-widget__bd">

					<ul class="o-nav o-nav--stacked">
						<li class="o-nav__item t-text--muted  t-lg-mb--sm">
							<i class="es-side-widget__icon fa fa-home t-lg-mr--md"></i>
							<?php echo JText::sprintf('COM_ES_JOINED_ON', $user->getRegistrationDate()->toLapsed()); ?>
						</li>

						<?php if ($this->config->get('badges.enabled') && $user->badgesViewable($this->my->id)) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<a class="o-nav__link t-text--muted" href="<?php echo ESR::badges(array('layout' => 'achievements', 'userid' => $user->getAlias()));?>">
								<i class="es-side-widget__icon fa fa-gift t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_ACHIEVEMENTS', $user->getTotalBadges()), $user->getTotalBadges()); ?>
							</a>
						</li>
						<?php } ?>

						<?php if( $this->config->get('points.enabled')){ ?>
						<li class="o-nav__item t-lg-mb--sm">
							<?php if ($this->my->canViewPointsHistory($user)) { ?>
							<a class="o-nav__link t-text--muted" href="<?php echo ESR::points(array('layout' => 'history', 'userid' => $user->getAlias()));?>">
							<?php } else { ?>
							<a class="o-nav__link t-text--muted" href="javascript:void(0);">
							<?php } ?>
								<i class="es-side-widget__icon fa fa-certificate t-lg-mr--md"></i>
								<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_POINTS', $user->getPoints()), $user->getPoints()); ?>
							</a>
						</li>
						<?php } ?>

						<?php if ($this->config->get('users.layout.gender', true)) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<?php echo $this->render('fields', 'user', 'profile', 'profileIntro', array('GENDER', $user)); ?>
						</li>
						<?php } ?>

						<?php if ($this->config->get('users.layout.age', true)) { ?>
						<li class="o-nav__item t-lg-mb--sm">
							<?php echo $this->render('fields', 'user', 'profile', 'profileIntro', array('BIRTHDAY', $user)); ?>
						</li>
						<?php } ?>

						<?php if ($this->config->get('users.layout.address', true)) { ?>
						<li class="o-nav__item">
							<?php echo $this->render('fields', 'user', 'profile', 'profileIntro', array('ADDRESS', $user)); ?>
						</li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<?php echo $this->render('module', 'es-profile-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php echo $this->render('widgets', 'user', 'profile', 'sidebarTop', array($user), 'site/widgets/sidebar.wrapper'); ?>

			<?php echo $this->render('module', 'es-profile-sidebar-after-apps'); ?>

			<?php echo $this->render('widgets', 'user', 'profile', 'sidebarBottom', array($user), 'site/widgets/sidebar.wrapper'); ?>

			<?php echo $this->render('module', 'es-profile-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>
		<?php } ?>

		<div class="es-content" data-profile-contents>
			<div class="es-profile-contents">
				<?php echo $this->html('html.loading'); ?>

				<?php echo $this->render('widgets', 'user', 'profile', 'aboveStream', array($user)); ?>

				<?php echo $this->render('module', 'es-profile-before-contents'); ?>
				
				<div class="es-profile-details" data-profile-real-content>
					<?php echo $contents; ?>
				</div>
			</div>

			<?php echo $this->render('module', 'es-profile-after-contents'); ?>
		</div>
	</div>
</div>
