<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('site:/views/views');

class EasySocialViewActivities extends EasySocialSiteView
{
	/**
	 * Returns an ajax chain.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string	The verb that we have performed.
	 */
	public function toggle($id, $curState)
	{
		// Load ajax lib
		$ajax = ES::ajax();

		// Determine if there's any errors on the form.
		$error = $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

		// Set the label
		$label = $curState ? JText::_('COM_EASYSOCIAL_ACTIVITY_HIDE') : JText::_('COM_EASYSOCIAL_ACTIVITY_SHOW');
		$isHidden = $curState ? 0 : 1;

		return $ajax->resolve($label, $isHidden);
	}

	public function delete()
	{
		// Load ajax lib
		$ajax = ES::ajax();

		// Determine if there's any errors on the form.
		$error = $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

		$html = JText::_('COM_EASYSOCIAL_ACTIVITY_ITEM_DELETED');

		return $ajax->resolve($html);
	}

	public function getActivities($filterType, $data, $nextlimit, $isloadmore = false)
	{
		// Load ajax lib
		$ajax = ES::ajax();

		// Determine if there's any errors on the form.
		$error = $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

		$theme = ES::get('Themes');
		$theme->set('activities', $data);

		$theme->set('nextlimit', $nextlimit);

        $title = '';
        switch ($filterType) {
            case 'hiddenapp':
                $title = JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_APPS');
                break;

            case 'hidden':
                $title = JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTIVITIES');
                break;

            case 'hiddenactor':
                $title = JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTORS');
                break;

            case 'all':
                $title = JText::_('COM_EASYSOCIAL_ACTIVITY_ALL_ACTIVITIES');
                break;

            default:
                $title = JText::sprintf('COM_EASYSOCIAL_ACTIVITY_ITEM_TITLE', ucfirst($filterType));
                break;
        }

        $theme->set('title', $title);

		$output = '';
		if ($isloadmore) {
			if ($data) {

                $filename = "site/activities/items/";

                if ($filterType == 'hiddenapp') {
                    $filename .= 'hiddenapp';
                } else if ($filterType == 'hiddenactor') {
                    $filename .= 'hiddenactor';
                } else {
                    $filename .= 'default';
                }

                $options = array('items' => $data, 'nextlimit' => $nextlimit, 'active' => $filterType);
				$output = $theme->loadTemplate($filename, $options);
			}

			return $ajax->resolve($output, $nextlimit);
		} else {

            $theme->set('active', $filterType);
            $theme->set('filterType', $filterType);
			$output = $theme->output('site/activities/default/content');

            $count = $data ? count($data) : 0;

			return $ajax->resolve($output, $count);
		}
	}

	/**
	 * Confirmation for deleting an activity item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function confirmDelete()
	{
		$ajax = ES::ajax();

		$theme = ES::themes();
		$contents = $theme->output('site/activities/dialog.delete');

		return $ajax->resolve($contents);
	}

	/**
	 * Retrieves a list of hidden apps from the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 * @return
	 */
	public function getHiddenApps($data)
	{
		// Load ajax lib
		$ajax	= ES::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

		$theme = ES::get('Themes');
        $theme->set('title', JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_APPS'));
		$theme->set('activities', $data);
        $theme->set('filtertype', 'hiddenapp');

		$output = $theme->output('site/activities/default/content');
		return $ajax->resolve($output, count($data));

	}

	/**
	 * Retrieves a list of hidden apps from the stream
	 *
	 * @since	1.0
	 * @access	public
	 * @param	Array
	 * @return
	 */
	public function getHiddenActors($data)
	{
		// Load ajax lib
		$ajax	= ES::ajax();

		// Determine if there's any errors on the form.
		$error 	= $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

		$theme = ES::get('Themes');
        $theme->set('title', JText::_('COM_EASYSOCIAL_ACTIVITY_HIDDEN_ACTORS'));
        $theme->set('activities', $data);
        $theme->set('filtertype', 'hiddenactor');

		$output = $theme->output('site/activities/default/content');

		return $ajax->resolve($output, count($data));

	}

	public function unhideapp()
	{
		// Load ajax library.
		$ajax = ES::ajax();
		$error = $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

        $message = JText::_('COM_EASYSOCIAL_ACTIVITY_APPS_UNHIDE_SUCCESSFULLY');
		return $ajax->resolve($message);
	}

	public function unhideactor()
	{
		// Load ajax library.
		$ajax = ES::ajax();

		$error = $this->getError();

		if ($error) {
			return $ajax->reject($error);
		}

        $message = JText::_('COM_EASYSOCIAL_ACTIVITY_USERS_UNHIDE_SUCCESSFULLY');

		return $ajax->resolve($message);
	}

}
