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

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$helix3_path = JPATH_PLUGINS . '/system/officehelix3/core/officehelix3.php';

if (file_exists($helix3_path)) {
	require_once($helix3_path);
	$this->helix3 = officehelix3::getInstance();
} else {
	die('Please install and activate office helix3 plugin');
}

require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

$twofactormethods = UsersHelper::getTwoFactorMethods();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
	<?php
		if ($favicon = $this->helix3->getParam('favicon')) {
			$doc->addFavicon(JURI::base(true) . '/' . $favicon);
		} else {
			$doc->addFavicon($this->helix3->getTemplateUri() . '/images/favicon.ico');
		}
	?>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" />
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/presets/<?php echo $this->helix3->Preset(); ?>.css" rel="stylesheet" type="text/css" class="preset">
</head>
<body class="si-offline-page">
	<jdoc:include type="message" />
	<div id="es">
		<div class="si-login-wrapper">
			<div class="si-login" data-dashboard-guest-login="">
				<div class="si-login__hd">
					<?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) { ?>
						<div class="si-login__logo">
							<!-- <div class="si-login__logo-img" style="background-image: url('<?php echo $app->get('offline_image'); ?>');"></div> -->
						</div>
					<?php } ?>

					<h1>
						<?php echo htmlspecialchars($app->get('sitename')); ?>
					</h1>
					<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') { ?>
						<div class="si-login__desc">
							<?php echo $app->get('offline_message'); ?>
						</div>
					<?php } else if ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != '') { ?>
						<div class="si-login__desc">
							<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
						</div>
					<?php } ?>
				</div>
				<div class="si-login__bd">
					<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login" class="si-login__form">
						<div class="si-login__fields">
							<div class="form-group" id="form-login-username">
								<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
								<input name="username" id="username" type="text" class="form-control" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" />
							</div>
							
							<div class="form-group" id="form-login-password">
								<label for="password"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
								<input type="password" name="password" class="form-control" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
							</div>

							<?php if (count($twofactormethods) > 1) { ?>
								<div class="form-group" id="form-login-secretkey">
									<input type="text" name="secretkey" class="form-control" size="18" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" id="secretkey" />
								</div>
							<?php } ?>

							<div class="form-group" id="submit-buton">
								<input type="submit" name="Submit" class="btn btn-es-primary btn-block" value="<?php echo JText::_('JLOGIN'); ?>" />
							</div>
						</div>

						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.login" />
						<input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
				</div>
				<div class="si-login__ft">
				</div>
			</div>
		</div>
	</div>
</body>
</html>
