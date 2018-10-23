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
<div class="es-users-item" data-item data-id="<?php echo $cluster->id;?>" data-type="<?php echo $cluster->getType();?>">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<?php echo $this->html('avatar.cluster', $cluster); ?>
				</div>

				<div class="o-flag__body">
					<a href="<?php echo $cluster->getEditPermalink();?>" class="es-user-name"><?php echo $cluster->getTitle();?></a>
					<div class="es-user-meta">
						<ol class="g-list-inline g-list-inline--delimited es-user-item-meta">							
						</ol>
					</div>
				</div>
			</div>
		</div>

		<div class="o-grid__cell o-grid__cell--auto-size">
			<div role="toolbar" class="btn-toolbar t-lg-mt--sm">
				<a href="javascript:void(0);" class="btn btn-sm btn-es-primary-o" data-approve>
					<?php echo JText::_('COM_EASYSOCIAL_APPROVE_BUTTON'); ?>
				</a>
				<a href="javascript:void(0);" class="btn btn-sm btn-es-danger-o" data-reject>
					<?php echo JText::_('COM_EASYSOCIAL_REJECT_BUTTON'); ?>
				</a>
			</div>
		</div>
	</div>
</div>
