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

class EasyBlogThemesHelperPanel extends EasyBlogThemesHelperAbstract
{
	/**
	 * Renders the heading of a panel
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function heading($text, $description = '')
	{
		if (!$description) {
			$description = $text . '_DESC';
		}

		$text = JText::_($text);
		$description = JText::_($description);

		$theme = EB::themes();
		$theme->set('text', $text);
		$theme->set('description', $description);

		$output = $theme->output('admin/html/panel/heading');

		return $output;
	}

	/**
	 * Generates a settings row in the panel body
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function label($text, $help = true, $helpText = '', $columns = 5)
	{
		if ($help && !$helpText) {
			$helpText = JText::_($text . '_HELP');

			// Fall back to _DESC for backward compatibility.
			if ($helpText == $text . '_HELP') {
				$helpText = JText::_($text . '_DESC');

				// Fall back to _HELP if the string still not translated.
				if ($helpText == $text . '_DESC') {
					$helpText = JText::_($text . '_HELP');
				}
			}
		}

		$text = JText::_($text);

		$theme = EB::themes();
		$theme->set('columns', $columns);
		$theme->set('text', $text);
		$theme->set('help', $help);
		$theme->set('helpText', $helpText);

		$output = $theme->output('admin/html/panel/label');

		return $output;
	}

	/**
	 * Generates an info section within panels
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function info($text, $link = '', $buttonText = '', $buttonSize = 'btn-sm', $image = '')
	{
		$text = JText::_($text);
		$buttonText = JText::_($buttonText);
		
		$theme = EB::themes();
		$theme->set('image', $image);
		$theme->set('buttonText', $buttonText);
		$theme->set('text', $text);
		$theme->set('link', $link);

		$output = $theme->output('admin/html/panel/info');

		return $output;		
	}
}
