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
<?php if (!$page->isPending()) { ?>
	<?php echo $this->html('cover.page', $page, 'info'); ?>
<?php } ?>

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container es-pages-edit" data-pages-edit data-es-container>

	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-pages-edit-sidebar-top'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_PROFILE_SIDEBAR_ABOUT'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<?php $i = 0; ?>
					<?php foreach ($steps as $step) { ?>
						<li class="o-tabs__item <?php echo $i == 0 ? ' active' :'';?>" data-page-edit-fields-step data-for="<?php echo $step->id;?>">
							<a href="javascript:void(0);" class="o-tabs__link"><?php echo $step->get('title'); ?></a>
						</li>
						<?php $i++; ?>
					<?php } ?>

					<?php if ($page->isDraft()) { ?>
						<li class="o-tabs__item" data-page-edit-fields-step data-for="history">
							<a href="javascript:void(0);" class="o-tabs__link"><?php echo JText::_('COM_ES_APPROVAL_HISTORY'); ?></a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php echo $this->render('module', 'es-pages-edit-sidebar-bottom'); ?>
	</div>

	<div class="es-content">
		<?php echo $this->render('module', 'es-pages-edit-before-contents'); ?>

		<div data-page-edit-fields>
			<form method="post" action="<?php echo JRoute::_('index.php'); ?>" class="es-forms" data-page-fields-form>
				<div class="tab-content">
					<div class="tab-content__item" data-page-edit-fields-content data-id="history">
						<div class="es-forms__group">
							<div class="es-forms__content">
								<?php if ($page->isDraft()) { ?>
									<?php echo $this->html('cluster.approvalHistory', $rejectedReasons); ?>
								<?php } ?>
							</div>
						</div>
					</div>

					<?php $i = 0; ?>
					<?php foreach($steps as $step){ ?>
						<div class="tab-content__item step-<?php echo $step->id;?> <?php echo $i == 0 ? 'is-active' : '';?>" data-page-edit-fields-content data-id="<?php echo $step->id; ?>">
							<div class="es-forms__group">
								<div class="es-forms__content">
									<div class="o-form-horizontal">
										<?php if($step->fields){ ?>
											<?php foreach($step->fields as $field){ ?>
												<?php echo $this->loadTemplate('site/registration/steps/field', array('field' => $field, 'errors' => '')); ?>
											<?php } ?>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
						<?php $i++; ?>
					<?php } ?>
				</div>

				<div class="es-forms__actions">
					<div class="o-form-actions">
						<?php if (!$page->isPending()) { ?>				
							<a href="<?php echo $page->getPermalink();?>" class="btn btn-es-default-o t-lg-pull-left"><?php echo JText::_('COM_ES_CANCEL'); ?></a>
							<button type="button" class="btn btn-es-primary-o t-lg-pull-right" data-task="update" data-page-fields-submit><?php echo JText::_('COM_ES_UPDATE');?></button>
						<?php } else { ?>
							<a href="<?php echo ESR::manage(array('layout' => 'clusters'));?>" class="btn btn-es-default-o t-lg-pull-left"><?php echo JText::_('COM_EASYSOCIAL_CLOSE_BUTTON'); ?></a>
							
							<button type="button" class="btn btn-es-danger-o t-lg-pull-right" data-task="reject" data-page-fields-submit><?php echo JText::_('COM_EASYSOCIAL_REJECT_BUTTON');?></button>
							<button type="button" class="btn btn-es-primary-o t-lg-pull-right" data-task="approve" data-page-fields-submit><?php echo JText::_('COM_EASYSOCIAL_APPROVE_BUTTON');?></button>
						<?php } ?>
					</div>
				</div>

				<input type="hidden" name="conditionalRequired" value="<?php echo ES::string()->escape($conditionalFields); ?>" data-conditional-check />
				<input type="hidden" name="Itemid" value="<?php echo JRequest::getInt('Itemid');?>" />
				<input type="hidden" name="option" value="com_easysocial" />
				<input type="hidden" name="controller" value="pages" />
				<input type="hidden" name="task" value="update" data-task-hidden-input/>
				<input type="hidden" name="id" value="<?php echo $page->id;?>" />
				<input type="hidden" name="<?php echo ES::token();?>" value="1" />
			</form>
		</div>

		<?php echo $this->render('module', 'es-pages-edit-after-contents'); ?>
	</div>
</div>
