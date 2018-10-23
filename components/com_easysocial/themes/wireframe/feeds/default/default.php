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
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container <?php echo !$feeds ? ' is-empty' : '';?>" data-es-container data-feeds data-uid="<?php echo $cluster->id;?>" data-app="<?php echo $appId;?>">
	<div class="es-sidebar" data-sidebar>
		<?php if ($cluster->canCreateFeeds()) { ?>
			<a href="javascript:void(0);" class="btn btn-es-primary btn-block t-lg-mb--xl" data-feeds-create><?php echo JText::_('APP_FEEDS_NEW_FEED'); ?></a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted" href="javasc">
							<i class="es-side-widget__icon fa fa-rss t-lg-mr--md"></i>
							<?php echo JText::sprintf('APP_FEEDS_TOTAL_FEEDS', $cluster->getTotalFeeds()); ?>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="app-contents">
			<?php echo $this->html('html.loading'); ?>
			<?php echo $this->html('html.emptyBlock', 'APP_FEEDS_NO_FEED_YET', 'fa-rss'); ?>

			<div data-feeds-lists>
				<?php foreach ($feeds as $feed) { ?>
					<?php echo $this->loadTemplate('site/feeds/default/item', array('cluster' => $cluster, 'feed' => $feed, 'totalDisplayed' => $totalDisplayed, 'user' => $user)); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>