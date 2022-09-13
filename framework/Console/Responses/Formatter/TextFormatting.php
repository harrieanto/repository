<?php
namespace Repository\Component\Console\Responses\Formatter;

use InvalidArgumentException;
use Repository\Component\Collection\Collection;

/**
 * Table Text Formatting.
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class TextFormatting
{
	/** @var int The default terminal screen length **/
	const DEFAULT_SCREEN_LENGTH = 100;

	/**
	 * The default charset used to count each string
	 * Default is `UTF-8`
	 * @var string $charset
	 */
	public $charset = TextColumnTypes::CHARSET;

	/*
	 * Get text column in center alignment
	 * 
	 * @param string|int $field
	 * @param string|int $data
	 * @param array $initialData
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */	
	public function getTextColumnInCenterFormat($field, $data, array $initialData)
	{
		$collection = Collection::make(array());
		$maxLengths = $this->parseColumnsMaxLength($initialData);
		$length = $this->length($data);

		$maxLength = $maxLengths[$field];
		$padding = $maxLength - $length;
		$padding = $padding / 2;
		$padding = is_float($padding) ? ceil($padding) : $padding;
		$leftPad = $rightPad = $padding;
			
		if ($leftPad+$rightPad+$length > $maxLength) {
			$rightPad = $rightPad-1;
		}
		
		$column = $collection;
		$leftPad = $this->setPadding($leftPad);
		$rightPad = $this->setPadding($rightPad);
		$column->push($leftPad . $data . $rightPad);
		$column = $column->all();
		$collection->flush();
		$collection->add($field, $column);
		
		return $collection;
	}

	/*
	 * Get text column in left alignment
	 * 
	 * @param string|int $field
	 * @param string|int $data
	 * @param array $initialData
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */	
	public function getTextColumnInLeftFormat($field, $data, array $initialData)
	{
		$collection = Collection::make(array());
		$maxLengths = $this->parseColumnsMaxLength($initialData);
		$length = $this->length($data);

		$maxLength = $maxLengths[$field];
		$padding = $maxLength - $length;
		
		$column = $collection;
		$column->push($data . $this->setPadding($padding));
		$column = $column->all();
		$collection->flush();
		$collection->add($field, $column);
		
		return $collection;
	}

	/*
	 * Get text column in right alignment
	 * 
	 * @param string|int $field
	 * @param string|int $data
	 * @param array $initialData
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */	
	public function getTextColumnInRightFormat($field, $data, array $initialData)
	{
		$collection = Collection::make(array());
		$maxLengths = $this->parseColumnsMaxLength($initialData);
		$length = $this->length($data);

		$maxLengths = $maxLength[$field];
		$padding = $maxLength - $length;
		
		$column = $collection;
		$column->push($this->setPadding($padding) . $data);
		$column = $column->all();
		$collection->flush();
		$collection->add($field, $column);
		
		return $collection;
	}

	/*
	 * Parse paired key and value into two side column.
	 * Example. Firstname .... Lastname
	 * 
	 * @param string $key
	 * @param string $value
	 * 
	 * @return string
	 */
	public function parseTwoSideColumn($key, $value)
	{
		$init = $key . $this->setPadding(1) . $value;
		$length = $this->length($init);
		$spaceLength = mb_strpos($init, $this->setPadding(1));
		$screenLength = intval(`tput cols`) / 2;
		
		if ($screenLength < 1) $screenLength = self::DEFAULT_SCREEN_LENGTH / 2;
		
		if ($spaceLength <= $screenLength) {
			$screenLength = $screenLength - $spaceLength;
			$spaceLength = str_repeat($this->setPadding(1), $screenLength);

			return $key . $spaceLength . $value;
		}
	}

	/*
	 * Get maximal text length of each columns
	 * 
	 * @param string|int $columnName
	 * 
	 * @return string|array
	 */
	public function getColumnMaxLength($columnName)
	{
		return $this->parseColumnsMaxLength($this->data)[$columnName];
	}

	/*
	 * Resolve maximal text length of each columns of the given data
	 * 
	 * @param array $data
	 * 
	 * @return array
	 */	
	public function parseColumnsMaxLength(array $data): array
	{
		$lengths = array();

		foreach ($data as $field => $rows) {
			$rows = (array) $rows;

			$lengths[$field] =  array_reduce($rows, function ($initial, $input) {
				return max($initial, $this->length($input));
   			}, $this->length($field));
		}
		
		return $lengths;
	}

	/*
	 * Get length of the given text
	 * 
	 * @param string $text
	 * 
	 * @return int
	 */	
	public function length(string $text)
	{
		return mb_strlen($text, $this->charset);
	}

	/*
	 * Set default charset used for count the length of text
	 * 
	 * @param string $charset
	 * 
	 * @throw InvalidArgumentException
	 * 
	 * @return void
	 */	
	public function setCharset(string $charset): void
	{
		if (!in_array($text, mb_list_encodings())) {
			$ex = "The given charset [$charset] is not supported by mb_list_encodings(). ";
			$ex.= "Please see the php documentation";

			throw new InvalidArgumentException($ex);
		}
		
		$this->charset = $charset;
	}

	/*
	 * Get default charset used for count the length of text
	 * 
	 * @return string
	 */
	public function getCharset()
	{
		return $this->charset;
	}

	/*
	 * Set padding by the given length
	 * 
	 * @param int $length
	 * 
	 * @return string
	 */	
	public function setPadding(int $length)
	{
		return str_repeat(' ', $length);
	}
}