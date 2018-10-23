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
<div class="es-group-category">

	<div class="es-cat-header">
		<div class="es-cat-header__hd">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<a href="" class="o-avatar es-cat-header__avatar">
						<img src="<?php echo $profile->getAvatar();?>" alt="<?php echo $this->html('string.escape', $profile->get('title'));?>">
					</a>    
				</div>
				<div class="o-flag__body">
					<div class="es-cat-header__hd-content-wrap">
						<div class="es-cat-header__hd-content">
							<div class="es-cat-header__title-link"><?php echo $profile->get('title'); ?></div>
							<div class="es-cat-header__desc">
								<?php echo $profile->get('description'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="es-cat-header__ft">
			<div class="t-lg-pull-left">
				<ul class="g-list-inline g-list-inline--space-right">
					<li>
						<i class="fa fa-users"></i> <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_USERS_COUNT', $totalUsers), $totalUsers); ?>
					</li>
				</ul>
			</div>

			<?php if ($this->my->guest) { ?>
			<a href="<?php echo ESR::registration();?>" class="btn btn-es-primary btn-sm t-lg-pull-right">
				<?php echo JText::_('COM_EASYSOCIAL_REGISTER_BUTTON'); ?> &rarr;
			</a>
			<?php } ?>
			
		</div>
	</div>

	<div class="es-container t-lg-mt--xl">
		<div class="es-sidebar" data-sidebar>
			<div data-dashboardSidebar-menu data-type="profile" data-id="<?php echo $profile->id;?>" class="active"></div>

			<?php echo $this->render('module', 'es-profiles-sidebar-top' , 'site/dashboard/sidebar.module.wrapper'); ?>

			<?php echo $this->includeTemplate('site/profiles/default/widgets/members'); ?>

			<hr class="es-hr" />

			<?php echo $this->includeTemplate('site/profiles/default/widgets/albums'); ?>

			<?php echo $this->render('module', 'es-profiles-sidebar-bottom' , 'site/dashboard/sidebar.module.wrapper'); ?>
		</div>

		<div class="es-content">
			<div class="es-snackbar">
				<h1 class="es-snackbar__title"><?php echo JText::_('COM_EASYSOCIAL_GROUPS_RECENT_UPDATES'); ?></h1>
			</div>

			<div class="es-content-wrap" data-es-group-item-content>
				<?php echo $stream->html();?>
			</div>
		</div>        
	</div>
</div>