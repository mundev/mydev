<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

require_once(__DIR__ . '/helper.php');

class SocialUserAppDiscuss extends SocialAppItem
{
	/**
	 * Determines if EasyDiscuss exists on the site.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {

			$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

			$exists = JFile::exists($file);

			if ($exists) {
				require_once($file);

				ED::loadLanguages();
			}
		}

		return $exists;
	}

	/**
	 * Function being triggered from EasySocial when processing GDPR data.
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function onExportGdpr(SocialGdprSection &$section, &$esParams)
	{
		$esUser = $section->user;
		$esLib = ES::gdpr();

		$edSection = $esLib->createSection($section->user, 'discuss', JText::_('APP_USER_EASYDISCUSS_GDPR_SECTION'), false);

		$items = $this->getAvailableAdapters();

		$params = new JRegistry();

		$states = array();

		// require ed required gdpr classes
		require_once(DISCUSS_ADMIN_INCLUDES . '/gdpr/dependencies.php');
		require_once(DISCUSS_ADMIN_INCLUDES . '/gdpr/types/abstract.php');

		// get generic es adapter
		if (!class_exists('SocialGdprItem')) {
			$file = __DIR__ . '/gdpr/item.php';
			require_once($file);
		}

		foreach ($items as $type) {

			$file = DISCUSS_ADMIN_INCLUDES . '/gdpr/types/' . $type . '.php';
			require_once($file);

			$className = 'EasyDiscussGdpr' . ucfirst($type);

			$edUser = ED::user($esUser->id);
			$adapter = new $className($edUser, $params);

			$esAdapter = new SocialGdprItem($section->user, $esParams);
			$adapter->onEasySocialGdprExport($edSection, $esAdapter, $esParams);

			// Determine if the process is completed
			$states[] = $esAdapter->getParams('complete');
		}

		if (in_array(false, $states)) {
			return 0;
		}

		return 1;
	}

	/**
	 * Retrieves a list of built-in gdpr adapters available
	 *
	 * @since	4.1
	 * @access	public
	 */
	public function getAvailableAdapters()
	{
		static $adapters = null;

		if (is_null($adapters)) {
			$files = JFolder::files(DISCUSS_ADMIN_INCLUDES . '/gdpr/types', '.php$', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'abstract.php'));

			foreach ($files as $file) {
				$adapters[] = str_ireplace('.php', '', $file);
			}
		}

