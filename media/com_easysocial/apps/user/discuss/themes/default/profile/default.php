<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->html('responsive.toggle'); ?>

<div class="es-container" data-ed-discussions data-id="<?php echo $user->id;?>">
	<div class="es-sidebar">
		<?php if ($canCreateDiscussion) { ?>
		<a href="<?php echo EDR::_('view=ask');?>" class="btn btn-es-primary btn-block t-lg-mb--xl"><?php echo JText::_('APP_EASYDISCUSS_CREATE_DISCUSSION'); ?></a>
		<?php } ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked feed-items">
					<li class="o-tabs__item <?php echo $filter == 'userposts' ? 'active' : '';?>" data-discuss-filter="userposts">
						<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_ALL');?></a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<li class="o-tabs__item <?php echo $filter == 'unanswered' ? 'active' : '';?>" data-discuss-filter="unanswered">
						<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_UNANSWERED');?></a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<li class="o-tabs__item <?php echo $filter == 'resolved' ? 'active' : '';?>" data-discuss-filter="resolved">
						<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_RESOLVED');?></a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<li class="o-tabs__item <?php echo $filter == 'unresolved' ? 'active' : '';?>" data-discuss-filter="unresolved">
						<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_UNRESOLVED');?></a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<li class="o-tabs__item <?php echo $filter == 'userreplies' ? 'active' : '';?>" data-discuss-filter="userreplies">
						<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_REPLIES');?></a>
						<div class="o-loader o-loader--sm"></div>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content">
		<div class=" app-contents<?php echo !$posts ? ' is-empty' : '';?>" data-discuss-wrapper>
			<?php echo ES::themes()->html('html.loading'); ?>
			<?php echo ES::themes()->html('html.emptyBlock', 'APP_GROUP_DISCUSSIONS_EMPTY', 'fa-database'); ?>

			<div data-discuss-contents>
				<?php if ($posts) { ?>
					<?php echo $this->loadTemplate('themes:/apps/user/discuss/profile/items', array('posts' => $posts, 'pagination' => $pagination)); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>