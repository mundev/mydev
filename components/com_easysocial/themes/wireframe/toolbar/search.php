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
<div class="es-toolbar__item es-toolbar__item--search" data-toolbar-search>
	<div id="es-toolbar-search" class="es-toolbar__search">

		<form action="<?php echo JRoute::_('index.php');?>" method="post" class="es-toolbar__search-form">
			
			<input type="text" name="q" class="es-toolbar__search-input" autocomplete="off" data-nav-search-input placeholder="<?php echo JText::_('COM_EASYSOCIAL_TOOLBAR_SEARCH', true);?>" />

			<div class="es-toolbar__search-submit-btn">
				<button class="btn btn-toolbar-search" type="submit">
					<i class="fa fa-search"></i>&nbsp; Search
				</button>
			</div>

			<?php if ($filters) { ?>
			<div class="es-toolbar__search-filter dropdown" data-filters>
				
				<a href="javascript:void(0);" class="btn dropdown-toggle_ es-toolbar__search-filter-toggle" data-bs-toggle="dropdown" data-filter-button>
					<i class="fa fa-cog es-toolbar__search-filter-icon"></i>
				</a>

				<ul class="dropdown-menu dropdown-menu-right es-toolbar__search-dropdown" data-filters-wrapper>
					<li>
						<div class="es-toolbar__search-filter-header">
							<div><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESC');?></div>
						</div>

						<ol class="g-list-inline g-list-inline--delimited">
							<li>
								<a href="javascript:void(0);" data-filter="select"><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_SELECT_ALL'); ?></a> 
							</li>
							<li data-breadcrumb="|">
								<a href="javascript:void(0);" data-filter="deselect"><?php echo JText::_('COM_EASYSOCIAL_SEARCH_FILTER_DESELECT_ALL'); ?></a>
							</li>
						</ol>
					</li>

					<?php foreach ($filters as $filter) { ?>
					<li class="es-toolbar__search-filter-item">
						<div class="o-checkbox">
							<input id="search-type-<?php echo $filter->id;?>" type="checkbox" name="filtertypes[]" value="<?php echo $filter->alias; ?>" data-search-filtertypes />
							<label for="search-type-<?php echo $filter->id;?>">
								<?php echo $filter->displayTitle;?>
							</label>
						</div>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>

			<?php echo $this->html('form.itemid', ESR::getItemId('search')); ?>
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="task" value="query" />
			<input type="hidden" name="option" value="com_easysocial" />
			<input type="hidden" name="<?php echo FD::token();?>" value="1" />
		</form>

		<form class="es-toolbar__search-form t-hidden" method="post" action="/index.php/blog">
			<div class="es-toolbar__search-box">
				<input name="query" class="es-toolbar__search-input" autocomplete="off" placeholder="Search..." value="" type="text">
				<input name="option" value="com_easyblog" type="hidden">
				<input name="task" value="search.query" data-table-grid-task="" type="hidden">
				<input name="35bf1074cbaee2857df57b1205ad6f46" value="1" type="hidden">
				<input name="boxchecked" value="0" data-table-grid-box-checked="" type="hidden">
				<div class="es-toolbar__search-filter-btn">
					<button class="btn btn-default" type="submit">
						<i class="fa fa-search"></i>&nbsp; Search </button>
				</div>
			</div>
			
		</form>
	</div>
</div>