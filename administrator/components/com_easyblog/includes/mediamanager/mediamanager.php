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

require_once(__DIR__ . '/adapter.php');

class EBMM extends EasyBlog
{
	/**
	 * The available extension to type mapping
	 * @var Array
	 */
	public static $types = array(
		// Images
		'jpg'	=> 'image',
		'png'	=> 'image',
		'gif'	=> 'image',
		'bmp'	=> 'image',
		'jpeg'	=> 'image',

		// Videos
		'mp4'	=> 'video',
		'swf'	=> 'video',
		'flv'	=> 'video',
		'mov'	=> 'video',
		'f4v'	=> 'video',
		'3gp'	=> 'video',
		'm4v'	=> 'video',
		'webm'	=> 'video',
		'ogv'	=> 'video',

		// Audios
		'mp3'	=> 'audio',
		'm4a'	=> 'audio',
		'aac'	=> 'audio',
		'ogg'	=> 'audio',

		// PDF
		'pdf' => 'pdf'
	);

	/**
	 * Maps the given place with the specific icons
	 * @var Array
	 */
	public static $icons = array(

		// Places
		'place/post' => 'fa fa-file',
		'place/user' => 'fa fa-folder',
		'place/shared' => 'fa fa-cloud',
		'place/flickr' => 'fa fa-flickr',
		'place/dropbox' => 'fa fa-dropbox',
		'place/album' => 'fa fa-folder',
		'place/jomsocial' => 'fa fa-folder',
		'place/easysocial' => 'fa fa-folder',
		'place/users' => 'fa fa-users',
		'place/posts' => 'fa fa-files-o',

		// Types
		'folder' => 'fa fa-folder',
		'file'   => 'fa fa-file-o',
		'image'  => 'fa fa-file-image-o',
		'audio'  => 'fa fa-file-audio-o',
		'video'  => 'fa fa-file-video-o',

		// Extensions
		'txt'  => 'fa fa-file-text-o',
		'rtf'  => 'fa fa-file-text-o',

		'htm'  => 'fa fa-file-code-o',
		'html' => 'fa fa-file-code-o',
		'php'  => 'fa fa-file-code-o',
		'css'  => 'fa fa-file-code-o',
		'js'   => 'fa fa-file-code-o',
		'json' => 'fa fa-file-code-o',
		'xml'  => 'fa fa-file-code-o',

		'zip'  => 'fa fa-file-archive-o',
		'rar'  => 'fa fa-file-archive-o',
		'7z'   => 'fa fa-file-archive-o',
		'gz'   => 'fa fa-file-archive-o',
		'tar'  => 'fa fa-file-archive-o',

		'doc'  => 'fa fa-file-word-o',
		'docx' => 'fa fa-file-word-o',
		'odt'  => 'fa fa-file-word-o',

		'xls'  => 'fa fa-file-excel-o',
		'xlsx' => 'fa fa-file-excel-o',
		'ods'  => 'fa fa-file-excel-o',

		'ppt'  => 'fa fa-file-powerpoint-o',
		'pptx' => 'fa fa-file-powerpoint-o',
		'odp'  => 'fa fa-file-powerpoint-o',

		'pdf'  => 'fa fa-file-pdf-o',
		'psd'  => 'fa fa-file-image-o',
		'tif'  => 'fa fa-file-image-o',
		'tiff' => 'fa fa-file-image-o'
	);

	/**
	 * Default ACL states for media manager
	 * @var Array
	 */
	public static $acl = array(
		'canCreateFolder'    => false,
		'canUploadItem'      => false,
		'canRenameItem'      => false,
		'canRemoveItem'      => false,
		'canMoveItem'		 => false,
		'canCreateVariation' => false,
		'canDeleteVariation' => false
	);

	public static $byte = 1048576;

