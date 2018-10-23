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
<?php if (!$browseView) { ?>
	<?php echo $this->html('cover.user', $user, 'groups'); ?>
<?php } ?>

<?php echo $this->html('responsive.toggle'); ?>
<div class="es-container es-groups" data-es-groups data-es-container>

	<?php echo $this->includeTemplate('site/groups/default/sidebar'); ?>

	<div class="es-content" data-wrapper>
		<?php echo $this->html('html.loading'); ?>

		<div class="es-detecting-location es-island" data-fetching-location>
			<i class="fa fa-globe fa-spin"></i> <span data-detecting-location-message><?php echo JText::_('COM_ES_FINDING_GROUPS_NEARBY'); ?></span>
		</div>

		<?php echo $this->render('module' , 'es-groups-before-contents'); ?>

		<div class="es-group-listing" data-contents>
			<?php echo $this->includeTemplate('site/groups/default/wrapper'); ?>
		</div>

		<?php echo $this->render('module', 'es-groups-after-contents'); ?>
	</div>
</div>
