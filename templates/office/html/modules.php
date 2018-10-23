<?php
/**
* @package Helix Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

function modChrome_sp_xhtml($module, $params, $attribs) {

	$moduleTag     = htmlspecialchars($params->get('module_tag', 'div'), ENT_QUOTES, 'UTF-8');
	$bootstrapSize = (int) $params->get('bootstrap_size', 0);
	$moduleClass   = $bootstrapSize != 0 ? ' col-sm-' . $bootstrapSize : '';
	$headerTag     = htmlspecialchars($params->get('header_tag', 'h3'), ENT_QUOTES, 'UTF-8');
	$headerClass   = htmlspecialchars($params->get('header_class', 'sp-module-title'), ENT_QUOTES, 'UTF-8');

	$html = '';
	$sidebar = false;

	// We purposely exclude sidebar module position here.
	if (isset($attribs['name']) && $attribs['name'] == 'sidebar') {
		$sidebar = true;
		$html = $module->content;
	}

	if ($module->content && !$sidebar) {
		$html = '<' . $moduleTag . ' class="sp-module ' . htmlspecialchars($params->get('moduleclass_sfx'), ENT_QUOTES, 'UTF-8') . $moduleClass . '">';

			if ($module->showtitle) {
				$html .= '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
			}

			$html .= '<div class="sp-module-content">';
			$html .= $module->content;
			$html .= '</div>';

		$html .= '</' . $moduleTag . '>';
	}

	echo $html;
}