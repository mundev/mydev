<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-es-container data-blog>
	<?php if ($user->isViewer()) { ?>
	<div class="es-sidebar" data-sidebar>
		<a class="btn btn-es-primary btn-block t-lg-mb--xl" href="<?php echo $composeLink;?>" data-eb-composer><?php echo JText::_('APP_USER_BLOG_NEW_POST_BUTTON'); ?></a>
	</div>
	<?php } ?>

	<div class="es-content">
		<div class="app-contents<?php echo !$posts ? ' is-empty' : '';?>" data-app-contents>
			<div data-blog-lists>
				<?php if ($posts) { ?>
					<?php foreach ($posts as $post) { ?>
						<?php echo $this->loadTemplate('themes:/apps/user/blog/profile/item', array('post' => $post, 'user' => $user, 'return' => $return)); ?>
					<?php } ?>
				<?php } ?>
			</div>

			<div class="app-contents-data">
				<hr />
				<div class="fd-cf">
					<span class="pull-right">
						<?php if ($posts) { ?>
							<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $user->id);?>">
								<?php echo JText::sprintf('APP_BLOG_VIEW_ALL_BLOG_POSTS_FROM_USER', $user->getName()); ?>
								<i class="ies-arrow-right-2 ies-small ml-5"></i>
							</a>
						<?php } ?>
					</span>
				</div>
			</div>
			
			<div class="<?php echo !$posts ? 'is-empty' : '';?>">
				<div class="o-empty">
					<div class="o-empty__content">
						<i class="o-empty__icon fa fa-book"></i>
						<div class="o-empty__text"><?php echo $user->getName();?> <?php echo JText::_('APP_BLOG_PROFILE_NO_BLOG_POSTS_CURRENTLY'); ?></div>
						<?php if ($user->isViewer()) { ?>
							<div class="o-empty__action">
									<a class="btn btn-es-primary btn-sm " href="<?php echo EB::composer()->getComposeUrl(array('return' => $return));?>" data-eb-composer><?php echo JText::_('APP_USER_BLOG_NEW_POST_BUTTON'); ?></a>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="mt-20 pagination-wrapper text-center">
				<?php echo $pagination->getListFooter('site'); ?>
			</div>
		</div>
	</div>
</div>