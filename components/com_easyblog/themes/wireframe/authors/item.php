<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php if ($author->custom_css && $author->getAcl()->get('custom_css')) { ?>
<style type="text/css">
<?php echo $author->custom_css;?>
</style>
<?php } ?>

<?php if ($this->params->get('author_header', true)) { ?>
	<div class="eb-author" data-author-item data-id="<?php echo $author->id;?>">
		<?php echo $this->html('headers.author', $author, array(
																	'name' => $this->params->get('author_name', true),
																	'avatar' => $this->params->get('author_avatar', true), 
																	'rss' => $author->id != $this->my->id,
																	'subscription' => $author->id != $this->my->id,
																	'twitter' => $this->params->get('author_twitter', true),
																	'website' => $this->params->get('author_website', true),
																	'biography' => $this->params->get('author_bio', true),
																	'featureAction' => false
															)
		); ?>
	</div>
<?php } ?>

<div class="eb-posts" data-blog-posts>
	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>
			<?php if (!EB::isSiteAdmin() && $this->config->get('main_password_protect') && !empty($post->blogpassword) && !$post->verifyPassword()) { ?>
				<?php echo $this->output('site/blogs/latest/default.protected', array('post' => $post)); ?>
			<?php } else { ?>
				<?php echo $this->output('site/blogs/latest/default.main', array('post' => $post)); ?>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-info-circle"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_BLOG_ENTRY');?>
		</div>
	<?php } ?>
</div>


<?php if($pagination) {?>
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<?php echo $pagination;?>

	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