	/**
	 * Generates a skeleton filegroup array
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function filegroup()
	{
		return array(
					'folder' => array(),
					'file'   => array()
				);
	}

	/**
	 * Checks if the media object exists in the system
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getMediaObject($uri, $relative = false)
	{
		static $cache = array();

		if (!isset($cache[$uri])) {

			$media = EB::table('Media');
			$exists = $media->load(array('uri' => $uri));

			if (!$exists) {

				// For backward compatibility, we try to create a new record
				$adapter = $this->getAdapter($uri);
				// $item = $adapter->getItem($uri, $relative);
				$item = $adapter->getItem($uri);

				// Filename refers to the title of the file on the filesystem
				$media->filename = $item->title;
				$media->title = JFile::stripExt($item->title);
				$media->type = $item->type;
				$media->icon = $item->icon;
				$media->key = $item->key;
				$media->uri = $item->uri;
				$media->place = $item->place;

				// when storing, we will store the absolute path. #946
				$media->url = $item->url;
				$media->parent = dirname($item->uri);
				$media->created = JFactory::getDate()->toSql();
				$media->created_by = $this->my->id;

				$meta = new stdClass();

				// Store the file / folder size
				$meta->size = $item->size;

				if ($item->type == 'image') {
					$media->preview = $item->preview;
					$meta->thumbnail = $item->thumbnail;
					$meta->variations = $item->variations;
				}


				if ($item->type == 'folder') {
					$meta->modified = $item->modified;
				}

				if ($item->type != 'folder') {
					$meta->extension = $item->extension;
					$meta->modified = $item->modified;
				}

				// we need to store the relative path so that if user change domain, the images in composer will not break. #946
				if ($relative && $item->type == 'image') {
					$media->url = EB::String()->abs2rel($media->url);
					$media->preview = EB::String()->abs2rel($media->preview);

					if ($meta->variations) {

						$items = array();
						foreach ($meta->variations as $key => $variation) {
							$variation->url = EB::String()->abs2rel($variation->url);
							$items[$key] = $variation;
						}

						$meta->variations = $items;
					}

					$meta->thumbnail = EB::String()->abs2rel($meta->thumbnail);
				}

				$media->params = json_encode($meta);

				$media->store();
			}
		}

		if ($relative && $media->type == 'image') {
			$media->preview = EB::String()->rel2abs($media->preview, JURI::root());

			$meta = json_decode($media->params);

			$meta->thumbnail = EB::String()->rel2abs($meta->thumbnail, JURI::root());
			$media->params = json_encode($meta);
		}

		return $media;
	}

	/**
	 * Deletes a variation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function deleteVariation($uri, $name)
	{
		// Check if the user is allowed to delete
		$place = $this->getPlace($uri);

		if (!$place->acl->canDeleteVariation) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		$adapter = $this->getAdapter($uri);
		$state = $adapter->deleteVariation($uri, $name);

		if ($state instanceof EasyBlogException) {
			return $state;
		}

		// Update the list of variations available once it is deleted
		$variations = $adapter->getVariations($uri);

		$media = $this->getMediaObject($uri);
		$media->updateVariations($variations);

		return true;
	}

	/**
	 * Creates a new variation
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createVariation($uri, $name, $params)
	{
		// Check if the user is allowed to delete
		$place = $this->getPlace($uri);

		if (!$place->acl->canCreateVariation) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		$adapter = $this->getAdapter($uri);
		$item = $adapter->createVariation($uri, $name, $params);

		// Update the media object in the database
		$media = $this->getMediaObject($uri);

		// Update the variations
		$media->updateVariations($item->variations);

		return $item;
	}

	/**
	 * Creates a new folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function createFolder($uri, $folder)
	{
		$folder = $this->normalizeFolderName($folder);

		// Generate an adapter for the current uri
		$adapter = $this->getAdapter($uri);
		$newUri = $adapter->createFolder($uri, $folder);

		if ($newUri instanceof EasyBlogException) {
			return $newUri;
		}

		$key = $this->getKey($newUri);
		$folder = $this->getInfo($key, true);

		return $folder;
	}

	/**
	 * Deletes an item from media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function delete($uri)
	{
		// Generate an adapter for the current uri
		$adapter = $this->getAdapter($uri);
		$state = $adapter->delete($uri);

		if ($state === true) {

			// Delete from the database
			$media = EB::table('Media');
			$media->load(array('uri' => $uri));

			if ($media->id) {
				$media->delete();
			}
		}

		return $state;
	}

	/**
	 * Generates a skeleton folder object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function folder($uri, $contents = array())
	{
		$folder = new stdClass();
		$folder->place = $uri;
		$folder->title = EasyBlogMediaManager::getPlaceName($uri);
		$folder->url = $uri;
		$folder->uri = $uri;
		$folder->key = self::getKey($uri);
		$folder->type = 'folder';
		$folder->icon = '';
		$folder->root = true;
		$folder->scantime = 0;
		$folder->contents = $contents;

		return $folder;
	}

	/**
	 * Generates a skeleton file object
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function file($uri, $type = '')
	{
		$item = new stdClass();

		$item->place = '';
		$item->title = '';
		$item->url = '';
		$item->uri = $uri;
		$item->path = '';
		$item->type = $type;
		$item->size = 0;
		$item->modified = '';
		$item->key = self::getKey($uri);
		$item->thumbnail = '';
		$item->preview = '';
		$item->variations = array();

		return $item;
	}

	/**
	 * Retrieves the adapter source type given the place id
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getSourceType($placeId)
	{
		if ($this->isPostPlace($placeId) || $this->isUserPlace($placeId) || $placeId == 'shared') {
			return EBLOG_MEDIA_SOURCE_LOCAL;
		}

		// Determines if this is an album or flickr place
		if ($this->isAlbumPlace($placeId) || $this->isFlickrPlace($placeId)) {
			$parts = explode(':', $placeId);

			if (count($parts) > 1) {
				$placeId = $parts[0];
			}
		}

		return $placeId;
	}

	/**
	 * Retrieves information about a single place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPlace($uri)
	{
		$placeId = self::getPlaceId($uri);

		return (object) array(
			'id' => $placeId,
			'title' => self::getPlaceName($placeId),
			'icon' => self::getPlaceIcon($placeId),
			'acl' => self::getPlaceAcl($placeId),
			'uri' => $placeId,
			'key' => self::getKey($placeId)
		);
	}

	/**
	 * Retrieve a list of places on the site.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getPlaces($user = null, EasyBlogPost $post = null)
	{
		$config = EB::config();
		$acl = EB::acl();

		// Get the current logged in user
		$my = JFactory::getUser($user);

		$places = array();

		// Get the current post's folder
		$places[] = $this->getPlace('post');

		// My Media
		$places[] = $this->getPlace('user:' . $my->id);

		// Shared folders
		if ($config->get('main_media_manager_place_shared_media') && $acl->get('media_places_shared')) {
			$places[] = $this->getPlace('shared');
		}

		// Flickr Integrations
		if ($config->get('layout_media_flickr') && $config->get('integrations_flickr_api_key') != '' && $config->get('integrations_flickr_secret_key') && $acl->get('media_places_flickr')) {
			$places[] = $this->getPlace('flickr');
		}

		// EasySocial
		if ($config->get('integrations_easysocial_album') && $acl->get('media_places_album') && EB::easysocial()->exists()) {
			$places[] = $this->getPlace('easysocial');
		}

		// JomSocial
		if ($config->get('integrations_jomsocial_album') && $acl->get('media_places_album') && EB::jomsocial()->exists()) {
			$places[] = $this->getPlace('jomsocial');
		}

		// All articles created by the author or admin
		$places[] = $this->getPlace('posts');

		// If the user is allowed
		if (EB::isSiteAdmin()) {

			// All Users
			$places[] = self::getPlace('users');
		}

		return $places;
	}

	/**
	 * Retrieves the place title
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getPlaceName($placeId)
	{
		$placeName = $placeId;

		if (self::isUserPlace($placeId)) {

			$my = JFactory::getUser();

			// Title should be dependent if the user is viewing their own media
			$id = explode(':', $placeId);
			$user = JFactory::getUser($id[1]);

			if ($my->id != $user->id) {
				return $user->name;
			}

			$placeName = 'user';
		}

		// If this is an article place
		if (self::isPostPlace($placeId)) {

			// Get the post id
			$id = explode(':', $placeId);
			$post = EB::post($id[1]);

			if (!$post->title) {
				return JText::sprintf('COM_EASYBLOG_MM_PLACE_POST_UNTITLED', $id[1]);
			}

			return $post->title;
		}

		// If this is an album place
		if (self::isAlbumPlace($placeId)) {

			$parts = explode('/', $placeId);
			$placeName = $placeId;

			if ($parts > 1) {
				$placeName = $parts[0];
			}
		}

		return JText::_('COM_EASYBLOG_MM_PLACE_' . strtoupper($placeName));
	}

	/**
	 * Gets the icon to be used for a place
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPlaceIcon($placeId)
	{
		$placeName = strtolower($placeId);

		if (self::isUserPlace($placeId)) {
			$placeName = 'user';
		}

		if (self::isPostPlace($placeId)) {
			$placeName = 'post';
		}

		if (self::isAlbumPlace($placeId)) {
			$placeName = 'album';
		}

		if (self::isFlickrPlace($placeId)) {
			$placeName = 'flickr';
		}

		return self::$icons["place/$placeName"];
	}

	/**
	 * Retrieves the list of allowed extensions
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAllowedExtensions()
	{
		$config = EB::config();

		$allowed = explode(',', $config->get('main_media_extensions'));

		return $allowed;
	}

	/**
	 * Determines if the user has access to a specific place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function hasAccess($placeId)
	{
		$acl = (object) self::getPlaceAcl($placeId);

		if (!$acl->canUploadItem) {
			return EB::exception('COM_EB_MM_NOT_ALLOWED_TO_UPLOAD_FILE', EASYBLOG_MSG_ERROR);
		}

		return true;
	}

	/**
	 * Gets the maximum allowed upload size
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getAllowedFilesize()
	{
		$config = EB::config();
		$maximum = (float) $config->get('main_upload_image_size', 0);

		// If it's 0, no restrictions done
		if ($maximum == 0) {
			return false;
		}

		// Compute the allowed size
		$maximum = $maximum * self::$byte;

		return $maximum;
	}

	/**
	 * Gets the ACL for the specific place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getPlaceAcl($placeId)
	{
		$my = JFactory::getUser();
		$aclLib = EB::acl();

		$allowedUpload = EB::isSiteAdmin() || $aclLib->get('upload_image');

		// TODO: I'm not sure if specific user, e.g. user 128 viewing user 64,
		// needs to be processed here. But I really like to get rid of user
		// folders altogether.
		if (self::isUserPlace($placeId)) {

			$acl = array_merge(self::$acl, array(
				'canCreateFolder'    => $allowedUpload,
				'canUploadItem'      => $allowedUpload,
				'canRenameItem'      => true,
				'canMoveItem'		 => true,
				'canRemoveItem'      => true,
				'canCreateVariation' => true,
				'canDeleteVariation' => true
			));
		}

		// Article place
		if (self::isPostPlace($placeId)) {

			$id = explode(':', $placeId);

			$post = EB::post($id[1]);

			$allowed = $my->id == $post->created_by || EB::isSiteAdmin() || $aclLib->get('moderate_entry');

			// Get the article
			$acl = array_merge(self::$acl, array(
				'canCreateFolder' => $allowedUpload,
				'canUploadItem' => $allowedUpload,
				'canRenameItem' => $allowedUpload,
				'canMoveItem' => $allowedUpload,
				'canRemoveItem' => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// Shared
		if (self::isSharedPlace($placeId)) {

			$allowed = EB::isSiteAdmin() || $aclLib->get('media_places_shared');

			$acl = array_merge(self::$acl, array(
				'canCreateFolder'    => $allowedUpload,
				'canUploadItem'      => $allowedUpload,
				'canRenameItem'      => $allowedUpload,
				'canMoveItem'		 => $allowedUpload,
				'canRemoveItem'      => $allowedUpload,
				'canCreateVariation' => $allowed,
				'canDeleteVariation' => $allowed
			));
		}

		// If there's no acl defined, we should use the default acl
		if (!isset($acl)) {
			$acl = self::$acl;
		}

		return (object) $acl;
	}

	/**
	 * Retrieves an adapter
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getAdapter($uri)
	{
		static $adapters = array();

		if (!isset($adapters[$uri])) {
			$place = $this->getPlace($uri);
			$type = $this->getSourceType($place->id);

			$adapters[$uri] = new EBMMAdapter($type, $this);
		}

		return $adapters[$uri];
	}

	/**
	 * Retrieves the type of file given the extension type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getType($extension)
	{
		$type = isset(self::$types[$extension]) ? self::$types[$extension] : 'file';

		return $type;
	}

	/**
	 * Retrieves the icon to be used given the extension type.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getIcon($extension)
	{
		$key = isset(self::$icons[$extension]) ? $extension : self::getType($extension);

		return self::$icons[$key];
	}

	/**
	 * Retrieves the place from uri
	 *
	 * Example:
	 * user:605/foo/bar
	 *
	 * Returns:
	 * user:605
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getPlaceId($uri)
	{
		$first = strpos($uri, '/');

		if ($first == false) {
			return $uri;
		}

		return substr($uri, 0, $first);
	}

	/**
	 * An alias to getFileName
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public static function getTitle($uri)
	{
		$placeId = self::getPlaceId($uri);

		// If they are identical, return place name
		if ($placeId == $uri) {
			return self::getPlaceName($placeId);
		}

		// Return filename
		return self::getFilename($uri);
	}

	/**
	 * Returns the file name based on the given uri
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getFilename($uri)
	{
		$last = strrpos($uri, '/');
		return substr($uri, $last + 1);
	}

	/**
	 * Retrieves the extension of a file given the file name.
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getExtension($filename)
	{
		$extension = JFile::getExt($filename);

		return strtolower($extension);
	}

	/**
	 * Returns path from uri
	 * user:605/foo/bar.jpg => /var/www/site.com/images/easyblog_images/605/foo/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function getPath($uri, $root = JPATH_ROOT)
	{
		// TODO: Strip off . & .. for security reasons or add other types of security measures.

		// Get place
		$placeId = self::getPlaceId($uri);

		// This speed up resolving on path of places
		static $places = array();

		$config = EB::config();

		// If this place hasn't been resolved before
		if (!isset($places[$placeId])) {

			// Shared
			if ($placeId=='shared') {
				$path = $config->get('main_shared_path');
				$places['shared'] = self::cleanPath($path);
			}

			// Articles place
			if ($placeId == 'posts') {
				$path = $config->get('main_articles_path');
				$places['posts'] = self::cleanPath($path);
			}

			if ($placeId == 'users') {
				$path = $config->get('main_image_path');
				$places['users'] = self::cleanPath($path);
			}

			// Article place
			if (self::isPostPlace($placeId)) {

				if (!isset($places['post'])) {
					$path = $config->get('main_articles_path');
					$places['post'] = self::cleanPath($path);
				}

				// Get the article id
				$parts = explode(':', $placeId);
				$articleId = $parts[1];

				// Build path
				$places[$placeId] = $places['post'] . '/' . $articleId;
			}

			// User
			if (self::isUserPlace($placeId)) {

				// Do this once to speed things up
				if (!isset($places['user'])) {
					$path = $config->get('main_image_path');
					$places['user'] = self::cleanPath($path);
				}

				// Get user id
				$parts = explode(':', $placeId);
				$userId = $parts[1];

				// Disallow user other than admin to open folders other his own
				// $my = JFactory::getUser();
				// if ($my->id != $userId && !EB::isSiteAdmin()) {
				//     $userId = $my->id;
				// }

				// Build path
				$places[$placeId] = $places['user'] . '/' . $userId;
			}
		}

		$isRootFolder = $placeId == $uri;

		$path = $root . '/' . @$places[$placeId];

		if (!$isRootFolder) {
			$path .= '/' . substr($uri, strpos($uri, '/') + 1);
		}


		return $path;
	}

	/**
	 * Converts a URI to a URL
	 *
	 * Example:
	 * user:605/foo/bar.jpg => http://site.com/images/easyblog_images/605/foo/bar.jpg
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function getUrl($uri, $relative = false)
	{
		static $root;

		if (!isset($root)) {
			$rootUri = rtrim(JURI::root(), '/');

			$root = preg_replace("(^https?://)", "//", $rootUri);
		}

		$url = self::getPath($uri, $root);

		if ($relative) {
			$url = EB::string()->abs2rel($url);
		}

		return $url;
	}

	/**
	 * Converts a URI format to KEY format
	 *
	 * Example:
	 * article:3/bar.jpg => _12313asdasd123123
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getKey($uri)
	{

		// If key given, just return key.
		return substr($uri, 0, 1)=='_' ? $uri :
			// Else convert key to uri by
			// adding signature underscore,
			// replacing url unsafe characters,
			// and encoding to base64.
			 '_' . strtr(base64_encode($uri), '+=/', '.-~');
	}

	/**
	 * Given a unique key, convert it to the uri format
	 *
	 * Example:
	 * _12313123asdasd123123 => article:3/bar.jpg
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getUri($key)
	{
		// If uri is given, just return uri.
		if (substr($key, 0, 1) !== '_') {
			return $key;
		}

		// Else convert uri to key by removing signature underscore,
		// reversing unsafe characters replacement, and decoding from base64.
		$uri = base64_decode(strtr(substr($key, 1), '.-~', '+=/'));

		return $uri;
	}

	public static function getHash($key)
	{
		// Returns a one-way unique identifier that is alphanumeric
		// so it can used in strict places like the id of an element.
		return md5(self::getKey($key));
	}

	/**
	 * Sanitizes a given path
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function cleanPath($path)
	{
		return trim(str_ireplace(array('/', '\\'), '/', $path), '/');
	}

	/**
	 * Renames a file or a folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function rename($source, $target)
	{
		$targetPath = EBMM::getPath($target);
		$targetName = basename($targetPath);

		$targetName = $this->normalizeFolderName($targetName);

		$target = dirname($source) . '/' . $targetName;

		$adapter = $this->getAdapter($source);
		$state = $adapter->rename($source, $target);

		// Throw error
		if (!$state) {
			return false;
		}

		// Get a new adapter for the new target
		$item = $this->getInfo($target, true);

		return $item;
	}

	/**
	 * Renders the html structure for media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function render()
	{
		// Get a list of places
		$places = self::getPlaces();
		$uploadUrl = JURI::base();

		if ($this->config->get('ajax_use_index')) {
			$uploadUrl .= 'index.php';
		}

		$sessionId = JFactory::getSession()->getId();
		$uploadUrl .= '?option=com_easyblog&task=media.upload&tmpl=component&lang=en&&sessionid=' . $sessionId . '&' . EB::getToken() . '=1';

		$theme = EB::themes();
		$theme->set('places', $places);
		$theme->set('uploadUrl', $uploadUrl);

		$output = $theme->output('site/composer/media/default');

		return $output;
	}

	/**
	 * Renders the breadcrumbs for each place / folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderBreadcrumb($meta)
	{
		$theme = EB::themes();
		$theme->set('meta', $meta);
		$output = $theme->output('site/composer/media/breadcrumbs/item');

		return $output;
	}

	/**
	 * Renders a list of articles for media manager
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function renderPosts()
	{
		$posts = array();

		$model = EB::model('Dashboard');
		$userId = EB::user()->id;

		// If the user is an admin, list down all blog posts created on the site
		if (EB::isSiteAdmin()) {
			$userId = null;
		}

		$posts = $model->getEntries($userId, array('state' => EASYBLOG_POST_PUBLISHED));

		$template = EB::template();
		$template->set('posts', $posts);

		$html = $template->output('site/mediamanager/posts');

		return $html;
	}

	/**
	 * Renders a list of users in media manager
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function renderUsers()
	{
		// Get a list of authors from the site
		$model = EB::model('Blogger');
		$app = JFactory::getApplication();
		$page = $app->input->get('page', 0, 'int');

		// Default to limit 20 items per page.
		$limit = 20;
		$limitstart = $page * $limit;

		$result = $model->getSiteAuthors($limit, $limitstart);
		$pagination = $model->getPagination();

		// Map them with the profile table
		$authors = array();

		if ($result) {

			//preload users
			$ids = array();
			foreach ($result as $row) {
				$ids[] = $row->id;
			}
			EB::user($ids);

			foreach ($result as $row) {
				$author = EB::user($row->id);
				$authors[] = $author;
			}
		}

		if (!isset($pagination->pagesCurrent)) {
			$currentPage = 'pages.current';
			$totalPage = 'pages.total';

			$pagination->pagesCurrent = $pagination->$currentPage;
			$pagination->pagesTotal = $pagination->$totalPage;
		}

		$template = EB::template();
		$template->set('authors', $authors);
		$template->set('pagination', $pagination);

		$html = $template->output('site/mediamanager/users');

		return $html;
	}

	/**
	 * Method to normalize the folder name
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function normalizeFolderName($name)
	{
		// Make sure the folder name do not have space
		$name = str_replace(' ', '_', $name);

		// and also hashtag character. #1645
		$name = str_replace('#', '_', $name);

		return $name;
	}

	/**
	 * Normalizes a path
	 *
	 * @since	5.0
	 * @access	public
	 */
	public static function normalizeFileName($name)
	{
		// Fix file names containing "/" in the file title
		if (strpos($name, '/') !== false) {
			$name = substr($name, strrpos($name, '/') + 1);
		}

		// Fix file names containing "\" in the file title
		if (strpos($name, '\\') !== false) {
			$name = substr($name, strrpos($name, '\\') + 1);
		}

		// Ensure that the file name is safe
		$name = JFile::makesafe($name);

		$name = trim($name);

		// Remove the extension
		$name = substr($name, 0, -4) . '.' . JFile::getExt($name);

		// Ensure that the file name contains an extension
		if (strpos($name, '.') === false) {
			$name = EB::date()->format('Ymd-Hms') . '.' . $name;
		}

		// Do not allow spaces in the name
		$name = str_ireplace(' ', '-', $name);

		return $name;
	}

