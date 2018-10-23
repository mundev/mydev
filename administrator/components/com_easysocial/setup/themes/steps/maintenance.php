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
<form name="installation" data-installation-form>

	<p>We will need to update existing site Users and ensure that the previous Users are synchronized properly with EasySocial.</p>

	<div data-sync-progress>
		<ol class="install-logs list-reset" data-progress-logs>
			<li class="pending" data-progress-syncuser>
				<b class="split__title">Synchronizing users</b>
				<span class="progress-state text-info">Executing</span>
				<div class="notes">
					<ul data-progress-syncuser-items></ul>
				</div>
			</li>
			<li class="pending" data-progress-syncprofiles>
				<b class="split__title">Synchronizing users that don't have a profile.</b>
				<span class="progress-state text-info">Executing</span>
				<div class="notes">
					<ul data-progress-syncprofile-items></ul>
				</div>
			</li>
			<li class="pending" data-progress-execscript>
				<b class="split__title">Executing maintenance scripts</b>
				<span class="progress-state text-info">Executing</span>
				<div class="notes">
					<ul data-progress-execscript-items></ul>
				</div>
			</li>
		</ol>
	</div>

	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
</form>

<script type="text/javascript">
$(document).ready(function(){
	es.maintenance.init();
});
</script>