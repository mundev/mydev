<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$primary = $this->config->get('buttons.primary', '#428bca');
$danger = $this->config->get('buttons.danger', '#d9534f');
$standard = $this->config->get('buttons.standard', '#333333');
$success = $this->config->get('buttons.success', '#39b54a');

// Get current theme
$theme = ES::themes();
$currentTheme = $theme->getCurrentTheme();
?>
<style type="text/css">
<?php if ($currentTheme != 'frosty') { ?>
#es .es-toolbar { background-color: <?php echo $this->config->get('general.layout.toolbarcolor', '#333333');?>;}
#es .es-toolbar,
#es .es-toolbar__item--search {border-color: <?php echo $this->config->get('general.layout.toolbarbordercolor', '#333333');?>; }
<?php } ?>

#es .es-toolbar .o-nav__item .es-toolbar__link { color: <?php echo $this->config->get('general.layout.toolbartextcolor', '#FFFFFF')?>; }
#es .es-toolbar .o-nav__item.is-active .es-toolbar__link,
#es .es-toolbar .o-nav__item .es-toolbar__link:hover, 
#es .es-toolbar .o-nav__item .es-toolbar__link:focus,
#es .es-toolbar .o-nav__item .es-toolbar__link:active { background-color: <?php echo $this->config->get('general.layout.toolbaractivecolor', '#5c5c5c')?>; }

#es .es-story-privacy .es-privacy .es-privacy-toggle, 
#es .es-story-privacy .es-privacy .es-privacy-toggle:hover, 
#es .es-story-privacy .es-privacy .es-privacy-toggle:focus, 
#es .es-story-privacy .es-privacy .es-privacy-toggle:active,
#es .btn-es-danger:hover,
#es .btn-es-danger:active,
#es .btn-es-danger,
#es .btn-es-default:hover,
#es .btn-es-default:active,
#es .btn-es-default,
#es .btn-es-success:hover,
#es .btn-es-success:active,
#es .btn-es-success,
#es .btn-es-primary:hover,
#es .btn-es-primary:active,
#es .btn-es-primary {
	background-color: #fff !important;
}

<?php if ($primary != '#428bca') { ?>
#es .es-story-privacy .es-privacy .es-privacy-toggle, 
#es .es-story-privacy .es-privacy .es-privacy-toggle:hover, 
#es .es-story-privacy .es-privacy .es-privacy-toggle:focus, 
#es .es-story-privacy .es-privacy .es-privacy-toggle:active,
#es .btn-es-primary,
#es .btn-es-primary:hover, 
#es .btn-es-primary:active,
#es .btn-es-primary:focus {
	background-image: linear-gradient(to top, rgba(<?php echo ES::string()->hexToRGB($primary);?>, 0.25) 20%, rgba(<?php echo ES::string()->hexToRGB($primary);?>, 0.08) 100%) !important;
	color: <?php echo $primary;?> !important;
	border-color: rgba(<?php echo ES::string()->hexToRGB($primary);?>, 0.5) !important;
}

#es .es-story-privacy .es-privacy .es-privacy-toggle:hover,
#es .btn-es-primary:hover {
	border-color: rgba(<?php echo ES::string()->hexToRGB($primary);?>, 0.6) !important;
}

#es .es-story-privacy .es-privacy .es-privacy-toggle:hover, #es .es-story-privacy .es-privacy .es-privacy-toggle:focus, #es .es-story-privacy .es-privacy .es-privacy-toggle:active,
#es .btn-es-primary-o:hover, #es .btn-es-primary-o:active, #es .btn-es-primary-o {
	color: <?php echo $primary;?> !important;
}
<?php } ?>

<?php if ($danger != '#d9534f') { ?>
/** Danger **/
#es .btn-es-danger,
#es .btn-es-danger:hover, 
#es .btn-es-danger:active, 
#es .btn-es-danger:focus {
	background-image: linear-gradient(to top, rgba(<?php echo ES::string()->hexToRGB($danger);?>, 0.25) 20%, rgba(<?php echo ES::string()->hexToRGB($danger);?>, 0.08) 100%) !important;
	color: <?php echo $danger;?> !important;
	border-color: rgba(<?php echo ES::string()->hexToRGB($danger);?>, 0.5) !important;
}

#es .btn-es-danger:hover {
	border-color: rgba(<?php echo ES::string()->hexToRGB($danger);?>, 0.6) !important;
}

#es .btn-es-danger-o:hover, #es .btn-es-danger-o:active, #es .btn-es-danger-o {
	color: <?php echo $danger;?> !important;
}
<?php } ?>

<?php if ($standard != '#333333') { ?>
/** Default **/
#es .btn-es-default,
#es .btn-es-default:hover, 
#es .btn-es-default:active, 
#es .btn-es-default:focus {
	background-image: linear-gradient(to top, rgba(<?php echo ES::string()->hexToRGB($standard);?>, 0.25) 20%, rgba(<?php echo ES::string()->hexToRGB($standard);?>, 0.08) 100%) !important;
	color: <?php echo $standard;?> !important;
	border-color: rgba(<?php echo ES::string()->hexToRGB($standard);?>, 0.8) !important;
}

#es .btn-es-default:hover {
	border-color: rgba(<?php echo ES::string()->hexToRGB($standard);?>, 1) !important;
}
#es .btn-es-default-o,
#es .btn-es-default-o:hover, 
#es .btn-es-default-o:active, 
#es .btn-es-default-o:focus, {
	color: <?php echo $standard;?> !important;
}
<?php } ?>

<?php if ($success != '#39b54a') { ?>
/** Success **/
#es .btn-es-success,
#es .btn-es-success:hover,
#es .btn-es-success:active,
#es .btn-es-success:focus {
	background-image: linear-gradient(to top, rgba(<?php echo ES::string()->hexToRGB($success);?>, 0.25) 20%, rgba(<?php echo ES::string()->hexToRGB($success);?>, 0.08) 100%) !important;
	color: <?php echo $success;?> !important;
	border-color: rgba(<?php echo ES::string()->hexToRGB($success);?>, 0.5) !important;
}

#es .btn-es-success:hover {
	border-color: rgba(<?php echo ES::string()->hexToRGB($success);?>, 0.8) !important;
}

#es .btn-es-success-o,
#es .btn-es-success-o:hover,
#es .btn-es-success-o:active,
#es .btn-es-success-o:focus {
	color: <?php echo $success;?> !important;
}
<?php } ?>
</style>