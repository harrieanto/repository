<?php
namespace Repository\Component\View\Compiler;

/**
 * Compiler.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Compiler
{
	/** The equal with sign **/
	const EQUAL_WITH = "=";

	/** The result dumping keyword **/
	const DUMP = 'dump';

	/** The lower case keyword **/
	const LOWER = 'lower';

	/** The upper case keyword **/
	const UPPER = 'upper';

	/** The entities case keyword **/
	const ENTITIES = 'entities';

	/** The unechoable case keyword **/
	const UNECHOABLE = '.';
	
	/** The echo case keyword**/	
	const ECHO = 'echo'. Compiler::WHITESPACE;

	/** The whitespace **/
	const WHITESPACE = ' ';

	/** The initial number helper **/
	const INITIAL = 1;

	/** The php open tag **/
	const PHP_OPEN = "<?php". Compiler::WHITESPACE;

	/** The php close tag **/
	const PHP_CLOSE = Compiler::WHITESPACE. "?>";

	/** The template engine extension **/
	const EXTENSION = '.rtengine.php';
	
	/** The expired time **/
	const EXPIRED_TIME = 3600;

	/** Minimum expired time **/
	const MINIMUM_EXPIRED_TIME 	= 25;

	/** Curly braces group **/
	const CURLY = "@curly";

	/** Component group **/
	const COMPONENT = "@component";

	/** Extend layout group **/
	const EXTEND = "@extend";

	/** Section group **/
	const SECTION = "@section";

	/** Generator group **/
	const GENERATOR = "@yield";

	/** Default directory pointing **/
	const DIR_POINTING = 4;

	/** Whitespace length **/
	const WHITESPACE_LENGTH = "    ";

	/** Block if group **/
	const IF = "@if";

	/** Block loop group **/
	const LOOP = "@loop";

	/** Block foreach group **/
	const FOREACH = '@foreach';

	/** Allowed operator type **/
	const OPERATOR_TYPE = "\$\d\w\-<\>\=\!\|\/\(\)\^\[\]\"\'\.\,\s\+\:\;\?\&";
	
	/**
	 * Compiled path file
	 * 
	 * @param string $compiledPath
	 */
	private $compiledPath;

	/**
	 * Set compiled path file
	 * 
	 * @param string $path
	 */
	public function setCompiledPath($path)
	{
		$this->compiledPath = $path;
	}

	/**
	 * Get compiled path file
	 * 
	 * @return string
	 */	
	public function getCompiledPath()
	{
		return $this->compiledPath;
	}
}