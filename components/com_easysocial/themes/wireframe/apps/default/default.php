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
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-apps data-es-container>

	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-apps-sidebar-top'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_APPS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item <?php echo $filter == 'browse' ? ' active' :'';?>" data-filter-item="all">
						<a href="<?php echo ESR::apps();?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_BROWSE_APPS', true);?>" data-apps-filter-link class="o-tabs__link">
							<?php echo JText::_('COM_EASYSOCIAL_APPS_BROWSE_APPS');?>
						</a>
					</li>
					<li class="o-tabs__item <?php echo $filter == 'mine' ? ' active' :'';?>" data-filter-item="mine">
						<a href="<?php echo ESR::apps(array('filter' => 'mine'));?>" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_YOUR_APPS', true);?>" data-apps-filter-link class="o-tabs__link">
							<?php echo JText::_('COM_EASYSOCIAL_APPS_YOUR_APPS');?>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<?php echo $this->render('module', 'es-apps-sidebar-bottom'); ?>
	</div>

	<div class="es-content<?php echo !$apps ? " is-empty " : ''; ?>" data-wrapper>
		<?php echo $this->render('module', 'es-apps-before-contents'); ?>

		<?php echo $this->html('html.loading'); ?>

		<div data-contents>
			<?php echo $this->includeTemplate('site/apps/default/items'); ?>
		</div>

		<?php echo $this->html('html.emptyBlock', 'COM_EASYSOCIAL_APPS_NO_APPS_INSTALLED_YET', 'fa-database'); ?>

		<?php echo $this->render('module', 'es-apps-after-contents'); ?>
	</div>
</div>
