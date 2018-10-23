<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ThemesHelperString extends ThemesHelperAbstract
{
	public function escape($string)
	{
		return ES::string()->escape($string);
	}

	/**
	 * Generates the "with xxx, yyy and zzz" html codes
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function with($users)
	{
		$count = count($users);

		$theme = ES::themes();
		$theme->set('users', $users);
		$theme->set('count', $count);
		$output = $theme->output('site/helpers/string/with');

		return $output;
	}

	/**
	 * Formats a given date string with a given date format
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function date( $timestamp , $format = '' , $withOffset = true )
	{
		// Get the current date object based on the timestamp provided.
		$date 	= FD::date( $timestamp , $withOffset );

		// If format is not provided, we should use DATE_FORMAT_LC2 by default.
		$format	= empty( $format ) ? 'DATE_FORMAT_LC2' : $format;

		// Get the proper format.
		$format	= JText::_( $format );

		$dateString 	= $date->toFormat( $format );

		return $date->toFormat( $format );
	}

	/**
	 * Pluralize the string if necessary.
	 *
	 * @since	1.2
	 * @access	public
	 */
	public function pluralize( $languageKey , $count )
	{
		return FD::string()->computeNoun( $languageKey , $count );
	}

	/**
	 * Alternative to @truncater to truncate contents with HTML codes
	 *
	 * @since	1.3
	 * @access	public
	 */
	public function truncate($text, $max = 250, $ending = '', $exact = false, $showMore = true, $overrideReadmore = false, $stripTags = false)
	{
		if (!$ending) {
			$ending = JText::_('COM_EASYSOCIAL_ELLIPSES');
		}

		// If the plain text is shorter than the maximum length, return the whole text
		if ((JString::strlen(preg_replace('/<.*?>/', '', $text)) <= $max) || !$max) {
			return $text;
		}

		// Truncate the string natively without retaining the original format.
		if ($stripTags) {
			$truncate = trim(strip_tags($text));
			$truncate = JString::substr($truncate, 0, $max) . $ending;
		} else {

			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

			$total_length = JString::strlen($ending);
			$open_tags = array();
			$truncate = '';

			foreach ($lines as $line_matchings) {

				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {

					// if it's an "empty element" with or without xhtml-conform closing slash
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
						// do nothing
					// if tag is a closing tag
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {

						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);

						if ($pos !== false) {
							unset($open_tags[$pos]);
						}

					// if tag is an opening tag
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {

						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, JString::strtolower($tag_matchings[1]));
					}

					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}

				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = JString::strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));

				if ($total_length + $content_length > $max) {

					// the number of characters which are left
					$left = $max - $total_length;
					$entities_length = 0;

					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {

						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entities_length <= $left) {
								$left--;
								$entities_length += JString::strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= JString::substr($line_matchings[2], 0, $left + $entities_length);
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}

				// if the maximum length is reached, get off the loop
				if ($total_length >= $max) {
					break;
				}
			}

			// If the words shouldn't be cut in the middle...
			if (!$exact) {

				// ...search the last occurance of a space...
				$spacepos = JString::strrpos($truncate, ' ');
				
				// ...and cut the text in this position
				if (isset($spacepos)) {

					// lets further test if the about truncate string has a html tag or not.
					$remainingString = JString::substr($truncate, $spacepos + 1);
					$remainingString = trim($remainingString);

					// check if string contain any html closing/opening tag before we proceed. #463
					$closingTagV1 = JString::strpos($remainingString, '>');
					$closingTagV2 = JString::strpos($remainingString, '/>');

					// Everything is safe. Let's truncate it.
					if ((!$closingTagV1 && !$closingTagV2) || ($closingTagV1 === 0 && $closingTagV2 === 0)) {
						$truncate = JString::substr($truncate, 0, $spacepos);
					}
				}
			}

			// add the defined ending to the text
			$truncate .= $ending;

			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}		
		}

		$theme = ES::themes();
		$theme->set('truncated', $truncate);
		$theme->set('original', $text);
		$theme->set('showMore', $showMore);
		$theme->set('overrideReadmore', $overrideReadmore);

		$output = $theme->output('site/helpers/string/truncate');

		return $output;
	}

	/**
	 * Truncates a string at a centrain length and add a more link
	 *
	 * @deprecated	2.0
	 * @access	public
	 */
	public function truncater($text, $max)
	{
		return $this->truncate($text, $max, '');
	}
}
