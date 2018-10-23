<?php
/**
* @package      Office Template
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
$helix3_path = JPATH_PLUGINS . '/system/officehelix3/core/officehelix3.php';

if (file_exists($helix3_path)) {
	require_once($helix3_path);
	$this->helix3 = officehelix3::getInstance();
} else {
	die('Please install and activate office helix3 plugin');
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>

	<?php
	$doc = JFactory::getDocument();

	if ($favicon = $this->params->get('favicon')) {
		$doc->addFavicon( JURI::base(true) . '/' .  $favicon);
	} else {
		$doc->addFavicon( $this->baseurl . '/templates/'. $this->template .'/images/favicon.ico' );
	}
	?>

	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/media/jui/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/frontend-edit.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
</head>
<body class="contentpane">
	<jdoc:include type="message" />
	<jdoc:include type="component" />
</body>
</html>
