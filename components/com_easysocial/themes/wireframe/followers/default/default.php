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
<?php echo $this->html('cover.user', $user, 'followers'); ?>

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-followers data-es-container>

	<div class="es-sidebar" data-sidebar>
		
		<?php echo $this->render('module', 'es-followers-sidebar-top'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWERS_TITLE'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item has-notice <?php echo $active == 'followers' ? ' active' : '';?>" data-filter-item data-type="followers" data-id="<?php echo $user->id;?>">
						<a href="<?php echo $filterFollowers;?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_FOLLOWERS', true);?>">
							<?php echo JText::_( 'COM_EASYSOCIAL_FOLLOWERS_FOLLOWERS' );?>
							<span class="o-tabs__bubble" data-followers-count><?php echo $totalFollowers;?></span>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item has-notice <?php echo $active == 'following' ? ' active' : '';?>" data-filter-item data-type="following" data-id="<?php echo $user->id;?>">
						<a href="<?php echo $filterFollowing;?>" class="o-tabs__link <?php echo $active == 'following' ? ' active' : '';?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_FOLLOWING');?>">
							<?php echo JText::_('COM_EASYSOCIAL_FOLLOWERS_FOLLOWING');?>
							<span class="o-tabs__bubble" data-following-count><?php echo $totalFollowing;?></span>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<?php if ($user->isViewer()) { ?>
					<li class="o-tabs__item has-notice <?php echo $active == 'suggest' ? ' active' : '';?>" data-filter-item data-type="suggest" data-id="<?php echo $user->id;?>">
						<a href="<?php echo $filterSuggest;?>" class="o-tabs__link <?php echo $active == 'suggest' ? ' active' : '';?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_PEOPLE_TO_FOLLOW', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_FOLLOWERS_SUGGEST');?>
							<span class="o-tabs__bubble" data-suggest-count><?php echo $totalSuggest;?></span>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>

				</ul>
			</div>
		</div>

		<?php echo $this->render('module', 'es-followers-sidebar-bottom'); ?>
	</div>


	<div class="es-content">
		<?php echo $this->render('module', 'es-followers-before-contents'); ?>
		
		<?php echo $this->includeTemplate('site/followers/default/items'); ?>

		<?php echo $this->render('module', 'es-followers-after-contents'); ?>
	</div>

</div>
