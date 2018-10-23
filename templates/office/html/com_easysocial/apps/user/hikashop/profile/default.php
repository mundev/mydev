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

<div class="es-container" data-es-container data-es-hikashop>
	<div class="es-sidebar" data-sidebar>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-shopping-cart t-lg-mr--md"></i>
							<?php echo JText::sprintf('APP_HIKASHOP_PROFILE_PRODUCTS', count($products)); ?>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="es-content">

		<?php echo $this->html('cover.user', $user, 'apps.' . $this->vars['app']->element); ?>
				
		<div class="app-contents<?php echo !$products ? ' is-empty' : '';?>" data-hikashop-wrapper>
			<?php echo $this->html('html.loading'); ?>
			<?php echo $this->html('html.emptyBlock', 'APP_HIKASHOP_PROFILE_NO_PRODUCTS_CURRENTLY', 'fa-database'); ?>

			<div data-hikashop-contents>
				<?php foreach ($products as $product) { ?>
					<?php echo $this->loadTemplate('themes:/apps/user/hikashop/profile/item', array('product' => $product)); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
