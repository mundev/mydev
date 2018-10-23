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

require_once(dirname(__FILE__) . '/table.php');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class EasyBlogTableBlogger extends EasyBlogTable
{
	var $id = null;
	var $title = null;
	var $biography = null;
	var $nickname = null;
	var $avatar = null;
	var $description = null;
	var $url = null;
	var $params = null;
	var $published = null;
	var $ordering = null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__easyblog_users', 'id', $db);
	}

	public function bindPost()
	{
		$data = array(
						'nickname' => $this->input->get('nickname', '', 'word'),
					  	'description' => $this->input->get('description'),
						'url' => $this->input->get('url'),
						'biography'	=> $this->input->get('biography'),
						'title' => $this->input->get('title')
						);
		pr($data);exit;

		parent::bind($data);

		$avatar	= $this->input->get('avatar', '', 'Files');
	}

	public function getAvatar()
	{
	    $avatar_link    = '';

        if ($this->avatar == 'default.png' || $this->avatar == 'default_blogger.png' || $this->avatar == 'components/com_easyblog/assets/images/default_blogger.png' || $this->avatar == 'components/com_easyblog/assets/images/default.png' || empty($this->avatar)) {
            $avatar_link   = 'components/com_easyblog/assets/images/default_blogger.png';
        } else {
    		$avatar_link   = EasyImageHelper::getAvatarRelativePath() . '/' . $this->avatar;
    	}

		return $avatar_link;
	}

	/**
	 * Retrieve a list of tags that is associated with this tag
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getDefaultParams()
	{
		static $cache = null;

		if (is_null($cache)) {
			$manifest = JPATH_ROOT . '/components/com_easyblog/views/blogger/tmpl/listings.xml';
			$fieldsets = EB::form()->getManifest($manifest);

			$obj = new stdClass();

			foreach($fieldsets as $fieldset) {
				foreach($fieldset->fields as $field) {
					$obj->{$field->attributes->name} = $field->attributes->default;
				}
			}

			$cache = new JRegistry($obj);
		}

		return $cache;
	}

}
