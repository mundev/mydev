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
<div class="es-users-item es-island" data-item data-id="<?php echo $event->id;?>">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<?php echo $this->html('avatar.cluster', $event); ?>
				</div>

				<div class="o-flag__body">
					<?php echo $this->html('html.cluster', $event); ?>
					<div class="es-user-meta">

						<?php if ($this->isMobile()) { ?>
						<div class="o-grid__cell o-grid__cell--auto-size">
							<div role="toolbar" class="btn-toolbar">
								<?php echo $this->html('event.action', $event); ?>

								<?php echo $this->html('event.bookmark', $event); ?>
							</div>
						</div>
						<?php } ?>

						<?php if (!$this->isMobile()) { ?>
						<ol class="g-list-inline g-list-inline--delimited t-text--muted">
							<?php if ($displayType) { ?>
							<li>
								<i class="fa fa-calendar"></i>&nbsp; <?php echo JText::_('COM_ES_EVENTS');?>
							</li>
							<?php } ?>
							<li>
								<i class="fa fa-users"></i>&nbsp; <?php echo $event->getTotalMembers();?>
							</li>
							<li data-breadcrumb="·">
								<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $event->getCategory()->getPermalink();?>"><?php echo $event->getCategory()->getTitle();?></a>
							</li>
							<li data-breadcrumb="·">
								<i class="fa fa-user"></i>&nbsp; <a href="<?php echo $event->getCreator()->getPermalink();?>"><?php echo $event->getCreator()->getName();?></a>
							</li>
							<li data-breadcrumb="·">
								<i class="fa fa-calendar"></i>&nbsp; <?php echo $event->getStartEndDisplay(array('timezone' => false, 'starttime' => false, 'endtime' => false)); ?>
							</li>
						</ol>
						<?php } ?>
					</div>
				</div>
			</div>        
		</div>
		
		<?php if (!$this->isMobile()) { ?>
		<div class="o-grid__cell o-grid__cell--auto-size">
			<div role="toolbar" class="btn-toolbar t-lg-mt--sm">
				<?php echo $this->html('event.action', $event); ?>

				<?php echo $this->html('event.bookmark', $event); ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
