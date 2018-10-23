<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-apps-item es-island">
	<div class="es-apps-item__hd">
		<a href="<?php echo $post->getPermalink();?>" class="es-apps-item__title"><?php echo $post->title; ?></a>	

		<?php if ($user->isViewer()) { ?>
		<div class="es-apps-item__action">
			<div class="btn-group">
				<button type="button" class="dropdown-toggle_ btn btn-es-default-o btn-xs" data-bs-toggle="dropdown">
					<i class="fa fa-caret-down"></i>
				</button>

				<ul class="dropdown-menu dropdown-menu-right">
					<li>
						<a href="<?php echo EB::composer()->getComposeUrl(array('uid' => $post->id . '.' . $post->revision_id, 'return' => $return));?>"><?php echo JText::_('APP_USER_BLOG_EDIT_POST');?></a>
					</li>
					<li>
						<a href="javascript:void(0);" data-post-delete data-id="<?php echo $post->id; ?>"><?php echo JText::_('APP_USER_BLOG_TRASH_POST');?></a>
						<form action="<?php echo JRoute::_('index.php');?>" data-post-trash="<?php echo $post->id;?>" method="post">
							<?php echo $this->html('form.token'); ?>
							<input type="hidden" name="option" value="com_easyblog" />
							<input type="hidden" name="task" value="posts.trash" />
							<input type="hidden" name="id" value="<?php echo $post->id;?>" />
							<input type="hidden" name="return" value="<?php echo $return;?>" />
					   </form>
					</li>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>

	<div class="es-apps-item__bd">

		<div class="o-grid-sm">
			<div class="o-grid-sm__cell">
				<div class="es-apps-item__desc">
					<?php echo $post->content;?>
				</div>
			</div>

			<?php if ($post->image) { ?>
			<div class="o-grid-sm__cell o-grid-sm__cell--auto-size">
				<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);?>" class="t-lg-ml--md">
					<img src="<?php echo $post->image;?>" width="240" height="auto" />
				</a>
			</div>
			<?php } ?>	
		</div>

		<div class="es-apps-item__item-action t-lg-mt--md">
			<a href="<?php echo $post->getPermalink();?>" class="btn btn-es-default-o btn-sm"><?php echo JText::_('COM_ES_VIEW_POST');?></a>
		</div>
	</div>

	<div class="es-apps-item__ft">
		<div class="o-grid">
			<div class="o-grid__cell">
				<div class="es-apps-item__meta">
					<div class="es-apps-item__meta-item">
						<ol class="g-list-inline g-list-inline--dashed">
							<li>
								<i class="fa fa-calendar"></i>&nbsp; <?php echo $this->html('string.date', $post->created, JText::_('DATE_FORMAT_LC1')); ?>
							</li>
							<?php foreach ($post->getCategories() as $category) { ?>
							<li> 
								<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $category->id);?>"><?php echo JText::_($category->title);?></a>
							</li>
							<?php } ?>
							<li>
								<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);?>#comments">
									<i class="fa fa-comments"></i> <?php echo $post->getTotalComments();?> <?php echo JText::_( 'APP_USER_BLOG_COMMENTS' ); ?>
								</a>
							</li>
						</ol>
					</div>
				</div>		
			</div>
		</div>
	</div>
</div>