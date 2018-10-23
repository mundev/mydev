<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-explorer" data-es-explorer data-fd-explorer="<?php echo $uuid;?>" data-uid="<?php echo $uid; ?>" data-type="<?php echo $type; ?>" 
	data-url="site/controllers/explorer/hook" data-controller-name="<?php echo isset($options['controllerName']) ? $options['controllerName'] : 'groups';?>" data-allowed-extensions="<?php echo isset($options['allowedExtensions']) ? $options['allowedExtensions'] : '';?>">

	<div class="fd-explorer-header">
		<?php if ($showUpload) { ?>
			<div class="fd-explorer-header__side">
				<button class="btn btn-es-default-o btn-sm" data-fd-explorer-button="addFolder">
					<i class="fa fa-plus"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_EXPLORER_ADD_FOLDER');?>
				</button>
			</div>
		<?php } ?>
		<?php if ($showUpload || $showClose || $showUse) { ?>
			<div class="fd-explorer-header__content">
				<div class="fd-explorer-browser-action">
				
					<?php if ($cluster->isAdmin() || $cluster->isOwner()) { ?>
						<div class="o-checkbox o-checkbox--inline">
							<input type="checkbox" data-fd-explorer-select-all id="fd-explorer-select-all"/>
							<label class="" for="fd-explorer-select-all">
								&nbsp;
							</label>
						</div>

						<a href="javascript:void(0);" data-fd-explorer-button="removeFile"><?php echo JText::_('COM_EASYSOCIAL_EXPLORER_DELETE_SELECTED');?></a>	
					<?php } ?>
					
					<div class="t-lg-pull-right">
						<?php if ($showUpload) { ?>
							<?php if (isset($options['uploadLimit'])) { ?>
								<span class="upload-limit">
									<?php echo JText::sprintf('COM_EASYSOCIAL_EXPLORER_UPLOAD_LIMIT', $options['uploadLimit']); ?>
								</span>
							<?php } ?>

							<button class="btn btn-es-default-o btn-sm fd-explorer-upload-button" data-plupload-upload-button>
								<i class="fa fa-upload"></i> <?php echo JText::_('COM_EASYSOCIAL_EXPLORER_UPLOAD');?>
							</button>
							
						<?php } ?>

						<?php if ($showClose) { ?>
						<button class="btn btn-link btn-sm pull-right t-text--muted t-lg-ml--xl" data-close>
							<i class="fa fa-remove"></i>
						</button>
						<?php } ?>

						<div class="btn-group t-lg-ml--md t-lg-pull-right">
							<?php if ($showUse) { ?>
							<button class="btn btn-es-default-o btn-sm" data-fd-explorer-button="useFile">
								<i class="fa fa-check"></i>&nbsp; <?php echo JText::_('COM_EASYSOCIAL_EXPLORER_INSERT');?>
							</button>
							<?php } ?>
						</div>
					</div>
					

					<div class="o-loader o-loader--sm"></div>
				</div>
			</div>
		<?php } ?>
		<div class="fd-explorer-sidebar-action pull-left">
			
		</div>
		
	</div>

	<div class="fd-explorer-content">
		<div class="fd-explorer-sidebar">
			<div class="fd-explorer-sidebar__title">
				<?php echo JText::_('COM_EASYSOCIAL_EXPLORER_FOLDERS');?>
			</div>
			<div class="fd-explorer-folder-group"></div>
		</div>
		<div class="fd-explorer-browser">
			<div class="fd-explorer-viewport"></div>
		</div>
	</div>
</div>