		return $adapters;
	}

	/**
	 * When cron executes, purge temporary attachments
	 *
	 * @since	2.0.5
	 * @access	public
	 */
	public function onCronExecute()
	{
		$db = ES::db();

		// Delete older attachments
		$query = 'SELECT * FROM `#__discuss_attachments_tmp` WHERE DATEDIFF(UTC_TIMESTAMP(), `created`) > 7;';

		$db->setQuery($query);
		$expired = $db->loadObjectList();

		if (!$expired) {
			return;
		}

		foreach ($expired as $item) {
			$table = ED::table('AttachmentsTmp');
			$table->bind($item);

			if (JFile::exists($table->path)) {
				JFile::delete($table->path);
			}

			$table->delete();
		}
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		$allowed = array('discuss', 'easydiscuss');

		if (!in_array($item->context, $allowed) || !$this->exists()) {
			return;
		}

		// If this is from a cluster, we need to ensure the viewer can really view the item
		if ($item->isCluster()) {
			$cluster = $item->getCluster();

			if (!$cluster->canViewItem()) {
				return;
			}
		}

		// Attach our own stylesheet
		$this->getApp()->loadCss();

		// Determine the current action
		$verb = $item->verb;
		$context = ucfirst($item->context);
		$method = $verb . $context . 'Stream';

		if (!method_exists($this, $method)) {
			return;
		}


		// Determine if the app is configured to display such stream items
		$params = $this->getParams();
		$allowed = $params->get('stream_' . $verb, true);

		if (!$allowed) {
			return;
		}

		// We only allow comment on the create stream item
		if ($verb != 'create') {
			$item->comments = false;
		}

		// Define standard stream looks
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;

		// This is to fix stream items generated prior to 2.0
		$item->content = '';

		$this->$method($item);
	}

	/**
	 * Prepares what should appear on user's story form.
	 *
	 * @since  1.3
	 * @access public
	 */
	public function onPrepareStoryPanel($story)
	{
		if (!$this->exists()) {
			return;
		}

		$acl = ED::acl();
		$my	= FD::user();
		$config	= ED::config();

		// We only allow event creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
		// Empty target is also allowed because it means no target.
		if (!empty($story->target) && $story->target != $my->id) {
			return;
		}

		// Ensure that user has access to this apps.
		if (!$this->getApp()->hasAccess($this->my->profile_id)) {
			return;
		}

		// If guest posting is disallowed in the settings, they shouldn't be able to create a discussion at all.
		if (!$acl->allowed('add_question', '0')) {
			return;
		}

		// For now we only allow for non-moderate post.
		if ($config->get('main_moderatepost') && !ED::isSiteAdmin($my->id) && !ED::isModerateThreshold($my->id)) {
			return;
		}

		$params = $this->getParams();

		// Create plugin object
		$plugin = $story->createPlugin('discuss', 'panel');

		$title = JText::_('APP_USER_EASYDISCUSS_STORY');
		$plugin->title = $title;

		// Get the theme class
		$theme = FD::themes();

		// Only display category that current logged in user has permission to select.
		$categoryModel = ED::model('Category');

		$defaultCategory = $categoryModel->getDefaultCategory();

		$categoryId = $defaultCategory->id;

		$nestedCategories = $this->populateCategories('category_id', $categoryId, true);

		$theme->set('nestedCategories', $nestedCategories);
		$theme->set('params', $params);
		$theme->set('title', $plugin->title);

		$plugin->button->html = $theme->output('apps/user/discuss/story/panel.button');
		$plugin->content->html = $theme->output('apps/user/discuss/story/panel.form');

		$appId = $this->getApp()->id;

		$script = ES::get('Script');
		$script->set('errorTitle', JText::_('APP_USER_EASYDISCUSS_INVALID_TITLE'));
		$script->set('errorContent', JText::_('APP_USER_EASYDISCUSS_INVALID_CONTENT'));
		$script->set('appId', $appId);
		$script->set('edconfig', $config);
		$script->set('params', $params);

		$plugin->script = $script->output('apps:/user/discuss/story');

		return $plugin;
	}

	public static function populateCategories($eleName, $default = false, $showPrivateCat = true)
	{
		$catModel = ED::model('Categories');

		$parentCat = $catModel->getParentCategories('', 'all', true, $showPrivateCat);

		if (!empty($parentCat)) {
			for ($i = 0; $i < count($parentCat); $i++) {
				$parent =& $parentCat[$i];

				$parent->childs = null;

				ED::buildNestedCategories($parent->id, $parent, false, true, $showPrivateCat, true);
			}
		}

		$form = '';

		if ($parentCat) {
			foreach ($parentCat as $category) {
				$selected = ($category->id == $default) ? ' selected="selected"' : '';

				$disabled = $category->container  ? ' disabled="disabled"' : '';

				$form .= '<option value="' . $category->id . '" ' . $selected . $disabled . '>' . JText::_($category->title) . '</option>';

				ED::accessNestedCategories($category, $form, '0', $default, 'select', '', true);
			}
		}

		$html = '';
		$html .= '<select data-story-discuss-category class="o-form-control">';
		$html .= $form;
		$html .= '</select>';

		return $html;
	}

	/**
	 * Stores the discussion
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function onBeforeStorySave(&$template, &$stream, &$content)
	{
		if (!$this->exists() || $template->context_type != 'discuss') {
			return;
		}

		// No need to check for category permission since we have already hide the category from the story panel
		$data = array(
						'title' => $_POST['discuss_title'],
						'content' => $_POST['discuss_content'],
						'user_id' => ES::user()->id,
						'category_id' => $_POST['discuss_categoryId'],
						'post_type' => 'default',
						'created' => ES::date()->toSql(),
						'modifed' => ES::date()->toSql(),
						'user_type' => 'member',
						'published' => 1,
						'password' => '',
						'ip' => JRequest::getVar('REMOTE_ADDR', '', 'SERVER')
					);

		// Check for attachments here

		$saveOptions = array('saveFromEasysocialStory' => true);

		// Save the discussion here
		$post = ED::post();
		$post->bind($data);
		$post->save($saveOptions);

		$template->context_type = 'discuss';
		$template->context_id = $post->id;

		// Process files
		$files = $this->input->get('discuss_files', array(), 'int');

		if ($files) {

			// Process temporary files here
			foreach ($files as $file) {
				$tmpAttachment = ED::table('AttachmentsTmp');
				$exists = $tmpAttachment->load((int) $file);

				if (!$exists) {
					continue;
				}

				// Generate a new unique name for the file
				$uniqueName = md5(JFactory::getDate()->toSql() . uniqid() . $tmpAttachment->title);

				// If the file exists, we now need to map the file with the post
				$file = array();
				$file['name'] = $tmpAttachment->title;
				$file['type'] = $tmpAttachment->mime;
				$file['tmp_name'] = $tmpAttachment->path;
				$file['error'] = '';
				$file['size'] = filesize($tmpAttachment->path);

				// Upload an attachment
				$attachment = ED::attachment();
				$attachment->upload($post, $file);

				// Delete the temporary attachment after copying
				if (JFile::exists($tmpAttachment->path)) {
					JFile::delete($tmpAttachment->path);
				}

				$tmpAttachment->delete();


			}
		}

		return;
	}

	/**
	 * Generates the activity stream for new discussion
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function createDiscussStream(SocialStreamItem &$item)
	{
		$post = ED::post($item->contextId);

		if (!$post) {
			return;
		}

		// Determine if this post is accessible by the current user
		if (!$post->canView()) {
			return;
		}

		if ($this->my->isSiteAdmin() || ($this->my->id == $item->actor->id && $post->canEdit())) {
			$item->edit_link = EDR::getEditRoute($post->id);
		}

		// Format the content
		$content = $this->processContent($post);

		$this->set('content', $content);
		$this->set('post', $post);
		$this->set('actor', $item->actor);

		$item->label = JText::_('APP_USER_DISCUSSIONS_STREAM_TOOLTIP');
		$item->title = parent::display('streams/create.title');
		$item->preview = parent::display('streams/post.preview');

		$item->addOgDescription($post->content);
	}

	/**
	 * Generates the activity stream for new discussion
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function createEasydiscussStream(SocialStreamItem &$item)
	{
		$actor = $item->actor;

		$id = $item->contextId;

		$post = ED::post($id);

		if (!$post) {
			return;
		}

		// Get the category
		$category = ED::category($post->category_id);

		if (!$category->canAccess()) {
			return;
		}

		$permalink = SocialDiscussHelper::getPermalink( $post->id );
		$catPermalink = $this->getCategoryPermalink( $category->id );

		// Remove code blocks from the content
		$post->content = ED::parser()->removeCodes($post->content);
		$post->content = ED::videos()->strip($post->content);

		$post->content = $this->processContent($post);

		$cluster = $item->getCluster();

		$group = $item->cluster_type;

		$this->set('catPermalink', $catPermalink);
		$this->set('permalink', $permalink);
		$this->set('post', $post);
		$this->set('category', $category);
		$this->set('actor', $actor);
		$this->set('group', $group);
		$this->set('cluster', $cluster);

		$item->label = JText::_('APP_USER_EASYDISCUSS_GROUP_STREAM_TOOLTIP');
		$item->title = parent::display('streams/group/create.title');
		$item->preview = parent::display('streams/group/create.content');

		$item->addOgDescription($post->content);
	}

	/**
	 * Generates the activity stream when a reply is marked as answer.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function acceptedDiscussStream(SocialStreamItem &$item)
	{
		$actor = $item->actor;

		$id = (int) $item->contextId;

		$post = ED::post($id);

		if ($post->parent_id) {
			$question = ED::post($post->parent_id);

		} else {
			$question = $post;
		}

		// if this post is a reply and user has no permission to view, do not show this stream.
		if ($post->isReply() && !$post->canView()) {
			return;
		}

		// If the user can't view the question, there is no point displaying the stream item
		if (!$question->canView()) {
			return;
		}

		$type = $post->id == $question->id ? 'question' : 'reply';

		$permalink 	= SocialDiscussHelper::getPermalink($question->id) . '#answer';

		// Remove code blocks from the content
		$post->content = ED::parser()->removeCodes($post->content);
		$post->content = $this->processContent($post);
		$post->title = $question->title;

		$this->set('permalink', $permalink);
		$this->set('type', $type);
		$this->set('post', $post);
		$this->set('actor', $actor);
		$this->set('question', $question);
		$this->set('content', $post->content);

		$item->title = parent::display('streams/accepted.title');
		$item->preview = parent::display('streams/post.preview');

		$item->addOgDescription($post->content);
	}

	/**
	 * Processes stream items for reply on discussion
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function replyDiscussStream(SocialStreamItem &$item)
	{
		// Load up the post
		$post = ED::post($item->contextId);
		$question = $post->getParent();

		// If the user can't view the question, there is no point displaying the stream item
		if (!$question->canView()) {
			return;
		}

		// check if user can view this reply or not.
		if (!$post->canView()) {
			return;
		}

		// Format the content
		$content = $this->processContent($post);

		// Generate the reply permalink
		$post->permalink = $this->getReplyPermalink($item, $post);

		$this->set('actor', $item->actor);
		$this->set('content', $content);
		$this->set('post', $post);
		$this->set('question', $question);

		$item->title = parent::display('streams/reply.title');
		$item->preview = parent::display('streams/reply.content');

		$item->addOgDescription($post->content);
	}

	/**
	 * Retrieve the reply permalink from the params
	 * since we have stored it during the reply saving process
	 *
	 * @since   4.0
	 * @access  public
	 */
	private function getReplyPermalink($item, $post)
	{
		if (!empty($item->params)) {
			$registry = ES::registry($item->params);

			$permalink = $registry->get('replyPermalink');

			return EDR::_($permalink, true);
		}

		return $post->getReplyPermalink();
	}

	/**
	 * Process notification for easydiscuss items
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		if (!$this->exists()) {
			return;
		}

		// Allowed notification types
		$allowed = array('discuss.create', 'discuss.accepted', 'discuss.accepted.owner', 'discuss.comment', 'discuss.reply', 'discuss.likes');

		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		// Get the actor
		$actor 	= ES::user($item->actor_id);
		$target = ES::user($item->target_id);

		// Load the current post data
		$post = ED::post($item->uid);

		// Retrieve the actor name of the post
		$actorName = $actor->getName();

		// If that is anonymous post, need to override their real name to anonymous user
		if ($post->isAnonymous() && ($item->cmd == 'discuss.create' || $item->cmd == 'discuss.reply')) {
			$actorName = JText::_('COM_EASYDISCUSS_ANONYMOUS_USER');
		}

		if ($item->cmd == 'discuss.comment') {

			$comment = ES::table('Comments');
			$comment->load($item->uid);
			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_COMMENT', $actorName);
			$item->content = JString::substr(strip_tags($comment->comment), 0, 50) . JText::_('COM_EASYSOCIAL_ELLIPSES');
			return $item;
		}

		if ($item->cmd == 'discuss.create') {
			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_CREATE', $actorName);
			$item->content = $post->title;
			return $item;
		}

		if ($item->cmd == 'discuss.likes') {
			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_LIKES', $actorName);
			return $item;
		}

		if ($item->cmd == 'discuss.comment') {
			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_COMMENT', $actorName);
			return $item;
		}

		$content = $post->getContent();
		$content = strip_tags($content);

		if ($item->cmd == 'discuss.reply') {
			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_REPLY', $actorName);
			$item->content = JString::substr($content, 0, 50) . JText::_('COM_EASYSOCIAL_ELLIPSES');
			return $item;
		}

		if ($item->cmd == 'discuss.accepted.owner') {
			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_ACCEPTED_OWNER', $actorName);
			$item->content 	= JString::substr($content, 0, 50) . JText::_('COM_EASYSOCIAL_ELLIPSES');
			return $item;
		}

		if ($item->cmd == 'discuss.accepted') {

			// If the person performing the action is the actor, we wouldn't want to display anything
			$my = ES::user();

			if ($my->id == $actor->id) {
				$item->title 	= '';
				$item->content 	= '';
				return $item;
			}

			$item->title = JText::sprintf('APP_USER_EASYDISCUSS_NOTIFICATIONS_ACCEPTED_YOUR', $actorName);
			$item->content = JString::substr($content, 0, 50) . JText::_('COM_EASYSOCIAL_ELLIPSES');
			return $item;
		}
	}

	/**
	 * Processes stream items when a new comment is posted on a post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function commentDiscussStream(SocialStreamItem &$item)
	{
		// Load the comment
		$comment = ED::table('Comment');
		$comment->load((int) $item->contextId);

		// Load the post object
		$post = ED::post($comment->post_id);

		// Load the question
		$question = $post->isQuestion() ? $post : $post->getParent();

		// If the user can't view the question, there is no point displaying the stream item
		if (!$question->canView()) {
			return;
		}

		// if this is a comment to a reply, we check if user can view the reply or not.
		// check if user can view this reply or not.
		if ($post->isReply() && !$post->canView()) {
			return;
		}

		$this->set('post', $post);
		$this->set('question', $question);
		$this->set('comment', $comment);
		$this->set('actor', $item->actor);

		$item->title = parent::display('streams/comment.title');
		$item->preview = parent::display('streams/comment.content');
	}

	/**
	 * Processes stream items when a user favorite a post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function favouriteDiscussStream(SocialStreamItem &$item)
	{
		// Load the post object
		$post = ED::post($item->contextId);

		// If the user can't view the question, there is no point displaying the stream item
		if (!$post->canView()) {
			return;
		}

		// Format the post's content
		$content = $this->processContent($post);

		$this->set('post', $post);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$item->title = parent::display('streams/favourite.title');
		$item->preview = parent::display('streams/post.preview');
	}

	/**
	 * Processes the stream item when a user likes a post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function likesDiscussStream(SocialStreamItem &$item)
	{
		$post = ED::post((int) $item->contextId);

		// Determine if there is a parent
		$question = $post->isQuestion() ? $post : $post->getParent();

		// If the user can't view the question, there is no point displaying the stream item
		if (!$question->canView()) {
			return;
		}

		// Format the content
		$content = $this->processContent($post);

		$this->set('question', $question);
		$this->set('content', $content);
		$this->set('post', $post);
		$this->set('actor', $item->actor);

		$item->title = parent::display('streams/likes.title');
		$item->preview = parent::display('streams/post.preview');
	}

	/**
	 * Processes the stream item when a user votes on a post
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function voteDiscussStream(SocialStreamItem &$item)
	{
		// Get the post object
		$post = ED::post((int) $item->contextId);

		// Determine if there is a parent
		$question = $post->isQuestion() ? $post : $post->getParent();

		// if this is a vote on reply and user has no permission to view reply, dont show this stream.
		if ($post->isReply() && !$post->canView()) {
			return;
		}

		// If the user can't view the question, there is no point displaying the stream item
		if (!$question->canView()) {
			return;
		}

		// Process the content
		$content = $this->processContent($post);

		$post = $question;

		$this->set('content', $content);
		$this->set('actor', $item->actor);
		$this->set('post', $post);
		$this->set('question', $question);

		$item->title = parent::display('streams/vote.title');
		$item->preview = parent::display('streams/post.preview');
	}

	/**
	 * Prepares the preview of the discussion
	 *
	 * @since	4.0
	 * @access	public
	 */
	private function processContent($post)
	{
		// Get app params
		$params = $this->getParams();
		$maxLength = $params->get('stream_max_chars', '');

		// Get the content
		$content = $post->getContent();

		if ($maxLength) {
			$content = ES::themes()->html('string.truncate', $content, $maxLength);
		}

		return $content;
	}

	private function getImage($text)
	{
		// IMG BBCode to find...
		$imgCodeSearch = array(
						 '/\[img\](.*?)\[\/img\]/ims'
		);

		// And replace them by...
		$imgCodeReplace = array(
						 '<img src="\1" alt="\1" />'
		);

		$text = preg_replace($imgCodeSearch ,$imgCodeReplace, $text);

		$img = '';
		$pattern = '#<img[^>]*>#i';
		preg_match($pattern, $text, $matches);

		if ($matches) {
			$img = $matches[0];
		}

		return $img;
	}

	public function onAfterCommentSave(&$comment)
	{
		$params = $this->getParams();
		$identifier = explode('.', $comment->element);


		if (empty($identifier[0]) || $identifier[0] !== 'discuss') {
			return;
		}

		list($element, $group, $verb) = $identifier;

		$streamItem = ES::table('StreamItem');
		$streamItem->load(array('actor_type' => 'user', 'context_type' => 'discuss', 'verb' => $verb, 'context_id' => $comment->uid));

		if (!$this->exists() || $streamItem->context_type != 'discuss') {
			return;
		}

		$sys = array(
			'context_type' => $identifier[0],
			'url' => $streamItem->getPermalink(false, false, false),
			'actor_id' => $comment->created_by,
			'uid' => $comment->id
		);

		// Notify stream actor separately
		if ($streamItem->actor_id != $comment->created_by) {
			ES::notify('discuss.comment', array($streamItem->actor_id), false, $sys);
		}

		if (!$params->get('stream_comment_convert')) {
			return;
		}

		// we need to check if this post is lock or not.
		$parent = ED::post($streamItem->context_id);
		if ($parent->isLocked()) {
			return;
		}


		// Data used to save this comment as reply in the discussion
		$data = array(
						'content' => $comment->comment,
						'parent_id' => $streamItem->context_id,
						'user_id' => $comment->created_by,
						'post_type' => 'default',
						'created' => ES::date()->toSql(),
						'replied' => ES::date()->toSql(),
						'user_type' => 'member',
						'published' => 1
					);

		$saveOptions = array('saveFromEasysocialStory' => true);

		$post = ED::post();
		$post->bind($data);

		// check the reply validate is it pass or not
		$valid = $post->validate($data, 'replying');

		// if one of the validate is not pass through
		if ($valid === false) {
			return false;
		}

		// Save the discussion here
		$state = $post->save($saveOptions);

		if ($state) {
			// If the post is successfully stored, we upload the attachment
			$this->uploadAttachments($post);
		}
	}

	public function uploadAttachments($post)
	{
		// Get the attachment id
		$attachments = $this->input->get('attachmentIds', array(), 'array');

		if ($attachments) {
			foreach ($attachments as $attachmentId) {

				$attachmentId = (int) $attachmentId;

				// Load the ES attachment obj
				$uploader = ES::table('Uploader');
				$uploader->load($attachmentId);

				// Replicate the file obj
				$file['name'] = $uploader->name;
				$file['type'] = $uploader->mime;
				$file['tmp_name'] = $uploader->path;
				$file['error'] = '';
				$file['size'] = $uploader->size;

				// Upload an attachment to ED
				$attachment = ED::attachment();
				$attachment->upload($post, $file);
			}
		}
	}

	public function onAfterLikeSave(&$likes)
	{
		$allowedVerbs = array('create', 'accepted', 'reply', 'comment', 'favourite', 'likes', 'vote');

		$identifier = explode('.', $likes->type);

		if (empty($identifier[0]) || $identifier[0] !== 'discuss') {
			return;
		}

		list($element, $group, $verb) = $identifier;

		$streamItem = ES::table('StreamItem');
		$streamItem->load(array('actor_type' => 'user', 'context_type' => 'discuss', 'verb' => $verb, 'context_id' => $likes->uid));

		$poster = ES::user($likes->created_by);

		if ($streamItem->actor_id != $likes->created_by) {

			$sys = array(
				// 'title' => JText::sprintf( 'COM_EASYSOCIAL_SYSTEM_STORY_LIKE_ITEM' , $poster->getName() ),
				'context_type' => $identifier[0],
				'url' => $streamItem->getPermalink(false, false, false),
				'actor_id' => $likes->created_by,
				'uid' => $likes->uid
			);

			ES::notify('discuss.likes', array($streamItem->actor_id), false, $sys);
		}
	}
}
