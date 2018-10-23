<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

class SocialUserAppBlog extends SocialAppItem
{
	/**
	 * Function being triggered from EasySocial when processing GDPR data.
	 *
	 * @since	5.2.5
	 * @access	public
	 */
	public function onExportGdpr(SocialGdprSection &$section, &$esParams)
	{
		$esUser = $section->user;
		$esLib = ES::gdpr();

		$ebSection = $esLib->createSection($section->user, 'blog', JText::_('APP_USER_BLOG_GDPR_SECTION'), false);

		$items = $this->getAvailableAdapters();

		$params = EB::registry();

		$states = array();

		// Ensure the class is not exists yet before include. #1649
		if (!class_exists('SocialGdprItem')) {
			$file = __DIR__ . '/gdpr/item.php';
			require_once($file);
		}

		foreach ($items as $type) {

			$file = EBLOG_LIB . '/gdpr/types/' . $type . '.php';
			require_once($file);

			$className = 'EasyBlogGdpr' . ucfirst($type);
			$adapter = new $className($esUser->id, $params);

			$esAdapter = new SocialGdprItem($section->user, $esParams);
			$adapter->onEasySocialGdprExport($ebSection, $esAdapter, $esParams);

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
	 * @since	5.2.5
	 * @access	public
	 */
	public function getAvailableAdapters()
	{
		static $adapters = null;

		if (is_null($adapters)) {
			$files = JFolder::files(EBLOG_LIB . '/gdpr/types', '.php$', false, false, array('.svn', 'CVS', '.DS_Store', '__MACOSX', 'template.php'));

			foreach ($files as $file) {
				$adapters[] = str_ireplace('.php', '', $file);
			}
		}

		return $adapters;
	}


	public function onComponentStart()
	{
		if (!$this->exists()) {
			return;
		}

		$view = $this->input->get('view');

		$params = $this->getParams();

		// Inject easyblog scripts on the page
		if (($view == 'dashboard' || $view == 'profile') && $params->get('blog_form', true)) {

			EB::init('site');
			$config = EB::config();

			$stylesheet = EB::stylesheet('site', $config->get('theme_site'));
			$stylesheet->attach();
		}
	}

	/**
	 * Determines if EasyBlog exists
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function exists()
	{
		$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		return true;
	}

	/**
	 * Renders the notification item in EasySocial
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		if ($item->type != 'blog') {
			return;
		}

		if (!$this->exists()) {
			$item->title = '';
			return;
		}

		$post = EB::post($item->uid);

		$actor = ES::user($item->actor_id);

		if (!$item->content) {
			$item->content = $post->title;
		}

		$item->image = $post->hasImage()? $post->getImage('thumbnail') : '';

		// Set the title of the notification
		if ($item->cmd == 'blog.create') {
			$item->title = JText::sprintf('APP_USER_BLOG_NOTIFICATION_CREATED', $actor->getName());
		}

		// Set the title of the notification
		if ($item->cmd == 'blog.likes') {
			// $item->title = JText::sprintf('APP_USER_BLOG_NOTIFICATION_LIKE_POST', $actor->getName());

			// below code are meant for ES 2.1
			$item->context_type = 'blog.user.create';
			$hook = $this->getHook('notification', 'likes');
			$hook->execute($item);
		}

		// Set the title of the notification
		if ($item->cmd == 'blog.comment') {
			$item->title = JText::sprintf('APP_USER_BLOG_NOTIFICATION_COMMENT_POST', $actor->getName(), $post->title);
		}

		if ($item->cmd == 'blog.comment.reply') {
			$item->title = JText::sprintf('APP_USER_BLOG_NOTIFICATION_COMMENT_REPLY_POST', $actor->getName(), $post->title);
		}

		return $item;
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

		// We only allow event creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
		// Empty target is also allowed because it means no target.
		if (!empty($story->target) && $story->target != ES::user()->id) {
			return;
		}

		// Ensure that user has access to this apps.
		if (!$this->getApp()->hasAccess($this->my->profile_id)) {
			return;
		}

		$params = $this->getParams();

		if (!$params->get('blog_form', true)) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('blog', 'panel');

		// check for acl to see if this user has the acl to blog or not
		$acl = EB::acl();
		if (!$acl->get('add_entry')) {
			return;
		}

		EB::loadLanguages();

		$title = JText::_('APP_BLOG_STORY_BLOG');
		$plugin->title = $title;

		// Get the theme class
		$theme = ES::themes();

		// Get the available blog category
		$model = EB::model('Category');
		$defaultCategory = $model->getDefaultCategory();
		$categories = EB::populateCategories('', '', 'select', 'categoryid', $defaultCategory->id, true, true, true, array(), 'data-story-blog-category', 'COM_EASYBLOG_SELECT_PARENT_CATEGORY', 'o-form-control');

		$theme->set('categories', $categories);
		$theme->set('title', $plugin->title);

		$plugin->button->html = $theme->output('apps/user/blog/story/panel.button');
		$plugin->content->html = $theme->output('apps/user/blog/story/panel.form');

		$script = ES::get('Script');
		$script->set('errorTitle', JText::_('APP_USER_BLOG_INVALID_TITLE', true));
		$script->set('errorContent', JText::_('APP_USER_BLOG_INVALID_CONTENT', true));
		$plugin->script = $script->output('apps:/user/blog/story');

		return $plugin;
	}

	/**
	 * When a user submits a new item on the story, we need to create the blog post
	 *
	 * @since	5.0
	 */
	public function onBeforeStorySave(&$template, &$stream, &$content)
	{
		if (!$this->exists() || $template->context_type != 'blog') {
			return;
		}

		// Retrieve the post data
		$title = $this->input->get('blog_title', '', 'default');
		$content = $this->input->get('blog_content', '', 'default');
		$category = $this->input->get('blog_categoryId', 0, 'int');

		if (!$title) {
			return false;
		}

		$author = ES::user();
		$post = EB::post();

		$data = new stdClass();
		$data->title = $title;
		$data->content = $content;
		$data->category_id = $category;
		$data->created_by = $author->id;

		// Get the current date
		$current = ES::date();

		// here we need to check if user has the publish post acl or not.
		$data->published = EASYBLOG_POST_PUBLISHED;

		$acl = EB::acl();
		if (!$acl->get('publish_entry')) {
			$data->published = EASYBLOG_POST_PENDING;
		}

		$data->created = $current->toSql();
		$data->modified = $current->toSql();
		$data->publish_up = $current->toSql();
		$data->publish_down	= '0000-00-00 00:00:00';
		$data->frontpage = 1;
		$data->allowcomment = 1;
		$data->subscription = 1;

		$post->create(array('overrideDoctType' => 'legacy'));

		// now let's get the uid
		$data->uid = $post->uid;
		$data->revision_id = $post->revision->id;

		// binding
		$post->bind($data, array());

		$saveOptions = array(
						'applyDateOffset' => false,
						'validateData' => false,
						'useAuthorAsRevisionOwner' => true,
						'saveFromEasysocialStory' => true
						);

		$post->save($saveOptions);

		$template->context_type = 'blog';

		$template->context_id = $post->id;

		return;
	}


	/**
	* Responsible to return the excluded verb from this app context
	* @since 2.0
	* @access public
	* @param array
	*/
	public function onStreamVerbExclude(&$exclude, $perspective = null)
	{
		// Get app params
		$params = $this->getParams();

		$excludeVerb = false;

		if (! $params->get('stream_create', true)) {
			$excludeVerb[] = 'create';
		}

		if (! $params->get('stream_updates', true)) {
			$excludeVerb[] = 'update';
		}

		if (! $params->get('stream_comments', true)) {
			$excludeVerb[] = 'create.comment';
		}

		if (! $params->get('stream_featured', true)) {
			$excludeVerb[] = 'featured';
		}


		// add pages into exclude list
		if ($excludeVerb !== false) {
			$exclude['blog'] = $excludeVerb;
		}
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if (!$this->exists() || $item->context != 'blog') {
			return;
		}

		if ($item->verb != 'create.comment' && $item->verb != 'add.reaction') {
			$post = EB::post($item->contextId);

			// if this is coming from story form and the post is under pending review,
			// show a proper message.
			if ($post->isPending()) {
				// we can only check agains the form post.
				$namespace = $this->input->get('namespace', '', 'default');

				if ($namespace && strpos($namespace, 'story/create') !== false) {
					$this->set('postTitle', $post->getTitle());
					$item->display = SOCIAL_STREAM_DISPLAY_MINI;
					$item->title = parent::display('streams/pending.title');
					return;
				}
			}

			// Skip this if the blog post is not published.
			if ($post->isPending() || $post->isUnpublished() || $post->isTrashed()) {
				return;
			}
		}

		// Attach our own stylesheet
		$this->getApp()->loadCss();

		// Get the context of the stream item
		$element = $item->context;
		$uid = $item->contextId;

		if ($item->isCluster()) {
			$cluster = $item->getCluster();

			if (!$cluster->canViewItem()) {
				return;
			}
		}

		if (!$item->isCluster()) {
			// Get user's privacy.
			$privacy = $this->my->getPrivacy();

			$validate = $privacy->validate('easyblog.blog.view', $uid, $element, $item->actor->id);

			// Determine if the user can view this current context
			if ($includePrivacy && !$validate) {
				return;
			}

			// Bind the privacy item
			if ($includePrivacy) {
				$item->privacy = $privacy->form($uid, $element, $item->actor->id, 'easyblog.blog.view', false, null, array(), array('iconOnly' => true));
			}
		}

		// Define standard stream looks
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->color = '#FFDB77';
		$item->fonticon = 'ies-pencil-2';
		$item->label = JText::_('APP_USER_BLOG_STREAM_TOOLTIP');

		$params = $this->getParams();

		$item->content = '';

		// New blog post
		if ($item->verb == 'create' && $params->get('stream_create', true)) {
			$this->prepareNewBlogStream($item);
		}

		// Updated blog post
		if ($item->verb == 'update' && $params->get('stream_updates', true)) {
			$this->prepareUpdateBlogStream($item);
		}

		// New comment
		if ($item->verb == 'create.comment' && $params->get('stream_comments', true)) {
			$this->prepareNewCommentStream($item);
		}

		// Featured posts
		if ($item->verb == 'featured' && $params->get('stream_featured', true)) {
			$this->prepareFeaturedBlogStream($item);
		}

		// New reaction
		if ($item->verb == 'add.reaction' && $params->get('stream_reaction', true)) {
			$this->prepareNewReactionStream($item);
		}
	}

	/**
	 * Renders the stream item for reaction on blog post
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function prepareNewReactionStream(&$item)
	{
		$reactionHistory = EB::table('ReactionHistory');
		$reactionHistory->load($item->contextId);

		if (!$reactionHistory->post_id) {
			return;
		}

		$post = $reactionHistory->getPost();

		// Skip this if the blog post is not published.
		if ($post->isUnpublished() || $post->isTrashed()) {
			return;
		}

		if (!$this->canViewPost($post)) {
			return;
		}

		// Attach stylesheet for the reactions
		EB::init('site');
		$config = EB::config();

		$stylesheet = EB::stylesheet('site', $config->get('theme_site'));
		$stylesheet->attach();

		$reaction = EB::table('Reaction');
		$reaction->load($reactionHistory->reaction_id);

		$url = $post->getPermalink();

		// Format the likes for the stream
		$likes = ES::likes();
		$likes->get($reactionHistory->id, 'blog', 'reactions');
		$item->likes = $likes;

		// Apply comments on the stream
		$item->comments = ES::comments($item->contextId, 'blog', 'reactions', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		$this->set('permalink', $url);
		$this->set('post', $post);
		$this->set('actor', $item->actor);
		$this->set('reaction', $reaction->type);

		$item->display  = SOCIAL_STREAM_DISPLAY_MINI;
		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->preview = '';
	}

	/**
	 * Output the content of the tags available in the stream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getTagsContent(&$item)
	{
		$tags = $item->tags;

		if (!empty($tags)) {
			$totalTags = count($tags);

			$theme = ES::themes();
			$theme->set('currentTotal', 1);
			$theme->set('tags', $tags);
			$theme->set('totalTags', $totalTags);

			return $theme->output('apps/user/blog/streams/tags.preview');
		}

		return '';
	}

	/**
	 * Displays the stream item for new blog post
	 *
	 * @since	1.0
	 * @access	public
	 */
	private function prepareNewBlogStream(&$item)
	{
		// Load the post
		$post = EB::post($item->contextId);

		if (!$post->id) {
			return;
		}

		if (!$post->getPrimaryCategory()) {
			return;
		}

		if (!$this->canViewPost($post)) {
			return;
		}

		// Format the likes for the stream
		$likes = ES::likes();
		$likes->get($item->contextId, 'blog', 'create');
		$item->likes = $likes;

		// Apply comments on the stream
		$url = $post->getExternalPermalink();
		$item->comments = ES::comments($item->contextId, 'blog', 'create', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		// We might want to use some javascript codes.
		EB::init('site');

		// Get app params
		$appParams = $this->getParams();

		// Get the configured alignment for image
		$extraAlignmentClass = $appParams->get('imagealignment', 'right') == 'right' ? 't-lg-ml--lg ' : 't-lg-mr--lg ';
		$alignment = $extraAlignmentClass . 'pull-' . $appParams->get('imagealignment', 'right');

		$maxLength = $appParams->get('maxlength');

		// Determine if we should use EB truncation instead of the apps truncation
		$useEBTruncation = !$maxLength ? null : false;

		// Get the content
		$content = $post->getIntro(true, $useEBTruncation, 'intro', $maxLength, array('triggerPlugins' => false));

		// Use apps truncation
		if (!is_null($useEBTruncation)) {
			$content = $this->truncateStreamContent($content, $maxLength);
		}

		// Remove loadmodule tag
		$content = $post->removeLoadmodulesTags($content);

		// Get the cluster
		$cluster = $item->getCluster();

		// Prepare the namespace
		$group = $item->cluster_type ? $item->cluster_type : SOCIAL_TYPE_USER;

		// Get available tags in the stream
		$tagsOutput = $this->getTagsContent($item);

		$this->set('alignment', $alignment);
		$this->set('post', $post);
		$this->set('actor', $item->actor);
		$this->set('content', $content);
		$this->set('cluster', $cluster);
		$this->set('cluster_type', $group);
		$this->set('tagsOutput', $tagsOutput);

		$titleNamespace = 'streams/' . $group . '/' . $item->verb . '.title';
		$contentNamespace = 'streams/preview';

		$item->title = parent::display($titleNamespace);
		$item->preview = parent::display($contentNamespace);

		// Append the opengraph tags
		$item->addOgImage($post->getImage('thumbnail'));
		$item->addOgDescription($content);
	}

	/**
	 * Renders the stream item for post being featured
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function prepareFeaturedBlogStream(&$item)
	{
		$post = EB::post($item->contextId);

		if (!$post) {
			return;
		}

		if (!$post->getPrimaryCategory()) {
			return;
		}

		if (!$this->canViewPost($post)) {
			return;
		}

		// Format the likes for the stream
		$likes = ES::likes();
		$likes->get($item->contextId, 'blog', 'featured');
		$item->likes = $likes;

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id, true, null, false, true);

		$item->comments = ES::comments($item->contextId, 'blog', 'featured', SOCIAL_APPS_GROUP_USER , array('url' => $url));

		$date = EB::date($post->created);

		$content = $post->getIntro(true, true, 'intro', null, $options = array('triggerPlugins' => false));

		$appParams = $this->getParams();
		$extraAlignmentClass = $appParams->get('imagealignment', 'right') == 'right' ? 't-lg-ml--lg ' : 't-lg-mr--lg ';
		$alignment = $extraAlignmentClass . 'pull-' . $appParams->get('imagealignment', 'right');

		$this->set('alignment' , $alignment);

		$contentLength  = $appParams->get('maxlength');

		if ($contentLength > 0) {
			$content = $post->getIntro(true, true, 'intro', null, $options = array('triggerPlugins' => false));
			// truncate the content
			$content = $this->truncateStreamContent($content, $contentLength);
		}

		// Remove loadmodule tag
		$content = $post->removeLoadmodulesTags($content);

		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$video = $this->getVideo($content);

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('post', $post);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $post->category_id, true, null, false, true);
		$this->set('categorypermalink', $catUrl);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->preview = parent::display('streams/preview');

		// Append the opengraph tags
		if ($post->getImage()) {
			$item->addOgImage($post->getImage('thumbnail'));
		}

		$item->addOgDescription($content);

	}

	/**
	 * Generates the stream for updated stream item
	 *
	 * @since	5.2
	 * @access	public
	 */
	private function prepareUpdateBlogStream(&$item)
	{
		$post = EB::post($item->contextId);

		// Post could be deleted from the site by now.
		if (!$post->id) {
			return;
		}

		if (!$post->getPrimaryCategory()) {
			return;
		}

		if (!$this->canViewPost($post)) {
			return;
		}

		// Format the likes for the stream
		$likes = ES::likes();
		$likes->get($item->contextId, 'blog', 'update');
		$item->likes = $likes;

		$url = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id);

		// Apply comments on the stream
		$item->comments = ES::comments($item->contextId, 'blog', 'update', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		// We might want to use some javascript codes.
		EB::init('site');

		$date = EB::date($post->created);

		$appParams = $this->getParams();
		$extraAlignmentClass = $appParams->get('imagealignment', 'right') == 'right' ? 't-lg-ml--lg ' : 't-lg-mr--lg ';
		$alignment = $extraAlignmentClass . 'pull-' . $appParams->get('imagealignment', 'right');

		$this->set('alignment', $alignment);

		$maxLength = $appParams->get('maxlength');

		// Determine if we should use EB truncation instead of the apps truncation
		$useEBTruncation = !$maxLength ? null : false;

		// Get the content
		$content = $post->getIntro(true, $useEBTruncation, 'intro', $maxLength, array('triggerPlugins' => false));

		// Use apps truncation
		if (!is_null($useEBTruncation)) {
			$content = $this->truncateStreamContent($content, $maxLength);
		}

		// Remove loadmodule tag
		$content = $post->removeLoadmodulesTags($content);

		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$video = $this->getVideo($content);

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $post->category_id, true, null, false, true);

		$this->set('categorypermalink', $catUrl);
		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('post', $post);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->preview = parent::display('streams/preview');

		// Append the opengraph tags
		if ($post->getImage()) {
			$item->addOgImage($post->getImage('thumbnail'));
		}

		$item->addOgDescription($content);
	}

	/**
	 * Renders the stream item for comments on blog post
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function prepareNewCommentStream(&$item)
	{
		$comment = EB::table('Comment');
		$comment->load($item->contextId);

		if (!$comment->post_id) {
			return;
		}

		$post = EB::post($comment->post_id);

		if (!$this->canViewPost($post)) {
			return;
		}

		// Format the likes for the stream
		$likes = ES::likes();
		$likes->get($comment->id, 'blog', 'comments');
		$item->likes = $likes;

		$url = $post->getPermalink();

		// Apply comments on the stream
		$item->comments = ES::comments($item->contextId, 'blog', 'comments', SOCIAL_APPS_GROUP_USER, array('url' => $url));

		$post = EB::post($comment->post_id);

		// Skip this if the blog post is not published.
		if ($post->isUnpublished() || $post->isTrashed()) {
			return;
		}

		$date = EB::date($post->created);

		// Parse the bbcode from EasyBlog
		$comment->comment = EB::comment()->parseBBCode($comment->comment);

		$this->set('comment', $comment);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $post);
		$this->set('actor', $item->actor);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->preview = parent::display('streams/preview.comment');

		$item->addOgDescription($comment->comment);
	}

	/**
	 * Triggered before comments notify subscribers
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed = array('blog.user.create', 'blog.user.update', 'blog.user.create.comment', 'blog.user.featured');

		if (!in_array($comment->element, $allowed) || !$this->exists()) {
			return;
		}

		// When a comment is posted in the stream, we also want to move it to EasyBlog's comment table.
		$ebComment = EB::table('Comment');
		$ebComment->post_id = $comment->uid;
		$ebComment->comment = $comment->comment;
		$ebComment->created_by = $comment->created_by;
		$ebComment->created	= $comment->created;
		$ebComment->modified = $comment->created;
		$ebComment->published = true;

		// Save the comment
		$state = $ebComment->store();

		// Get the blog post
		$post = EB::post($comment->uid);

		$isSiteMultilingualEnabled = EB::isSiteMultilingualEnabled();

		$defaultPermalink = 'index.php?option=com_easyblog&view=entry&id=' . $post->id;

		if ($isSiteMultilingualEnabled) {

			// For some reason if the post language columns is stored empty data, we will override this.
			if (empty($post->language)) {
				$post->language = '*';
			}

			// retrieve infront language code
			$langcode = substr($post->language, 0, 2);

			if ($post->language != '*') {

				$permalink = EBR::_($defaultPermalink . '&lang=' . $langcode, false, null, false, false, false);

			} else {
				$permalink = $defaultPermalink;
			}

		} else {
			$permalink = EBR::_($defaultPermalink, false, null, false, false, false);
		}

		$options = array(
			'context_type' => 'blog.comment',
			'url' => $permalink,
			'actor_id' => $comment->created_by,
			'uid' => $post->id,
			'aggregate' => false
		);

		if ($comment->created_by != $post->created_by) {
			ES::notify('blog.comment', array($post->created_by), false, $options);
		}
	}

	/**
	 * event onLiked on story
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed = array('blog.user.create', 'blog.user.update', 'blog.user.create.comment', 'blog.user.featured');

		if (!in_array($likes->type, $allowed) || !$this->exists()) {
			return;
		}

		$segments = explode('.', $likes->type);

		$element = array_shift($segments);
		$group = array_shift($segments);
		$verb = implode('.', $segments);

		// Get the owner of the blog post
		$post = EB::post($likes->uid);

		$recipients = array($post->created_by);

		$isSiteMultilingualEnabled = EB::isSiteMultilingualEnabled();

		$defaultPermalink = 'index.php?option=com_easyblog&view=entry&id=' . $post->id;

		if ($isSiteMultilingualEnabled) {

			// For some reason if the post language columns is stored empty data, we will override this.
			if (empty($post->language)) {
				$post->language = '*';
			}

			// retrieve infront language code
			$langcode = substr($post->language, 0, 2);

			if ($post->language != '*') {

				$permalink = EBR::_($defaultPermalink . '&lang=' . $langcode, false, null, false, false, false);

			} else {
				$permalink = $defaultPermalink;
			}

		} else {
			$permalink = EBR::_($defaultPermalink, false, null, false, false, false);
		}

		$options = array(
			// 'title' => $title,
			'context_type' => 'blog.likes',
			'url' => $permalink,
			'actor_id' => $likes->created_by,
			'uid' => $post->id,
			'aggregate' => false
		);

		if ($likes->created_by != $post->created_by) {
			ES::notify('blog.likes', array($post->created_by), false, $options);
		}

		// Do we want to notify participants?
		// $recipients = $this->getStreamNotificationTargets($likes->uid, $element, $group, $verb, array(), array($blog->created_by, $likes->created_by));
	}

	/**
	 * Prepares the activity log
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function onPrepareActivityLog(SocialStreamItem &$item, $includePrivacy = true)
	{

		if ($this->exists() === false) {
			return;
		}

		if ($item->context != 'blog') {
			return;
		}

		// Stories wouldn't be aggregated
		$actor = $item->actor;
		$permalink = '';

		if ($item->verb == 'create.comment') {
			$comment = EB::table('Comment');
			$comment->load($item->contextId);
			$permalink = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $comment->post_id, true, null, false, true);

			$post = EB::post($comment->post_id);
			$this->set('comment', $comment);

		} else {
			$post = EB::post($item->contextId);
			$permalink = EBR::_('index.php?option=com_easyblog&view=entry&id=' . $post->id, true, null, false, true);
		}

		$this->set('actor', $actor);
		$this->set('post', $post);
		$this->set('blog', $post);
		$this->set('permalink', $permalink);

		$titleNamespace = 'streams/';

		if ($item->verb == 'create') {
			// Prepare the namespace
			$group = $item->cluster_type ? $item->cluster_type : SOCIAL_TYPE_USER;
			$cluster = $item->getCluster();

			$this->set('cluster', $cluster);
			$this->set('cluster_type', $group);

			$titleNamespace .= $group . '/' . $item->verb . '.title';
		} else {
			$titleNamespace .= $item->verb . '.title';
		}

		$item->display  = SOCIAL_STREAM_DISPLAY_MINI;
		$item->title    = parent::display($titleNamespace);
		$item->content = '';

		if ($includePrivacy) {
			$privacy = $this->my->getPrivacy();

			// only activiy log can use stream->uid directly bcos now the uid is holding id from social_stream_item.id;
			$item->privacy = $privacy->form($item->contextId, $item->context, $item->actor->id, 'easyblog.blog.view', false, null, array(), array('iconOnly' => true));
		}

		return true;
	}

	public function onPrivacyChange($data)
	{
		if (!$data) {
			return;
		}

		if ($data->utype != 'blog' || !$data->uid) {
			return;
		}

		if ($this->exists() === false) {
			return;
		}


		$db = ES::db();
		$sql = $db->sql();

		$query = 'update `#__easyblog_post` set `access` = ' . $db->Quote($data->value);
		$query .= ' where `id` = ' . $db->Quote($data->uid);

		$sql->clear();
		$sql->raw($query);
		$db->setQuery($sql);
		$db->query();

		return true;
	}

	private function prepareContent(&$content)
	{
		// See if there's any audio files to process.
		$audios = EB::audio()->getItems($content);

		// Get videos attached in the content
		$videos = $this->getVideos($content);
		$video = false;

		if (isset($videos[0])) {
			$video = $videos[0];
		}

		// Remove videos from the source
		$content = EB::videos()->strip($content);

		// Remove audios from the content
		$content = EB::audio()->strip($content);

		$this->set('video', $video);
		$this->set('audios', $audios);
		$this->set('date', $date);
		$this->set('permalink', $url);
		$this->set('blog', $blog);
		$this->set('actor', $item->actor);
		$this->set('content', $content);

		$catUrl = EBR::_('index.php?option=com_easyblog&view=categories&layout=listings&id=' . $blog->category_id, true, null, false, true);
		$this->set('categorypermalink', $catUrl);

		$item->title = parent::display('streams/' . $item->verb . '.title');
		$item->content = parent::display('streams/' . $item->verb . '.content');
	}

	private function getVideo($content)
	{
		$videos = EB::videos()->getVideoObjects($content);

		if (isset($videos[0])) {
			return $videos[0];
		}

		return false;
	}

	public function onIndexerReIndex(&$itemCount)
	{
		if ($this->exists() === false) {
			return;
		}

		static $rowCount = null;

		$db = ES::db();
		$sql = $db->sql();

		$indexer = ES::get('Indexer', 'com_easyblog');
		$ebConfig = EB::config();
		$limit = 5;

		if (is_null($rowCount)) {
			$query = 'select count(1) from `#__easyblog_post` as a';
			$query .= ' where not exists (select b.`uid` from `#__social_indexer` as b where a.`id` = b.`uid` and b.`utype` = ' . $db->Quote('blog') . ')';
			$query .= ' and a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query .= ' and a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);

			$sql->raw($query);
			$db->setQuery($sql);

			$rowCount = $db->loadResult();
		}

		$itemCount = $itemCount + $rowCount;

		if ($rowCount) {
			$query = 'select * from #__easyblog_post as a';
			$query .= ' where not exists (select b.`uid` from #__social_indexer as b where a.`id` = b.`uid` and b.`utype` = ' . $db->Quote('blog') . ')';
			$query .= ' and a.`published` = ' . $db->Quote(EASYBLOG_POST_PUBLISHED);
			$query .= ' and a.`state` = ' . $db->Quote(EASYBLOG_POST_NORMAL);
			$query .= ' order by a.`id` limit ' . $limit;

			$sql->raw($query);
			$db->setQuery($sql);

			$rows = $db->loadObjectList();


			foreach ($rows as $row) {

				$post = EB::post();
				$post->bind($row);


				$template = $indexer->getTemplate();

				// getting the blog content
				$content = $post->intro . $post->content;


				$image = '';

				// @rule: Try to get the blog image.
				if ($post->getImage()) {
					$image = $post->getImage('thumbnail');
				}

				if (empty($image)) {
					// @rule: Match images from blog post
					$pattern = '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
					preg_match($pattern, $content, $matches);

					$image = '';

					if ($matches) {
						$image = isset($matches[1])? $matches[1] : '';

						if (EBString::stristr($matches[1], 'https://') === false && EBString::stristr($matches[1], 'http://') === false && !empty($image)) {
							$image = rtrim(JURI::root(), '/') . '/' . ltrim($image, '/');
						}
					}
				}

				if (!$image) {
					$image = rtrim(JURI::root(), '/') . '/components/com_easyblog/assets/images/default_facebook.png';
				}

				$content = $post->getContent();

				// @rule: Once the gallery is already processed above, we will need to strip out the gallery contents since it may contain some unwanted codes
				// @2.0: <input class="easyblog-gallery"
				// @3.5: {ebgallery:'name'}
				$content = EB::gallery()->removeGalleryCodes($content);

				// remove all html tags.
				$content = strip_tags($content);

				if (EBString::strlen($content) > $ebConfig->get('integrations_easysocial_indexer_newpost_length', 250)) {
					$content = EBString::substr($content, 0, $ebConfig->get('integrations_easysocial_indexer_newpost_length', 250));
				}

				// lets include the title as the search snapshot.
				$content = $post->title . ' ' . $content;
				$template->setContent($post->title, $content);

				$url = EBR::_('index.php?option=com_easyblog&view=entry&id='.$post->id);

				if ($url) {
					$url = '/' . ltrim($url, '/');
					$url = str_replace('/administrator/', '/', $url);
				}

				$template->setSource($post->id, 'blog', $post->created_by, $url);

				$template->setThumbnail($image);

				$template->setLastUpdate($post->modified);

				$indexer->index($template);

			}
		}
	}

	private function canViewPost($post)
	{
		$access = $post->isAccessible();
		return $access->allowed;
	}

	/**
	 * Truncate the stream item
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function truncateStreamContent($content, $contentLength)
	{
		// Get the app params
		$params = $this->getParams();
		$truncateType = $params->get('truncation');

		if ($truncateType == 'chars') {

			// Remove uneccessary html tags to avoid unclosed html tags
			$content = strip_tags($content);

			// Remove blank spaces since the word calculation should not include new lines or blanks.
			$content = trim($content);

			// @task: Let's truncate the content now.
			$content = EBString::substr(strip_tags($content), 0, $contentLength) . JText::_('COM_EASYSOCIAL_ELLIPSES');

		} else {

			$tag = false;
			$count = 0;
			$output = '';

			// Remove uneccessary html tags to avoid unclosed html tags
			$content = strip_tags($content);

			$chunks = preg_split("/([\s]+)/", $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			foreach($chunks as $piece) {

				if (!$tag || stripos($piece, '>') !== false) {
					$tag = (bool) (strripos($piece, '>') < strripos($piece, '<'));
				}

				if (!$tag && trim($piece) == '') {
					$count++;
				}

				if ($count > $contentLength && !$tag) {
					break;
				}

				$output .= $piece;
			}

			unset($chunks);
			$content = $output . JText::_('COM_EASYSOCIAL_ELLIPSES');
		}

		return $content;
	}
}
