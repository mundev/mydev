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

<?php if ($activeGenre) { ?>
<div class="t-lg-mb--xl">
	<?php echo $this->html('miniheader.audioGenre', $activeGenre); ?>
</div>
<?php } ?>

<?php if ($featuredAudios && isset($featuredOutput) && $featuredOutput) { ?>
	<?php echo $featuredOutput; ?>
<?php } ?>

<?php if ($browseView) { ?>
<div class="es-list-sorting">
	<?php echo $this->html('form.popdown', 'sorting', $sort, array(
		$this->html('form.popdownOption', 'latest', 'COM_ES_SORT_BY_LATEST', '', false, $sortItems->latest->attributes, $sortItems->latest->url),
		$this->html('form.popdownOption', 'alphabetical', 'COM_ES_SORT_ALPHABETICALLY', '', false, $sortItems->alphabetical->attributes, $sortItems->alphabetical->url),
		$this->html('form.popdownOption', 'popular', 'COM_ES_SORT_BY_POPULARITY', '', false, $sortItems->popular->attributes, $sortItems->popular->url),
		$this->html('form.popdownOption', 'commented', 'COM_ES_SORT_BY_MOST_COMMENTED', '', false, $sortItems->commented->attributes, $sortItems->commented->url),
		$this->html('form.popdownOption', 'likes', 'COM_ES_SORT_BY_MOST_LIKES', '', false, $sortItems->likes->attributes, $sortItems->likes->url)
	)); ?>
</div>
<?php } ?>

<div class="es-list-result">
	<?php echo $this->html('html.loading'); ?>

	<div data-result-list>
		<?php if (isset($filterOutput) && $filterOutput) { ?>
			<?php echo $filterOutput; ?>
		<?php } else { ?>
			<?php echo $this->loadTemplate('site/audios/default/item.list', array('audios' => $audios, 'pagination' => $pagination, 'rawUid' => $rawUid, 'type' => $type, 'browseView' => $browseView, 'from' => $from, 'returnUrl' => $returnUrl, 'lists' => $lists)); ?>
		<?php } ?>

	</div>
</div>
