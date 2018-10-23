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
<div role="tabpanel" class="tab-pane" id="reactions" aria-labelledby="reactions-tab">
	<div class="dash-stream dash-stream-graph">
		<div  data-chart-reactions style="height: 200px; width: 100%;"></div>
		<div data-chart-reactions-legend></div>
	</div>

	<?php if ($reactions) { ?>
		<?php foreach ($reactions as $reaction) { ?>
			<div class="dash-stream">
				<div class="dash-stream-content">
					<div class="dash-stream-headline pull-left">
						<i class="eb-emoji-icon eb-emoji-icon--sm eb-emoji-icon--<?php echo $reaction->type;?>"></i>
						<?php echo JText::sprintf('COM_EASYBLOG_REACTIONS_USER_REACTED_ON_THE_POST', $reaction->user->getName(), $reaction->post->title); ?>
					</div>
					<div class="dash-stream-time pull-right">
						<span class="ml-10">
							<i class="fa fa-clock-o"></i>&nbsp; <?php echo $this->html('string.date', $reaction->created, JText::_('Y-m-d H:i'));?>
						</span>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="dash-stream empty">
			<?php echo JText::_('COM_EASYBLOG_NO_REACTIONS_CURRENTLY'); ?>
		</div>
	<?php } ?>
</div>