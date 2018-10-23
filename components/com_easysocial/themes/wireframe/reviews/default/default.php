<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-container data-es-reviews data-id="<?php echo $cluster->id; ?>">
	<div class="es-sidebar" data-sidebar>
		<?php if ($cluster->canSubmitReview()) { ?>
		<a href="<?php echo ESR::apps(array('layout' => 'canvas', 'customView' => 'form', 'uid' => $cluster->getAlias(), 'type' => $cluster->getType(), 'id' => $app->getAlias()));?>" class="btn btn-es-primary btn-block t-lg-mb--xl">
			<?php echo JText::_('APP_REVIEWS_SUBMIT'); ?>
		</a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted" href="javasc">
							<i class="es-side-widget__icon fa fa-star t-lg-mr--md"></i>
							<?php echo JText::sprintf('APP_REVIEWS_RATINGS', $cluster->getAverageRatings()); ?>
						</span>
					</li>

					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted" href="javasc">
							<i class="es-side-widget__icon fa fa-gift t-lg-mr--md"></i>
							<?php echo JText::sprintf('APP_REVIEWS_SUBMITTED_REVIEWS', $cluster->getTotalReviews()); ?>
						</span>
					</li>
				</ul>
			</div>
		</div>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item active" data-review-filter="all">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_DISCUSSIONS_FILTER_ALL');?>
						</a>
					</li>

					<?php if ($cluster->isAdmin()) { ?>
					<li class="o-tabs__item has-notice" data-review-filter="pending">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('COM_ES_PENDING');?>
							<div class="o-tabs__bubble" data-counter><?php echo $cluster->getTotalReviews(array('pending' => true)); ?></div>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="es-reviews app-contents<?php echo !$items ? ' is-empty' : '';?>" data-reviews-wrapper>
			<?php echo $this->html('html.loading'); ?>
			<?php echo $this->html('html.emptyBlock', 'APP_REVIEWS_EMPTY', 'fa-database'); ?>

			<div data-reviews-contents>
				<?php foreach ($items as $review) { ?>
					<?php echo $this->loadTemplate('site/reviews/default/items', array('review' => $review, 'params' => $params, 'cluster' => $cluster)); ?>
				<?php } ?>

				<?php echo $pagination->getListFooter('site'); ?>
			</div>
		</div>
	</div>
</div>