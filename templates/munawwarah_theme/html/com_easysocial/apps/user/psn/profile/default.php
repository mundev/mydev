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
<div class="es-container" data-es-container>
	<div class="es-sidebar" data-sidebar>
		<?php if ($user->isViewer()) { ?>
			<a href="<?php echo $unlink;?>" class="btn btn-es-danger btn-block t-lg-mb--xl">
				<?php echo JText::_('APP_USER_PSN_DEAUTHORIZE'); ?>
			</a>
		<?php } ?>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-trophy t-lg-mr--md"></i>
							<?php echo JText::sprintf('APP_USER_PSN_TROPHIES', $trophiesEarned); ?>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">

		<?php echo $this->html('cover.user', $user, 'apps.' . $this->vars['app']->element); ?>

		<?php echo $this->html('html.snackbar', 'APP_PSN_USER_TITLE' , 'h2'); ?>
		
		<div class="es-app-psn app-contents<?php echo !$trophies ? ' is-empty' : '';?>" data-psn-wrapper>

		<?php echo $this->html('html.loading'); ?>
		<?php echo $this->html('html.emptyBlock', 'APP_USER_PSN_NO_TROPHIES_YET', 'fa-trophy'); ?>

		<div data-discussion-contents>
			<?php foreach ($trophies as $trophy) { ?>
				<div class="es-apps-item es-island">
					<div class="es-apps-item__bd">
						<div class="o-flag">
							<div class="o-flag__image">
								<img src="<?php echo $trophy->thumbnail;?>" width="180" />
							</div>

							
							<div class="o-flag__body o-flag--top">
								<a href="<?php echo $trophy->permalink;?>" class="es-apps-item__title"><?php echo $trophy->title;?></a>

								<div class="t-text--center t-lg-mt--xl" style="width:auto;"">
									<div class="o-grid">
										<div class="o-grid__cell trophy-bronze" style="color: <?php echo PSNLibrary::getTrophyColor('bronze');?>;">
											<i class="fa fa-trophy"></i> <?php echo $trophy->bronze;?>
										</div>

										<div class="o-grid__cell trophy-silver" style="color: <?php echo PSNLibrary::getTrophyColor('silver');?>;">
											<i class="fa fa-trophy"></i> <?php echo $trophy->silver;?>
										</div>

										<div class="o-grid__cell trophy-gold" style="color: <?php echo PSNLibrary::getTrophyColor('gold');?>;">
											<i class="fa fa-trophy"></i> <?php echo $trophy->gold;?>
										</div>
									</div>

									<div style="background:#ddd;border: 1px solid #d7d7d7;position: relative;">
										<span style="position: relative; display: block;font-size: 11px;color:#fff;text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3), -1px -1px 1px rgba(0, 0, 0, 0.3), 1px -1px 1px rgba(0, 0, 0, 0.3), -1px 1px 1px rgba(0, 0, 0, 0.3);"><?php echo $trophy->progress;?>%</span>
										<div style="position: absolute;top: 0;background: #667fb2;height: 18px;width: <?php echo $trophy->progress;?>%">&nbsp;
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="es-apps-item__ft es-bleed--bottom">
						<div class="o-grid">
							<div class="o-grid__cell">
								<div class="es-apps-item__meta">
									<div class="es-apps-item__meta-item">
										<ol class="g-list-inline g-list-inline--dashed">
											<li>
												<i class="fa fa-trophy"></i>&nbsp; <?php echo JText::sprintf('APP_USER_PSN_PROFILE_TROPHIES_EARNED', '<b>' . $trophy->earned . '</b>', '<b>' . $trophy->total . '</b>'); ?>
											</li>
										</ol>
									</div>
								</div>		
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>