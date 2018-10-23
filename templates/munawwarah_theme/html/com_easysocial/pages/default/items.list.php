<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (isset($heading)) { ?>
	<?php echo $this->html('html.snackbar', $heading); ?>
<?php } ?>

<div class="si-group-items">
	<?php if ($pages) { ?>
		<?php foreach ($pages as $page) { ?>
			<div class="si-group-item <?php echo $page->isFeatured() ? 'is-featured' : ''; ?>">
				<div class="si-group-item__avatar">
					<?php echo $this->html('card.avatar', $page); ?>
				</div>
				<div class="si-group-item__content">
					<div class="">
						<?php echo $this->html('card.title', $page->getTitle(), $page->getPermalink()); ?>
					</div>
					<?php if ($this->config->get('pages.layout.listingdesc')) { ?>
					<div class="si-group-item__desc">
						<?php if ($page->description) { ?>
							<?php echo $this->html('string.truncate', $page->getDescription(), 200, '', false, false, false, true);?>
						<?php } else { ?>
							<?php echo JText::_('COM_EASYSOCIAL_PAGES_NO_DESCRIPTION_YET'); ?>
						<?php }?>
					</div>
					<?php } ?>
					<div class="">
						<ol class="g-list-inline g-list-inline--delimited">
							<li>
								<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $page->getCategory()->getFilterPermalink();?>"><?php echo $page->getCategory()->getTitle();?></a>
							</li>
							<li>
								<?php echo $this->html('page.type', $page, 'top'); ?>
							</li>
							<li>
								<a href="<?php echo $page->getAppPermalink('followers');?>" data-es-provide="tooltip"
									data-original-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_PAGES_LIKERS', $page->getTotalMembers()), $page->getTotalMembers()); ?>""
								>
									<i class="fa fa-thumbs-o-up"></i>&nbsp; <span data-page-like-count-<?php echo $page->id; ?> ><?php echo $page->getTotalMembers();?></span>
								</a>
							</li>
						</ol>
					</div>
				</div>
				<div class="si-group-item__action">
					<?php echo $this->html('card.icon', 'featured', 'COM_EASYSOCIAL_PAGES_FEATURED_PAGES'); ?>

					<?php echo $this->html('page.action', $page); ?>

					<?php if ($page->canAccessActionMenu()) { ?>
					<div class="pull-right dropdown_">
						<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
							<i class="fa fa-ellipsis-h"></i>
						</a>

						<ul class="dropdown-menu">
							<?php echo $this->html('page.adminActions', $page); ?>

							<?php if ($this->html('page.report', $page)) { ?>
							<li>
								<?php echo $this->html('page.report', $page); ?>
							</li>
							<?php } ?>
						</ul>
					</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>

<?php if ($pagination) { ?>
	<?php echo $pagination->getListFooter('site');?>
<?php } ?>