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

<div class="es-container" data-es-advanced data-es-container>
	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('widgets', 'user', 'search', 'sidebarTop'); ?>

		<a href="<?php echo $lib->getLink();?>" class="btn btn-es-primary btn-block t-lg-mb--xl">
			<?php echo JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_NEW_SEARCH');?>
		</a>
		
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_SEARCH_TYPES'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked" data-sidebar-filters>					
					<?php foreach ($adapters as $adapter) { ?>
						<li class="o-tabs__item custom-filter<?php echo $adapter->type == $type ? ' active' : '';?>">
							<a href="<?php echo $adapter->getLink();?>" class="o-tabs__link"><?php echo $adapter->getTitle();?></a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_SEARCH_FILTER'); ?>

			<div class="es-side-widget__bd">
				<?php if ($filters) { ?>
				<ul class="o-tabs o-tabs--stacked" data-sidebar-filters>
					<?php foreach ($filters as $filter) { ?>
						<li class="o-tabs__item custom-filter<?php echo $activeFilter->id == $filter->id ? ' active' : '';?>" data-filter-item="custom" data-id="<?php echo $filter->id;?>">
							<a href="<?php echo $filter->getPermalink(true);?>" class="o-tabs__link" title="<?php echo $this->html('string.escape', $filter->title); ?>">
								<?php echo $filter->_('title'); ?>
							</a>
						</li>
					<?php } ?>
				</ul>
				<?php } else { ?>
					<div class="t-text--muted">
						<?php echo JText::_('COM_EASYSOCIAL_ADVANCED_SEARCH_EMPTY_FILTERS'); ?>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php echo $this->render('widgets', 'user', 'search', 'sidebarBottom'); ?>
	</div>

	<div class="es-content">
		<?php echo $this->html('html.loading'); ?>

		<div data-contents>
			<?php echo $this->includeTemplate('site/search/advanced/contents'); ?>
		</div>
	</div>
</div>