	/**
	 * Retrieves information about a file or folder
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getFile($uri)
	{
		static $items = array();

		$file = $uri;

		if (!is_object($uri)) {

			// Get the place based on the uri
			$place = self::getPlace($uri);
			$type = self::getSourceType($place->id);

			// Get the file information
			$adapter = $this->getAdapter($uri);
			$file = $adapter->getItem($uri);
		}

		return $file;
	}

	/**
	 * Renders the html codes for file items
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderFile($file)
	{
		$params = $file->getParams();
		$ext = $params->get('extension', '');
		$preview = $file->getPreview();

		$theme = EB::themes();
		$theme->set('item', $file);
		$theme->set('params', $params);
		$theme->set('ext', $ext);
		$theme->set('preview', $preview);

		$html = $theme->output('site/composer/media/file');
		return $html;
	}

	/**
	 * Renders the html output for a particular media item as it may be used
	 * in legacy editors
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderInfo($file)
	{
		$preview = $file->getPreview();
		$variations = $file->getVariations();

		$theme = EB::themes();
		$theme->set('file', $file);
		$theme->set('variations', $variations);
		$theme->set('preview', $preview);

		$html = $theme->output('site/composer/media/info');

		return $html;
	}

	/**
	 * Renders the preview for media manager
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function renderPanel($file, $currentPostId = false)
	{
		$params = $file->getParams();
		$variations = $file->getVariations();
		$preview = $file->getPreview();

		// Default preferred variation
		$preferredVariation = null;

		// Detect preferred variation to use
		if ($file->type == 'image') {
			// Preferred variations are store in params
			$variationName = $params->get('variation', '');

			if ($variationName) {
				$preferredVariation = $variations[$variationName];
			} else {
				$preferredVariations = array('system/large', 'system/original');

				if ($this->config->get('main_media_variation') == 'system/original') {
					$preferredVariations = array('system/original', 'system/large');
				}

				foreach ($preferredVariations as $variationName) {
					if (isset($variations[$variationName])) {
						$preferredVariation = $variations[$variationName];
						break;
					}
				}
			}
		}

		$isLegacyPost = false;

		if ($currentPostId && self::isPostPlace($currentPostId)) {
			// Get the post id
			$id = explode(':', $currentPostId);
			$post = EB::post($id[1]);

			$isLegacyPost = $post->isLegacy() ? true : false;
		}

		$theme = EB::themes();
		$theme->set('file', $file);
		$theme->set('variations', $variations);
		$theme->set('params', $params);
		$theme->set('preview', $preview);
		$theme->set('preferredVariation', $preferredVariation);
		$theme->set('isLegacyPost', $isLegacyPost);

		$html = $theme->output('site/composer/media/panels/' . $file->type);

		return $html;
	}

	/**
	 * Renders a list of known variations for a set of images
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function renderVariations($file)
	{
		// Return empty html for non-image file
		if ($file->type != 'image') {
			return array();
		}

		$variations = $file->getVariations();

		$theme = EB::themes();
		$theme->set('file', $file);
		$theme->set('variations', $variations);

		$html = $theme->output('site/composer/media/info/variations');

		return $html;
	}

	/**
	 * Retrieves the contents of a given place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getContents($key)
	{
		$uri = $this->getUri($key);

		// Ensure that the user has access to this place
		$placeId = $this->getPlaceId($uri);

		if (!$this->hasAccess($placeId)) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		// Generate an adapter for the current uri
		$adapter = $this->getAdapter($uri);
		$folder = $adapter->getItems($uri);
		$meta = $adapter->getItem($uri);

		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = $meta;
		$media->meta->items = $folder->contents;
		$media->variations = array();
		$media->objects = array();
		$media->contents = $adapter->renderFolderContents($folder);
		$media->breadcrumb = $this->renderBreadcrumb($meta);
		$media->root = isset($folder->root) ? $folder->root : false;

		$media->login = $adapter->hasLogin();

		return $media;
	}

	/**
	 * Retrieves contents of a particular file
	 *
	 * @since	5.1
	 * @access	public
	 */
	public function getInfo($key, $fileTemplate = false, $currentPostId = false)
	{
		$uri = $this->getUri($key);

		// Ensure that the user has access to this place
		$placeId = $this->getPlaceId($uri);

		if (!$this->hasAccess($placeId)) {
			return EB::exception('COM_EASYBLOG_MM_NO_PERMISSIONS', EASYBLOG_MSG_ERROR);
		}

		$useRelative = $this->config->get('main_media_relative_path', true) ? true : false;

		// Check if the item is already in the #__easyblog_media table.
		// If it doesn't exist, we need to create it automatically
		$item = $this->getMediaObject($uri, $useRelative);
		$item->variations = $item->getVariations();

		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = $item;
		$media->variations = array();
		$media->objects = array();
		$media->info = null;
		$media->panel = $this->renderPanel($item, $currentPostId);

		// Get the file size if it exists
		$params = $item->getParams();
		$item->size = $params->get('size');

		// Get the size of the file if we are unable to locate it
		if (!$item->size) {
			$path = $this->getPath($media->uri);

			$item->size = @filesize($path);
		}


		// We don't really need these variations data if it is a folder
		if ($item->type != 'folder') {
			$media->variations = $this->renderVariations($item);
			$media->info = $this->renderInfo($item);
		}

		if ($fileTemplate) {
			$media->file = $this->renderFile($item);
		}

		return $media;
	}

