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

<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-container data-profile-user-apps-notes data-app-id="<?php echo $app->id;?>">
	<div class="es-sidebar">
		<?php if ($user->isViewer()) { ?>
			<a href="javascript:void(0);" class="btn btn-es-primary btn-block t-lg-mb--xl" data-notes-create><?php echo JText::_('APP_NOTES_NEW_NOTE_BUTTON'); ?></a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-sticky-note-o t-lg-mr--md"></i>
							<b><?php echo $total;?></b> <?php echo JText::_('COM_ES_NOTES');?>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="app-notes <?php echo !$notes ? ' is-empty' : '';?>" data-notes-list>
			<?php if ($notes) { ?>
				<?php foreach ($notes as $note) { ?>
					<?php echo $this->output('site/notes/profile/item', array('note' => $note, 'user' => $user)); ?>
				<?php } ?>
			<?php } ?>

			<?php echo $this->html('html.emptyBlock', JText::sprintf('APP_NOTES_EMPTY_NOTES_PROFILE', $user->getName()), 'fa-info-circle'); ?>
		</div>
	</div>
</div>
