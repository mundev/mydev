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
<div class="es-container">
	<div class="es-sidebar">
		<?php echo $this->render('module', 'es-module-left' , 'site/dashboard/sidebar.module.wrapper'); ?>
	</div>
	
	<div class="es-content">
		
		<?php echo $this->html('cover.user', $user, 'apps.' . $this->vars['app']->element); ?>

		<?php echo $this->html('html.snackbar', 'APP_PSN_USER_TITLE' , 'h2'); ?>

		<div class="is-empty es-island">
			<div class="o-empty">
				<div class="o-empty__content">
					<i class="o-empty__icon fa fa-trophy"></i>
					<div class="o-empty__text">
						<?php if ($user->isViewer()) { ?>
							<?php echo JText::_('APP_USER_YOUR_PSN_NOT_CONNECTED_YET'); ?>
						<?php } else { ?>
							<?php echo JText::_('APP_USER_USER_PSN_NOT_CONNECTED_YET'); ?>
						<?php } ?>
					</div>

					<?php if ($user->isViewer()) { ?>
					<div class="o-empty__action">
						<a href="javascript:void(0);" class="btn btn-es-primary" data-psn-signin>
							<?php echo JText::_('APP_USER_PSN_SIGNIN_BUTTON');?>
						</a>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>