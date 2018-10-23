<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

EB::responsive()->isIphone();
?>
<form name="composer" method="post" action="<?php echo JRoute::_('index.php');?>" data-composer-form autocomplete="off">
	<div class="eb-comp
		<?php echo $this->isMobile() ? '' : ' is-sidebar-open';?>
		<?php if (!$templateEditor) { ?>
			<?php echo $post->isBlank() && $this->config->get('composer_templates') && $postTemplates ? ' show-templates' : '';?>
			<?php echo $draftRevision ? ' warning-draft' : ''; ?>
			<?php echo $post->renderClassnames(); ?>
		<?php } else { ?>
		is-editing-template
		<?php } ?>
		"
	data-composer-manager>

		<div class="eb-comp-mobile-footbar">
			<?php if ((!$post->isLegacy() && !$templateEditor) || ($templateEditor && !$postTemplate->isLegacy())) { ?>
			<div class="eb-comp-mobile-footbar__item is-active">
				<a href="javascript:void(0);" class="eb-comp-mobile-footbar__link" data-toolbar-blocks>
					<i class="fa fa-cube"></i>
					<div class="eb-comp-mobile-footbar__link-text"><?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?></div>
				</a>
			</div>
			<?php } ?>
			<div class="eb-comp-mobile-footbar__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-footbar__link" data-eb-composer-media data-uri="post">
					<i class="fa fa-camera"></i>
					<div class="eb-comp-mobile-footbar__link-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_MEDIA');?></div>
				</a>
			</div>
			<div class="eb-comp-mobile-footbar__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-footbar__link" data-composer-mobile-cover>
					<i class="fa fa-photo"></i>
					<div class="eb-comp-mobile-footbar__link-text"><?php echo JText::_('COM_EASYBLOG_POST_COVER');?></div>
				</a>
			</div>

			<div class="eb-comp-mobile-footbar__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-footbar__link" data-composer-mobile-posts>
					<i class="fa fa-file-text-o"></i>
					<div class="eb-comp-mobile-footbar__link-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_SIDEBAR_TITLE_POSTS');?></div>
				</a>
			</div>

			<?php if ($this->config->get('main_locations')) { ?>
			<div class="eb-comp-mobile-footbar__item">
				<a href="javascript:void(0);" class="eb-comp-mobile-footbar__link" data-composer-mobile-location>
					<i class="fa fa-map-marker"></i>
					<div class="eb-comp-mobile-footbar__link-text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_LOCATION');?></div>
				</a>
			</div>
			<?php } ?>
		</div>
		
		<div class="eb-comp__body">
			<?php echo $this->output('site/composer/toolbar/default'); ?>

			<div class="o-alert o-alert--eb-composer o-alert--dismissible t-lg-mb--no <?php echo $alert ? 'o-alert--' . $alert->type : 't-hidden';?>" data-composer-alert>
				<button type="button" class="o-alert__close" data-bp-dismiss="alert">
					<span aria-hidden="true">×</span>
				</button>
				<span data-composer-alert-message>
					<?php if ($alert) { ?>
						<?php echo $alert->text;?>
					<?php } ?>
				</span>
			</div>

			<div class="eb-revision-bar">
				<div class="eb-revision-bar__title">
					<div class="eb-revision-bar__txt" data-revisions-comparison-title></div>
				</div>

				<div class="eb-revision-bar__action">
					<div class="eb-revision-bar__close">
						<a href="javascript:void(0);" data-revisions-close-comparison>
							<i class="fa fa-times-circle"></i>
						</a>
					</div>
				</div>
			</div>

			<div class="eb-composer-views" data-eb-composer-views>
				<div class="eb-composer-view eb-composer-document active" data-eb-composer-view data-name="document" data-eb-composer-document>
					<div class="eb-composer-viewport">
						<div class="eb-composer-viewport-content">
							<div class="eb-composer-page">
								<div class="eb-composer-page-viewport" data-eb-composer-page-viewport>
									<div class="eb-composer-page-header" data-eb-composer-page-header>
										<div class="eb-composer-page-meta row-table">
											<div class="col-cell<?php echo $templateEditor ? ' hide' : ''; ?>">
												<div class="o-form-group eb-composer-field-primary-category" data-category-primary>
													<div class="dropdown_">
														<div class="dropdown-toggle_ eb-composer-page-meta-text" data-bp-toggle="dropdown">
															<span class="eb-composer-primary-category-title" data-category-primary-title><?php echo $primaryCategory->getTitle();?></span>
															<i class="fa fa-caret-down"></i>
															<input type="hidden" name="category_id" value="<?php echo $primaryCategory->id;?>" data-category-primary-input />
														</div>
														<ul class="dropdown-menu" role="menu" data-category-primary-items></ul>
													</div>
												</div>

												<div class="hide" data-category-primary-template>
													<li data-category-primary-item data-title="" data-id="">
														<a href="javascript:void(0);" data-title-text></a>
													</li>
												</div>
											</div>
										</div>

										<div class="eb-composer-field-title">
											<textarea name="title" placeholder="<?php echo JText::_('COM_EASYBLOG_DASHBOARD_WRITE_DEFAULT_TITLE'); ?>" data-post-title><?php echo $this->html('string.escape', $post->title); ?></textarea>
										</div>

										<div class="eb-comp-permalink" data-eb-editor-permalink>
											<div class="eb-comp-permalink__label">
												<?php echo JText::_('COM_EASYBLOG_COMPOSER_PERMALINK');?>
											</div>

											<div class="eb-comp-permalink__preview">
												<div class="eb-comp-permalink__sample">
													<a href="javascript:void(0);" class="eb-comp-permalink__post-name" data-permalink-preview>
														<?php echo $post->permalink;?>
														<?php if ($post->permalink) { ?>
														<i class="fa fa-external-link" aria-hidden="true"></i>
														<?php } ?>
													</a>
													<div class="eb-comp-permalink__edit-field">
														<input type="text" class="o-form-control" name="permalink" value="<?php echo $this->html('string.escape', $post->permalink);?>" data-permalink-input />
													</div>
												</div>

												<div class="eb-comp-permalink__edit-action">
													<?php if ($this->config->get('layout_composer_permalink')) { ?>
														<a href="javascript:void(0);" class="btn btn-eb-default-o btn-permalink--edit btn--xs" data-permalink-edit><?php echo JText::_('COM_EASYBLOG_EDIT_POST_PERMALINK'); ?></a>
													<?php } ?>

													<a href="javascript:void(0);" class="btn btn-eb-primary-o btn-permalink--confirm btn--xs" data-permalink-update>
														<i class="fa fa-check"></i>
													</a>
													<a href="javascript:void(0);" class="btn btn-eb-default-o btn-permalink--cancel btn--xs" data-permalink-cancel>
														<i class="fa fa-close"></i>
													</a>
												</div>
											</div>
										</div>
									</div>

									<div class="eb-composer-page-body eb-editor--<?php echo $this->config->get('layout_editor'); ?>" data-eb-composer-page-body>
										<?php if ($post->isLegacy() || ($templateEditor && $postTemplate->isLegacy())) { ?>
											<?php  echo $this->output($legacyEditorNamespace); ?>
										<?php } else { ?>
											<?php echo $this->output('site/composer/editor/ebd'); ?>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="hide" data-eb-block-template>
					<?php echo $this->output('site/document/blocks/editable'); ?>
				</div>

				<div class="eb-composer-view eb-composer-revisions" data-eb-composer-view data-name="revisions" data-eb-composer-revisions>
					<div data-eb-composer-revisions-compare-screen></div>
				</div>
			</div>
		</div>
		
		<div class="eb-composer-blocks" data-eb-composer-blocks>
			<div class="eb-composer-blocks__hd">
				<?php echo JText::_('COM_EASYBLOG_INSERT_BLOCK');?>
				<div class="eb-composer-blocks__hd-action">
					<a href="javascript:void(0);" class="eb-composer-blocks__close" data-toolbar-blocks-close>
						<i class="fa fa-times-circle"></i>
					</a>
				</div>
			</div>
			<div class="eb-composer-blocks__bd">

				<div class="eb-composer-blocks__search">
					<input type="text" placeholder="<?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_SEARCH');?>" data-eb-blocks-search />
				</div>

				<div class="eb-composer-blocks-group-container">

					<?php foreach($blocks as $category => $blockItems) { ?>
						<?php if ((count($blockItems) == 1 && $blockItems[0]->visible == true) || count($blockItems) > 1) { ?>
						<div class="eb-composer-fieldset">
							<div class="eb-composer-fieldset-header">
								<strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_CATEGORY_' . strtoupper($category)); ?></strong>
							</div>
							<div class="eb-composer-fieldset-content">
								<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
									<?php foreach ($blockItems as $block) { ?>
									<div class="eb-composer-block-menu ebd-block<?php echo !$block->visible ? ' is-hidden' : '';?>" data-eb-composer-block-menu data-type="<?php echo $block->type; ?>" data-keywords="<?php echo $block->keywords; ?>">
										<div>
											<i class="<?php echo $block->icon; ?>"></i>
											<span><?php echo $block->title; ?></span>
										</div>
										<textarea data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
									</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<?php } else if (count($blockItems) == 1 && $blockItems[0]->visible != true) { ?>
						<div class="eb-composer-block-menu-group" data-eb-composer-block-menu-group>
							<?php foreach ($blockItems as $block) { ?>
							<div class="eb-composer-block-menu ebd-block<?php echo !$block->visible ? ' is-hidden' : '';?>" data-eb-composer-block-menu data-type="<?php echo $block->type; ?>" data-keywords="<?php echo $block->keywords; ?>">
								<div>
									<i class="<?php echo $block->icon; ?>"></i>
									<span><?php echo $block->title; ?></span>
								</div>
								<textarea data-eb-composer-block-meta data-type="<?php echo $block->type; ?>"><?php echo json_encode($block->meta(), JSON_HEX_QUOT | JSON_HEX_TAG); ?></textarea>
							</div>
							<?php } ?>
						</div>
						<?php } ?>
					<?php } ?>

					<div class="o-empty">
						<div class="o-empty__content">
							<i class="o-empty__icon fa fa-cube"></i>
							<div class="o-empty__text"><?php echo JText::_('COM_EASYBLOG_COMPOSER_BLOCKS_NOT_FOUND'); ?></div>
						</div>
					</div>

					<div class="o-loader o-loader--top"></div>
				</div>
			</div>
		</div>
		<div class="eb-comp__side">
			<div class="eb-comp-side-content">
				<?php echo $this->output('site/composer/sidebar/panels'); ?>
			</div>
		</div>

		<?php if (!$templateEditor && $totalTemplates > 1) { ?>
			<?php echo $this->output('site/composer/templates'); ?>
		<?php } ?>


		<?php if ($draftRevision) { ?>
			<?php echo $this->output('site/composer/revisions/draft.warning'); ?>
		<?php } ?>
	</div>

	<input type="hidden" name="document" value="" data-composer-field-document />
	<input type="hidden" name="published" value="<?php echo $post->published; ?>" data-composer-field-published />
	<input type="hidden" name="preview" value="0" data-composer-field-preview />
	<input type="hidden" name="return" value="<?php echo $returnUrl; ?>" />

	<?php if ($templateEditor) { ?>
		<input type="hidden" name="template_id" value="<?php echo $postTemplate ? $postTemplate->id : '';?>" data-eb-composer-template-id />
		<?php echo $this->html('form.action', 'templates.save'); ?>
	<?php } else { ?>
		<input type="hidden" name="uid" value="<?php echo $post->uid; ?>" data-eb-composer-post-uid />
		<input type="hidden" name="id" value="<?php echo $post->id; ?>" data-eb-composer-post-id />
		<input type="hidden" name="revision_id" value="<?php echo $post->revision->id; ?>" data-eb-composer-revision-id />
		<?php echo $this->html('form.action', 'posts.save'); ?>
	<?php } ?>
</form>

<?php echo EB::mediamanager()->render();?>
