<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style type="text/css">
#eb .eb-header .eb-toolbar { background-color: <?php echo $this->config->get('layout_toolbarcolor', '#333333');?>;}
#eb .eb-header .eb-toolbar,
#eb .eb-toolbar__item--search {border-color: <?php echo $this->config->get('layout_toolbarbordercolor', '#333333');?>; }
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link { color: <?php echo $this->config->get('layout_toolbartextcolor', '#FFFFFF')?>; }
#eb .eb-header .eb-toolbar .o-nav__item.is-active .eb-toolbar__link,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:hover, 
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:focus,
#eb .eb-header .eb-toolbar .o-nav__item .eb-toolbar__link:active { background-color: <?php echo $this->config->get('layout_toolbaractivecolor', '#5c5c5c')?>; }
#eb .eb-toolbar__link.has-composer {background-color: <?php echo $this->config->get('layout_toolbarcomposerbackgroundcolor', '#428bca')?>; }
#eb .eb-reading-progress {background: <?php echo $this->config->get('main_reading_background');?> !important;color: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-bar {background: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress__container {background-color: <?php echo $this->config->get('main_reading_background');?> !important;}
#eb .eb-reading-progress::-moz-progress-bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress::-webkit-progress-value {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
#eb .eb-reading-progress__bar {background: <?php echo $this->config->get('main_reading_foreground');?> !important;}
</style>