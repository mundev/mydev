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

ES::import('admin:/tables/table');

class NotesTableNote extends SocialTable
{
	public $id = null;
	public $user_id	= null;
	public $title = null;
	public $alias = null;
	public $content	= null;
	public $created	= null;
	public $params = null;

	public function __construct(& $db)
	{
		parent::__construct('#__social_notes', 'id', $db);
	}

	public function store($updateNulls = false)
	{
		// @TODO: Automatically set the alias
		if (!$this->alias) {

		}

		$state = parent::store();

		return $state;
	}

	public function getAppId()
	{
		return $this->getApp()->id;
	}

	public function getApp()
	{
		static $app;

		if (empty($app)) {
			$app = FD::table('app');
			$app->load(array('type' => SOCIAL_TYPE_APPS, 'group' => SOCIAL_APPS_GROUP_USER, 'element' => 'notes'));
		}

		return $app;
	}

	/**
	 * Formats the content of a note
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function getContent()
	{
		// Apply e-mail replacements
		$content = ES::string()->replaceEmails($this->content);

		// Apply hyperlinks
		$content = ES::string()->replaceHyperlinks($content);

		// Apply bbcode
		$content = ES::string()->parseBBCode($content, array('code' => true, 'escape' => false));

		return $content;
	}

	/**
	 * Creates a new stream record
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function createStream($verb)
	{
		// Add activity logging when a friend connection has been made.
		// Activity logging.
		$stream	= ES::stream();
		$streamTemplate	= $stream->getTemplate();

		// Set the actor.
		$streamTemplate->setActor($this->user_id, SOCIAL_TYPE_USER);

		// Set the context.
		$streamTemplate->setContext($this->id, 'notes');

		// Set the verb.
		$streamTemplate->setVerb($verb);

		$streamTemplate->setAccess('core.view');

		// Create the stream data.
		$stream->add($streamTemplate);
	}

	/**
	 * Overrides parent's delete behavior
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function delete($pk = null)
	{
		$state = parent::delete($pk);

		// Delete streams that are related to this note.
		$stream = ES::stream();
		$stream->delete($this->id, 'notes');

		return $state;
	}

	/**
	 * Shorthand to get the permalink of this note.
	 *
	 * @since  1.2
	 * @access public
	 */
	public function getPermalink($external = false, $xhtml = true, $sef = true)
	{
		return $this->getApp()->getCanvasUrl(array('cid' => $this->id, 'uid' => ES::user($this->user_id)->getAlias(), 'type' => SOCIAL_TYPE_USER, 'external' => $external, 'sef' => $sef), $xhtml);
	}
}
