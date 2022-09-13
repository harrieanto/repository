<?php
namespace Repository\Component\Html;

use Repository\Component\Collection\Collection;

/**
 * Html Helpers.
 *
 * @package	  \Repository\Component\Html
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Html
{
	/**
	 * Generate HTML tag
	 *
	 * @param  string $type Html tag type
	 * @param  string $type Html tag content
	 * @param  array $type Html tag attribute
	 * 
	 * @return string Html tag
	 */
	public function tag($element, $content, $attributes = array(), bool $closeTag = true)
	{
		if (empty($attributes)) {
			return sprintf("<%s>%s</%s>", $element, $content, $element);
		}
		
		$attribute = '';

		foreach ($attributes as $type => $attr) {
			$attribute.= (is_int($type)) ?  $attr : "{$type} = \"{$attr}\" ";
		}
		
		$attribute = trim($attribute);
		
		$result = sprintf("<%s %s>%s</%s>", $element, $attribute, $content, $element);

		if (!$closeTag) {
			$result = sprintf("<%s %s/>%s", $element, $attribute, $content);
		}

		$attribute = '';
		
		return $result;
	}
	
	/**
	 * Repeat string or html tag
	 * 
	 * @param  string $html
	 * @param  int|integer $loop The number of times repaeat the tag
	 * 
	 * @return string
	 */
	public function repeat($html = '&nbsp;', int $loop = 1)
	{
		$repeat = str_repeat($html, $loop);
		
		return $repeat;
	}

	/**
	 * Convert an HTML string to entities.
	 *
	 * @param  string  $value
	 * 
	 * @return string
	 */
	public function entities($value)
	{
		$entities = htmlentities($value, ENT_QUOTES, 'UTF-8', false);
		
		return $entities;
	}

	/**
	 * Convert entities to HTML characters.
	 *
	 * @param  string  $value
	 * 
	 * @return string
	 */
	public function decode($value)
	{
		$html = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
		
		return $html;
	}

	/**
	 * Removes HTML tag from a string
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */
	public function strip($content)
	{
		$content = htmlspecialchars_decode(strip_tags($content));
		
		return $content;
	}

	/**
	 * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
	 *
	 * @param  string  $email
	 * 
	 * @return string
	 */
	public function email($email)
	{
		$email = str_replace('@', '&#64;', $this->obfuscate($email));
		
		return $email;
	}

	/**
	 * Obfuscate a string to prevent spam-bots from sniffing it.
	 * 
	 * Credit for FatFree component for these convenient way
	 *
	 * @param  string  $value
	 * 
	 * @return string
	 */
	public function obfuscate($value)
	{
		$safe = '';

		foreach (str_split($value) as $letter) {
			if (ord($letter) > 128) {
				return $letter;
			}

			// To properly obfuscate the value, we will randomly convert each letter to
			// its entity or hexadecimal representation, keeping a bot from sniffing
			// the randomly obfuscated letters out of the string on the responses.
			switch (rand(1, 3)) {
				case 1:
					$safe .= '&#'.ord($letter).';'; break;

				case 2:
					$safe .= '&#x'.dechex(ord($letter)).';'; break;

				case 3:
					$safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Cleans HTML to prevent most XSS attacks.
	 * 
	 * Credit for FatFree component for these convenient way
	 * 
	 * @param  string $html HTML
	 * 
	 * @return string Cleaned HTML
	 */
	public static function clean($html)
	{
		do {
			$oldHtml = $html;

			// Fix &entity\n;
			$html = str_replace(['&amp;','&lt;','&gt;'], ['&amp;amp;','&amp;lt;','&amp;gt;'], $html);
			$html = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $html);
			$html = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $html);
			$html = html_entity_decode($html, ENT_COMPAT, 'UTF-8');

			// Remove any attribute starting with "on" or xmlns
			$html = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>#iUu', "$1>", $html);

			// Remove javascript: and vbscript: protocols
			$html = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/|(&\#\d+;)]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $html);
			$html = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/|(&\#\d+;)]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $html);
			$html = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/|(&\#\d+;)]*-moz-binding[\x00-\x20]*:#Uu', '$1=$2nomozbinding...', $html);
			$html = preg_replace('#([a-z]*)[\x00-\x20\/]*=[\x00-\x20\/]*([\`\'\"]*)[\x00-\x20\/|(&\#\d+;)]*data[\x00-\x20]*:#Uu', '$1=$2nodata...', $html);

			// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
			$html = preg_replace('#(<[^>]+[\x00-\x20\"\'\/])style[^>]*>#iUu', "$1>", $html);

			// Remove namespaced elements (we do not need them)
			$html = preg_replace('#</*\w+:\w[^>]*>#i', "", $html);

			// Remove really unwanted tags
			$html = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $html);
		}
		while ($oldHtml !== $html);

		return $html;
	}
}