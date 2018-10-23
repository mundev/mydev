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

$allowedRss = array('me','everyone','list','bookmarks','following','custom');

$streamType = $this->input->get('type');

$showRSS = in_array($streamType, $allowedRss) || !$streamType;
?>
<?php if ($cluster) { ?>
	<?php echo $this->html('html.miniheader', $cluster); ?>
<?php } ?>

<?php if ($hashtag) { ?>
<div class="es-snackbar es-hashtag">
	<div class="es-snackbar__cell">
		<h1 class="es-snackbar__title"><?php echo JText::sprintf('COM_EASYSOCIAL_STREAM_HASHTAG_CURRENTLY_FILTERING' , '<a href="' . ESR::dashboard(array('layout' => 'hashtag' , 'tag' => $hashtagAlias)) . '">#' . $hashtag . '</a>'); ?></h1>
	</div>
	<div class="es-snackbar__cell">
		<a href="javascript:void(0)"
		   class="pull-right subscribe-rss btn-rss"
		   data-hashtag-filter-save
		   data-tag="<?php echo $hashtag; ?>">
			<i class="fa fa-floppy-o"></i>&nbsp; <?php echo JText::_('COM_ES_SAVE');?>
		</a>
	</div>
</div>
<?php } else { ?>

	<?php if ($streamFilter) { ?>
		<div class="es-snackbar es-hashtag">
			<div class="es-snackbar__cell">
				<h1 class="es-snackbar__title"><?php echo JText::_($streamFilter->title);?></h1>
			</div>
			<div class="es-snackbar__cell">
				<a 
				data-edit-filter
				data-id="<?php echo $streamFilter->id; ?>"
				data-type="<?php echo $streamFilter->utype; ?>"
				class="pull-right subscribe-rss btn-rss"
				href="<?php echo ESR::dashboard(array('type' => 'filterForm', 'filterid' => $streamFilter->getAlias()));?>">
					<i class="fa fa-pencil"></i>&nbsp; <?php echo JText::_('COM_ES_EDIT');?>
				</a>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php echo $stream->html();?>
