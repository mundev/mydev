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

<div class="es-container" data-es-container data-es-discussions data-id="<?php echo $cluster->id;?>">
	<div class="es-sidebar" data-sidebar>

		<?php if ($cluster->canCreateDiscussion()) { ?>
		<a href="<?php echo ESR::apps(array('layout' => 'canvas', 'uid' => $cluster->getAlias(), 'type' => $cluster->getType(), 'id' => $app->getAlias(), 'customView' => 'create')); ?>" class="btn btn-es-primary btn-block t-lg-mb--xl">
			<?php echo JText::_('APP_GROUP_DISCUSSIONS_CREATE_DISCUSSION'); ?>
		</a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item has-notice active" data-discussion-filter="all">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_DISCUSSIONS_FILTER_ALL');?>
						</a>
						<div class="o-tabs__bubble"><?php echo $counters['total'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-discussion-filter="unanswered">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_DISCUSSIONS_FILTER_UNANSWERED');?>
						</a>
						<div class="o-tabs__bubble"><?php echo $counters['unanswered'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-discussion-filter="resolved">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_DISCUSSIONS_FILTER_RESOLVED');?>
						</a>
						<div class="o-tabs__bubble"><?php echo $counters['resolved'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-discussion-filter="unresolved">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_DISCUSSIONS_FILTER_UNRESOLVED');?>
						</a>
						<div class="o-tabs__bubble"><?php echo $counters['unresolved'];?></div>
					</li>

					<li class="o-tabs__item has-notice" data-discussion-filter="locked">
						<a href="javascript:void(0);" class="o-tabs__link">
							<?php echo JText::_('APP_GROUP_DISCUSSIONS_FILTER_LOCKED');?>
						</a>
						<div class="o-tabs__bubble"><?php echo $counters['locked'];?></div>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="es-app-discussions app-contents<?php echo !$discussions ? ' is-empty' : '';?>" data-discussion-wrapper>
			<?php echo $this->html('html.loading'); ?>
			<?php echo $this->html('html.emptyBlock', 'APP_GROUP_DISCUSSIONS_EMPTY', 'fa-database'); ?>

			<div data-discussion-contents>
				<?php foreach ($discussions as $discussion) { ?>
					<?php echo $this->loadTemplate('site/discussions/default/items', array('discussion' => $discussion, 'params' => $params)); ?>
				<?php } ?>

				<?php echo $pagination->getListFooter('site'); ?>
			</div>
		</div>
	</div>
</div>
