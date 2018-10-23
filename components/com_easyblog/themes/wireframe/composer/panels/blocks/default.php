<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-panel is-multipanel eb-composer-panel-blocks is-empty <?php echo $templateEditor ? 'active' : ''; ?>" data-eb-composer-panel data-id="blocks">
	<div class="eb-composer-panel-content">

		<div class="eb-composer-subpanel eb-composer-blocks-block-subpanel" data-eb-composer-blocks-block-subpanel>

			<div class="eb-composer-subpanel-content" data-scrolly="y">
				<div data-scrolly-viewport>

					<div class="eb-composer-fieldset eb-blocks-tree-field tree-minimal" data-eb-blocks-tree-field>
						<div class="eb-block o-grid-sm" data-eb-block>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size eb-block-icon">
								<i class="fa fa-suqare-o" data-eb-block-icon></i>
							</div>
							<div class="o-grid-sm__cell t-lg-pl--md">
								<div class="eb-block-title" data-eb-block-title><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS');?></div>
								<div class="eb-block-stat text-muted" data-eb-block-stat>
									<span class="eb-block-level"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TREE_LEVEL'); ?> <span class="eb-block-level-count" data-eb-block-level-count><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LEVEL_COUNT_ONE');?></span></span>
									<span class="eb-block-child"> &middot; <span class="eb-block-child-count" data-eb-block-child-count><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_LEVEL_COUNT_ZERO');?></span> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_TREE_CHILD_BLOCKS'); ?></span>
								</div>
							</div>
							<div class="o-grid-sm__cell o-grid-sm__cell--auto-size">
								<button type="button" class="btn btn-eb-default btn--sm eb-blocks-tree-toggle-button" data-eb-blocks-tree-toggle-button><span class="text-show-tree"><i class="fa fa-plus-square"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SHOW_FULL_TREE'); ?></span><span class="text-hide-tree"><i class="fa fa-minus-square"></i> <?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_HIDE_FULL_TREE'); ?></span></button>
							</div>
						</div>
						<div class="eb-blocks-tree" data-eb-blocks-tree>
							<div class="eb-list">
								<div class="eb-list-item-group" data-eb-blocks-tree-item-group>
								</div>
							</div>
						</div>
						<div class="hide" data-eb-blocks-tree-item-template>
							<div class="eb-list-item eb-blocks-tree-item" data-eb-blocks-tree-item data-type="" data-uid="">
								<i class="" data-eb-blocks-tree-item-icon></i>
								<strong data-eb-blocks-tree-item-title></strong>
							</div>
						</div>
					</div>


					<div class="eb-composer-blocks-prop-group" data-eb-composer-blocks-prop-group data-type="specific"></div>

					<div class="eb-composer-fieldset" data-name="font" style="display: none;">
						<div class="eb-composer-fieldset-header">
							<strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_FONT_HEADER'); ?></strong>
						</div>
						<div class="eb-composer-fieldset-content o-form-horizontal">
							<?php echo $this->output('site/composer/fields/font'); ?>
							<?php
								$layout = array(
									array(
										'class' => 'group-basic',
										'actions' => array('bold', 'italic', 'underline', 'strikethrough')
									),
									array(
										'class' => 'group-alignment',
										'actions' => array('alignleft', 'aligncenter', 'alignright', 'justify')
									)
								);
								echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-text', 'layout' => $layout));
							?>
							<?php
								$layout = array(
									array(
										'class' => 'group-list',
										'actions' => array('orderedlist', 'unorderedlist')
									),
									array(
										'class' => 'group-indent',
										'actions' => array('outdent', 'indent')
									)
								);
								echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-list', 'layout' => $layout));
							?>
						</div>
					</div>

					<div class="eb-composer-fieldset" data-name="dimensions" style="display: none;">
						<div class="eb-composer-fieldset-header">
							<strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_DIMENSIONS'); ?></strong>
						</div>
						<div class="eb-composer-fieldset-content o-form-horizontal">
							<div class="o-form-group eb-dimensions style-bordered" data-type="dimensions">
								<?php
									echo $this->output('site/composer/fields/numslider', array(
										'name' => 'width',
										'label' => JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_WIDTH'),
										'attributes' => 'data-eb-block-dimensions-field',
										'container' => 'data-eb-block-dimensions-field-container'
									));
								?>
								<?php
									echo $this->output('site/composer/fields/numslider', array(
										'name' => 'height',
										'label' => JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_HEIGHT'),
										'attributes' => 'data-eb-block-dimensions-field',
										'container' => 'data-eb-block-dimensions-field-container',
										'units' => array(
											array(
												'title'   => 'COM_EASYBLOG_PIXEL',
												'type'    => 'pixel',
												'unit'    => '%',
												'default' => true
											)
										)
									));
								?>
							</div>
						</div>
					</div>

					<div class="eb-composer-fieldset" data-name="text" style="display: none;">
						<div class="eb-composer-fieldset-header">
							<strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCK_TEXT_SELECTION'); ?></strong>
						</div>
						<div class="eb-composer-fieldset-content o-form-horizontal">
							<?php echo $this->output('site/composer/fields/font'); ?>
							<?php
								$layout = array(
									array(
										'class' => 'group-basic',
										'actions' => array('bold', 'italic', 'underline', 'strikethrough', 'code')
									),
									array(
										'class' => 'group-subsup',
										'actions' => array('subscript', 'superscript')
									),
									array(
										'class' => 'group-clear',
										'actions' => array('clear')
									)
								);
								echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-text', 'layout' => $layout));
							?>
							<?php
								$layout = array(
									array(
										'class' => 'group-list',
										'actions' => array('orderedlist', 'unorderedlist')
									),
									array(
										'class' => 'group-indent',
										'actions' => array('outdent', 'indent')
									)
								);
								echo $this->output('site/composer/fields/font_formatting', array('classname' => 'section-list', 'layout' => $layout));
							?>
						</div>
					</div>

					<div class="eb-composer-fieldset" data-name="links" style="display: none;">
						<div class="eb-composer-fieldset-header">
							<strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_FORMATTER_LINKS'); ?></strong>
						</div>
						<div class="eb-composer-fieldset-content o-form-horizontal">
							<?php echo $this->output('site/composer/fields/links'); ?>
						</div>
					</div>

					<?php if ($this->config->get('layout_composer_customcss')) { ?>
					<div class="eb-composer-fieldset" data-name="css" style="display: none;">
						<div class="eb-composer-fieldset-header">
							<strong><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CUSTOM_CSS'); ?></strong>
						</div>
						<div class="eb-composer-fieldset-content o-form-horizontal">
							<div class="o-form-group">
								<label for="custom_css" class="o-control-label eb-composer-field-label">
									<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_CUSTOM_CSS_WRAPPER_CLASS'); ?>
								</label>
								<div class="o-control-input">
									<input id="custom_css" type="text" class="o-form-control" value="" autocomplete="off" data-eb-composer-blocks-css />
								</div>
							</div>
						</div>
					</div>
					<?php } ?>

					<div class="eb-hint style-gray layout-overlay hint-empty">
						<div>
							<i class="eb-hint-icon fa fa-cube"></i>
							<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SELECT'); ?></span>
						</div>
					</div>

				</div>
			</div>
		</div>


		<div class="eb-composer-subpanel eb-composer-blocks-removal-subpanel" data-eb-composer-blocks-removal-subpanel>
			<div>
				<div class="eb-hint layout-overlay style-gray">
					<div>
						<i class="eb-hint-icon fa fa-trash-o"></i>
						<span class="eb-hint-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_DROP_BLOCK_TO_REMOVE_BLOCK');?></span>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>