	/**
	 * Retrieves the media item
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function getMedia($uri)
	{
		$media = new stdClass();
		$media->uri = $uri;
		$media->meta = self::getFile($uri);
		$media->info = '';
		$media->variations = array();

		if ($media->meta->type == 'folder') {
			$media->folder = EBMM::renderFolder($uri);
		} else {
			$media->variations = EBMM::renderVariations($uri);
			$media->info = EBMM::renderInfo($uri);
		}

		return $media;
	}

	// TODO: Move this to a proper Math library
	public static function formatSize($size)
	{
		$units = array(' B', ' KB', ' MB', ' GB', ' TB');
		for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return round($size, 2).$units[$i];
	}

	/**
	 * Determines if the given place id is a shared folder place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isSharedPlace($placeId)
	{
		if ($placeId == 'shared') {
			return true;
		}

		// Match for shared place
		if (preg_match('/shared/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is an album place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isAlbumPlace($placeId)
	{
		if ($placeId == 'easysocial' || $placeId == 'jomsocial') {
			return true;
		}

		if (preg_match('/easysocial/i', $placeId) || preg_match('/jomsocial/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is flickr
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isFlickrPlace($placeId)
	{
		if ($placeId == 'flickr' || $placeId == 'flickr') {
			return true;
		}

		if (preg_match('/flickr/i', $placeId)) {
			return true;
		}

		return false;
	}

	/**
	 * Determines if the given place id is an album place
	 *
	 * @since	5.1
	 * @access	public
	 */
	public static function isMoveablePlace($placeId)
	{
		if ($placeId == 'easysocial' || $placeId == 'jomsocial' || $placeId == 'flickr') {
			return false;
		}

		return true;
	}

	public static function isExternalPlace($placeId)
	{
		return !self::isMoveablePlace($placeId);
	}

	/**
	 * Determines if this is a post place
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isPostPlace($placeId)
	{
		return preg_match('/^post\:/i', $placeId);
	}

	/**
	 * Determines if this place is a user's media
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function isUserPlace($placeId)
	{
		return preg_match('/^user\:/i', $placeId);
	}
}
