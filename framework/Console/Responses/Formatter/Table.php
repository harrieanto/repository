<?php
namespace Repository\Component\Console\Responses\Formatter;

use InvalidArgumentException;
use Repository\Component\Collection\Collection;
use Repository\Component\Contracts\Console\ResponseInterface;
use Repository\Component\Contracts\Console\TableFormatterInterface;

/**
 * Table Formatter.
 * Parse Array data into table
 *
 * @package	  \Repository\Component\Console
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Table implements TableFormatterInterface
{
	/**
	 * The array data for parse to the table format
	 * @var array $data
	 */
	private $data = array();

	/**
	 * The console response interface
	 * @var \Repository\Component\Contracts\Console\ResponseInterface $response
	 */
	private $response;

	/**
	 * The text formatter instance
	 * @var \Repository\Component\Console\Responses\Formatter\TextFormatter $formatter
	 */	
	private $formatter;

	/**
	 * Table columns separator
	 * @var array $separators
	 */
	private $separators = array(
		'line' => '-', 
		'corner' => '+', 
		'left' => '|', 
		'right' => '|', 
	);

	/**
	 * The text position of field/header table
	 * @var string $textFieldPosition
	 */
	private $textFieldPosition = TextColumnTypes::CENTER;

	/**
	 * The text position of row/column/body table
	 * @var string $textColumnPosition
	 */
	private $textColumnPosition = TextColumnTypes::LEFT;
	
	/**
	 * @param array $data The array data for parse to the table format
	 * @param \Repository\Component\Contracts\Console\ResponseInterface $response
	 */
	public function __construct(array $data, ResponseInterface $response)
	{
		$this->data = $data;
		$this->response = $response;
		$this->formatter = new TextFormatting();
	}

	/**
	 * Render parsed data as table to the actual resource (STDOUT)
	 * 
	 * @return void
	 */	
	public function renderTable(): void
	{
		$formatter = $this->response->getOutputFormatter();
		$field = $this->getParsedFieldColumn();
		$row = $this->getParsedRowColumn();

		$field = $formatter->setStringFormatting($field, function ($formatter) {
			$formatter->setBackgroundColor(Background::RED);
			$formatter->setTextColor(Foreground::LIGHT_GRAY);
		});

		$this->response->write($field);

		$row = $formatter->setStringFormatting($row, function ($formatter) {
			$formatter->setBackgroundColor(Background::LIGHT_GRAY);
			$formatter->setTextColor(Foreground::BLACK);
		});

		$this->response->writeln($row);
	}

	/**
	 * Get parsed field/header for the table
	 * 
	 * @return string
	 */
	public function getParsedFieldColumn(): string
	{
		$field = Collection::make(array());
		$initialPosition = $this->getTextColumnPosition();
		$this->setTextColumnPosition($this->getTextFieldPosition());

		foreach ($this->data as $fieldName => $rows) {
			$text = $this->getTextColumnOn($fieldName, ucwords($fieldName), $this->data);
			$field->push($text->get($fieldName));
		}
		
		$rowLine = '';
		$rowLine .= $this->appendSeparator();
		$rowLine .= $this->getFlattenString($field->all()).PHP_EOL;
		$rowLine .= $this->appendSeparator();
		
		$this->setTextColumnPosition($initialPosition);

		return $rowLine;
	}

	/**
	 * Get parsed row body for the table
	 * 
	 * @return string
	 */
	public function getParsedRowColumn(): string
	{
		$row = $this->parseRowColumns($this->data);
		$rowLine = $this->getFlattenString($row, $this->data);

		return $rowLine;
	}

	/**
	 * Append separator for the table
	 * 
	 * @param array $initialData The earlier data being parsed
	 * 
	 * @return string
	 */
	public function appendSeparator(array $initialData = array())
	{
		$this->prepareInitialData($initialData);

		$separator = '';
		$lineSeparator = $this->getSeparatorType('line');
		$initialSeparator = $this->getSeparatorType('corner');
		$initialSeparatorPad = str_repeat($initialSeparator, 2);
		
		$textLengths = $this->formatter->parseColumnsMaxLength($initialData);

		foreach ($textLengths as $field => $length) {
			$separator .= $initialSeparator . str_repeat($lineSeparator, $length) .$initialSeparator;
		}
		
		return str_replace($initialSeparatorPad, $initialSeparator, $separator).PHP_EOL;
	}

	/**
	 * Get parsed array data to the string format.
	 * So we can use it for creating table
	 * 
	 * @param array $columns The parsed datas
	 * @param array $initialData The initial data being parsed
	 * 
	 * @return string
	 */
	public function getFlattenString(array $columns, array $initialData = array())
	{
		$rowLine = '';
		$leftBorder = $this->getSeparatorType('left');
		$leftBorderPad = str_repeat($leftBorder, 2);
		$rightBorder = $this->getSeparatorType('right');
		$rightBorderPad = str_repeat($rightBorder, 2);

		$border = [$leftBorder, $rightBorder];
		$multiplied = [$leftBorderPad, $rightBorderPad];

		$this->prepareInitialData($initialData);

		foreach ($columns as $rows) {
			$rowLine .= $this->appendBorder(array_shift($rows));

			if (count($initialData) <= 1) {
				$rowLine = $rowLine.PHP_EOL;
				$rowLine .= $this->appendSeparator($initialData);
			}

			$rowLineEnd = array_pop($rows);

			if ($rowLineEnd !== null) {
				$separator = $this->getSeparatorType('left');
				$rowLine .= $this->appendBorder(implode($separator, $rows));
				$rowLine .= $this->appendBorder($rowLineEnd).PHP_EOL;
				$rowLine .= $this->appendSeparator($initialData);
			}
		}

		return str_replace($multiplied, $border, $rowLine);
	}

	/**
	 * Parse raw data to the row body by the given data
	 * 
	 * @param array $data The initial data being parsed
	 * 
	 * @return array Parsed row body
	 */	
	public function parseRowColumns(array $data)
	{
		$no = 0;
		$parsedColumns = array();
		$columns = $this->parseTextColumns($data);

		foreach ($data as $field => $rows) {
			$rows = (array) $rows;

			foreach ($rows as $index => $row) {
				for ($i = 0; $i < count($rows); $i++) {
					if ($index === $i) {
						if (isset($columns[$field][$i])) {
							$text = array_shift($columns[$field][$i]);
							$parsedColumns[$i][$no] = $text;
						}
					}
				}
			}

			$no++;
		}

		foreach ($parsedColumns as $index => $rows) {
			if (count($rows) < count($data)) {
				$no = 0;
				
				foreach ($data as $field => $raws) {
					if (isset($rows[$no])) {
						$parsedColumns[$index][$no] = $rows[$no];
					} else {
						$this->setTextColumnPosition(TextColumnTypes::CENTER);
						$text = $this->getTextColumnOn($field, TextColumnTypes::EMPTY_ENTRY, $data);
						$text = $text->get($field);
						$parsedColumns[$index][$no] = array_shift($text);
					}

					$no++;
					ksort($parsedColumns[$index]);
				}
			}
		}

		return $parsedColumns;
	}

	/**
	 * Parse text on each column of the given array data
	 * 
	 * @param array $data The data being parsed
	 * 
	 * @return array
	 */	
	public function parseTextColumns(array $data)
	{
		$columns = array();

		foreach ($data as $field => $rows) {
			$rows = (array) $rows;
			foreach ($rows as $index => $row) {
				$text = $this->getTextColumnOn($field, $row, $data);
				$columns[$field][] = $text->get($field);
			}
		}
		
		return  $columns;
	}

	/**
	 * As the console couldn't perform data by newline/carriage return
	 * We need sanitize the data from it. So we can ordering table nicely
	 * 
	 * @param string $data
	 * 
	 * @return string
	 */	
	private function sanitizeNewline($data)
	{
		$data = preg_split('/[\r\n]/', $data, null, PREG_SPLIT_NO_EMPTY);
		$data = implode(' ', $data);
		
		return $data;
	}

	/**
	 * Here we can adjust, sanitize and append padding as long as their longest column length
	 * of the given row/body data by their own field
	 * 
	 * @param string|int $field
	 * @param string|int $data
	 * 
	 * @return array
	 */
	public function getTextColumnOn($field, $data, array $initialData)
	{
		$alignment = $this->formatter;
		$data = $this->sanitizeNewline($data);

		$maxLengths = $alignment->parseColumnsMaxLength($initialData);
		$length = $alignment->length($data);

		$maxLength = $maxLengths[$field];
		$padding = $maxLength - $length;
		$collection = Collection::make(array());

		if ($padding > 0) {
			switch ($position = $this->getTextColumnPosition()) {
				case TextColumnTypes::LEFT: 
					$collection = $alignment->getTextColumnInLeftFormat($field, $data, $initialData); 
					break;
				case TextColumnTypes::RIGHT: 
					$collection = $alignment->getTextColumnInRightFormat($field, $data, $initialData); 
					break;
				case TextColumnTypes::CENTER: 
					$collection = $alignment->getTextColumnInCenterFormat($field, $data, $initialData); 
					break;
			}
		} else {
			$column = $collection;
			$column->push($data);
			$column = $column->all();
			$collection->flush();
			$collection->add($field, $column);
		}
		
		return $collection;
	}

	/**
	 * Set text field/header position of the table
	 * 
	 * @param string $position
	 * 
	 * @throws InvalidArgumentException
	 * When the given position doesn't match one from the supplied positions
	 * 
	 * @return void
	 */
	public function setTextFieldPosition(string $position): void
	{
		if (!in_array($position, $positions = TextColumnTypes::POSITIONS)) {
			$ex = "Invalid the given text column position. Available is {$positions}";
			throw new InvalidArgumentException($ex);
		}

		$this->textFieldPosition = $position;
	}

	/**
	 * Set text position of the row/body table
	 * 
	 * @param string $position
	 * 
	 * @throws InvalidArgumentException
	 * When the given position doesn't match one from the supplied positions
	 * 
	 * @return void
	 */
	public function setTextColumnPosition(string $position)
	{
		if (!in_array($position, $positions = TextColumnTypes::POSITIONS)) {
			$ex = "Invalid the given text column position. Available is {$positions}";
			throw new InvalidArgumentException($ex);
		}

		$this->textColumnPosition = $position;
	}

	/**
	 * Get text field/header position of the table
	 * 
	 * @return string
	 */
	public function getTextFieldPosition()
	{
		return $this->textFieldPosition;
	}

	/**
	 * Get text position of the row/body table
	 * 
	 * @return string
	 */	
	public function getTextColumnPosition()
	{
		return $this->textColumnPosition;
	}

	/**
	 * Prepare initial data when we need different data
	 * When we leave the initial data argument to empty and then the earlier initial data will be used
	 * 
	 * @param array &$initialData
	 * 
	 * @return void
	 */	
	private function prepareInitialData(array &$initialData): void
	{
		if (empty($initialData)) $initialData = $this->data;
	}

	/**
	 * Append border for the given text column
	 * 
	 * @param string $column
	 * 
	 * @return string
	 */
	public function appendBorder(string $column): string
	{
		$left = $this->getSeparatorType('left');
		$right = $this->getSeparatorType('right');

		return $left . $column . $right;
	}

	/**
	 * Set custom separator for rendered table
	 * 
	 * @param string $type The type of the separator
	 * @param string $value The value for separator
	 * 
	 * @return void
	 */	
	public function setSeparatorType(string $type, string $value): void
	{
		$this->separators[$type] = $value;
	}

	/**
	 * Get table separator by the given type
	 * 
	 * @param string $type The type of the separator
	 * 
	 * @return string
	 */		
	public function getSeparatorType(string $type): string
	{
		return $this->separators[$type];
	}

	/**
	 * Get initial data for parsed as table
	 * 
	 * @return array
	 */
	public function getInitialData(): array
	{
		return $this->data;
	}
}