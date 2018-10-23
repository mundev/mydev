<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$msgList = $displayData['msgList'];
?>
<?php if (is_array($msgList) && !empty($msgList)) { ?>
<div id="system-message-container">
	<div id="system-message">
		<?php foreach ($msgList as $type => $msgs) { ?>
			<div id="es" class="alert alert-<?php echo $type; ?>">
				<a class="close" data-dismiss="alert">x</a>

				<?php if (!empty($msgs)) { ?>
					<h4 class="alert-heading"><?php echo ucfirst(JText::_($type)); ?></h4>
					<div>
						<?php foreach ($msgs as $msg) { ?>
							<div class="alert-message"><?php echo $msg; ?></div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>
<?php } ?>