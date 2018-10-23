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

<div class="es-container" data-article>
	<div class="es-sidebar">
		<?php if ($user->isViewer()) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_content&view=form&layout=edit');?>" class="btn btn-es-primary btn-block t-lg-mb--xl"><?php echo JText::_('COM_ES_NEW_ARTICLE'); ?></a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_STATISTICS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-nav o-nav--stacked">
					<li class="o-nav__item t-lg-mb--sm">
						<span class="o-nav__link t-text--muted">
							<i class="es-side-widget__icon fa fa-sticky-note-o t-lg-mr--md"></i>
							<b><?php echo $total;?></b> <?php echo JText::_('COM_ES_ARTICLES');?>
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class="app-article">
			<div class="app-contents<?php echo !$articles ? ' is-empty' : '';?>" data-article-lists>
				<?php if ($articles) { ?>
					<?php foreach ($articles as $article) { ?>
						<?php echo $this->output('site/articles/item', array('article' => $article, 'user' => $user, 'maxContentLength' => $maxContentLength)); ?>
					<?php } ?>
				<?php } ?>

				<?php echo $pagination->getListFooter('site');?>
				
				<?php echo $this->html('html.emptyBlock', JText::_('COM_EASYSOCIAL_NO_ARTICLES_CREATED_YET'), 'fa-database'); ?>
			</div>

		</div>
	</div>
</div>