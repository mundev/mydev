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
<div class="es-users-item es-island" data-item data-id="<?php echo $group->id;?>">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<?php echo $this->html('avatar.cluster', $group); ?>
				</div>

				<div class="o-flag__body">
					<?php echo $this->html('html.cluster', $group); ?>
					<div class="es-user-meta">

						<?php if ($this->isMobile()) { ?>
						<div class="o-grid__cell o-grid__cell--auto-size">
							<div role="toolbar" class="btn-toolbar">
								<?php echo $this->html('group.action', $group); ?>

								<?php echo $this->html('group.bookmark', $group); ?>
							</div>
						</div>
						<?php } ?>

						<?php if (!$this->isMobile()) { ?>
						<ol class="g-list-inline g-list-inline--delimited t-text--muted">
							<?php if ($displayType) { ?>
							<li>
								<i class="fa fa-users"></i>&nbsp; <?php echo JText::_('COM_ES_GROUPS');?>
							</li>
							<?php } ?>
							<li>
								<i class="fa fa-users"></i>&nbsp; <?php echo $group->getTotalMembers();?>
							</li>
							<li data-breadcrumb="·">
								<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $group->getCategory()->getPermalink();?>"><?php echo $group->getCategory()->getTitle();?></a>
							</li>
							<li data-breadcrumb="·">
								<i class="fa fa-user"></i>&nbsp; <a href="<?php echo $group->getCreator()->getPermalink();?>"><?php echo $group->getCreator()->getName();?></a>
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
				<?php echo $this->html('group.action', $group); ?>

				<?php echo $this->html('group.bookmark', $group); ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
