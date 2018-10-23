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
<div id="eb" class="eb-mod mod-easyblogpostmap<?php echo $modules->getWrapperClass(); ?>" data-eb-module-postmap>
	<div class="locationMap" style="width:<?php echo $params->get('fluid', true) ? '100%' : $mapWidth.'px'; ?>; height: <?php echo $mapHeight; ?>px;"></div>
</div>

<script type="text/javascript">
EasyBlog.require()
.script('site/location', 'site/vendors/ratings')
.done(function($) {

	$("[data-eb-module-postmap]").implement("EasyBlog.Controller.Location.Map", {
		language: "<?php echo $language; ?>",
		gMapsKey: "<?php echo $gMapsKey; ?>",
		zoom: <?php echo $zoom; ?>,
		fitBounds: <?php echo $fitBounds; ?>,
		useStaticMap: false,
		disableMapsUI: <?php echo $mapUi; ?>,
		locations: <?php echo json_encode($locations); ?>,
		enableClusterer: <?php echo $enableMarkerClusterer; ?>
	});

});
</script>