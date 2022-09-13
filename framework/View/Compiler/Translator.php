<?php
namespace Repository\Component\View\Compiler;

use Repository\Component\View\Compiler\Exception\TranslatorException;
use Repository\Component\Filesystem\Filesystem as Fs;
use Repository\Component\Collection\Collection;
use Psr\Http\Message\StreamInterface;

/**
 * Resolve Content Template Specfification.
 * 
 * @package	  \Repository\Component\View
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Translator extends Compiler
{
	/**
	 * Filesystem instance
	 * 
	 * @var \Psr\Message\StreamInterface $fs
	 */
	protected $fs;
	
	/**
	 * Maker instance
	 * 
	 * @var \Repository\Component\Compiler\Maker $maker
	 */	
	protected $maker;

	/**
	 * Conditional instance
	 * 
	 * @var \Repository\Component\Compiler\Conditional $conditinal
	 */	
	protected $conditional;

	/**
	 * Main template path file
	 * 
	 * @var string $mainFile
	 */	
	protected $mainFile;

	/**
	 * The layout target of the current main file
	 * 
	 * @var string $layoutTarget
	 */	
	protected $layoutTarget;

	/**
	 * Basepath layout target
	 * 
	 * @var string $basepath
	 */
	protected $basepath = 'tests';

	/**
	 * Filtered content list
	 * 
	 * @var array $items
	 */
	public $items = array();

	/**
	 * The littered content that want translate
	 * 
	 * @var string $content
	 */
	protected $content;

	/**
	 * Item identity in the specific filtered group items
	 * 
	 * @var int $identity
	 */
	protected $identity;

	/**
	 * Group name of the specific filtered item
	 * 
	 * @var string $groupName
	 */	
	protected $groupName;

	/**
	 * Grab the number of specific item
	 * depending on the given pattern type
	 * 
	 * @var string $pattern
	 */	
	protected $pattern;

	/**
	 * @param \Psr\Http\Message\StreamInterface $fs
	 */	
	public function __construct(StreamInterface $fs)
	{
		$this->fs = $fs;
		$this->maker = new Maker($this);
		$this->conditional = new Conditional($this, $this->maker);
	}

	/**
	 * Make content that want be translating
	 * 
	 * @param stream|string $content
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function make($content)
	{
		if ($this->fs->isFile($content)) {
			$this->mainFile = $content;
			$content = $this->fs->getContent($content);
		}

		$this->content = $content;

		return $this;
	}

	/**
	 * Determine if the given content contain a pipe `|` character
	 * 
	 * @param string $content
	 * 
	 * @return bool false When content has a pipe character
	 * Return the specifiic offset number when content has a pipe character
	 */	
	public function isContainsPipeCharacter($content)
	{
		return strpos($content, '|');
	}

	/**
	 * Resolve the given content into php container
	 * 
	 * @return string
	 */
	public function resolvePhpContainer($content)
	{
		return Compiler::PHP_OPEN. $content . Compiler::PHP_CLOSE;
	}

	/**
	 * Resolve specifications from both of master layout or extender layout
	 * 
	 * @return void
	 */	
	public function resolve()
	{
		try {
			$this->resolveAtExtend();
		} catch (\Exception $ex) {
			$this->resolveBuildedSpecification();
			$this->resolveSharedComponent();
			return;
		}

		$sections = $this->resolveAtSections();
		$resolved = $sections->getContentsBy(Compiler::SECTION);
		//Make sure the layout extender exist before compiling attempt
		$this->makeLayout(array());
		if($resolved !== null) $this->resolveLayout($resolved);
		$this->resolveSharedComponent();
	}

	/**
	 * Resolve specification in the layout target
	 * 
	 * @param array $sections
	 * 
	 * @return void
	 */	
	public function resolveLayout(array $sections)
	{
		//Build layout defined by layout target
		$this->makeLayout($sections);
		$this->resolveBuildedSpecification();
	}

	/**
	 * Resolve template specifications
	 * 
	 * @return void
	 */
	public function resolveBuildedSpecification()
	{
		//Resolve at curly braces specifications
		$this->resolveAtCurlyBraces();
		//Resolve if conditional statement
		$this->conditional->resolveIf();
		//Resolve looping statement
		$this->conditional->resolveLoop();
		//Resolve foreach statement
		$this->conditional->resolveForeach();
	}

	/**
	 * Build layout target from extended container specification
	 * 
	 * @throw \Repository\Component\View\Compiler\Exception\TransalatorException
	 * 
	 * @return void
	 */
	public function makeLayout(array $sections)
	{
		$this->resolveAtExtend();

		//Get layout target
		list($layout) = $this->getContentsBy(Compiler::EXTEND)['content'];

		//Remove whitespace and or slash character
		//from the layout target
		//So we have appropriate layout target
		$layout = trim(trim($layout, DIRECTORY_SEPARATOR), Compiler::WHITESPACE);

		$layout = $this->getBasepath().$layout.Compiler::EXTENSION;

		$this->setLayoutTarget($layout);
		
		//Check conditional state of the layout target
		if ($this->fs->exists($layout)) {

			$this->make($this->fs->getContent($layout));
			$this->resolveAtYieldGenerator();
			
			if (count($sections) > 0) {
			   $this->resolveGenerator($layout, $sections);
			}
			
		} else {
			throw new TranslatorException(
				"Layout [$layout] not found"
			);
		}
	}

	/**
	 * Resolve shared components
	 * 
	 * @return void
	 */	
	public function resolveSharedComponent()
	{
		$this->resolveAtComponent();
		$this->resolveBuildedSpecification();
	}

	/**
	 * Resolve specification in the component container
	 * 
	 * @return void
	 */	
	public function resolveAtComponent()
	{	
		//Set pattern
		$pattern = "/
			(@component\([\'\"])
			([\/\.\w\-\_\\s\.\|]+[^\'])
			([\'\"]\))/x";
		
		$this->setPattern($pattern);
		$this->resolveContentBy($pattern, Compiler::COMPONENT);

		if(isset($this->items[Compiler::COMPONENT])) {
			$componentPartials = array();
			$components = $this->getContentsBy(Compiler::COMPONENT);

			$open = $components['open'][0];
			$close = $components['close'][0];

			foreach($components['content'] as $component) {
				$componentBlock = $open.$component.$close;
				$component = str_replace(' ', '', $component);
				$component = trim($component, Fs::DS);
				$components = explode('|', $component);
				$componentLength = count($components);

				if($componentLength === 2) {
					$componentPartials['external'][] = $components;
					$componentPartials['external']['block'][$components[1]] = $componentBlock;
				} else {
					$componentPartials['internal'][] = $components;
					$componentPartials['internal']['block'][$components[0]] = $componentBlock;
				}
			}

			if (isset($componentPartials['internal'])) {
				$this->resolveInternalComponent($componentPartials['internal']);
			}

			if (isset($componentPartials['external'])) {
				$this->resolveExternalComponent($componentPartials['external']);
			}
		}
	}
	
	/**
	 * Resolve external component from the current layout file
	 * This is useful when you need another component of the another/different layout/template
	 *  
	 * @param array $componentPartials
	 */
	private function resolveExternalComponent($componentPartials)
	{
		$componentBlocks = $componentPartials['block'];
		unset($componentPartials['block']);

		foreach ($componentPartials as $partials) {
			$path = $partials[0];
			$target = $this->getBasepath().$path.Compiler::EXTENSION;
			$translator = new self($this->fs, $this->maker);
			$translator->setBasepath($this->getBasepath());
			$translator->make($target);

			//Throw an exception when user doesn't provide
			//template file
			if (null === $translator->getMainFile()) {
				throw new TranslatorException(
		  			"Template file [$target] not found"
		  		);
		   	}
		   	
		   	//Resolve layout specifications
		   	$translator->resolve();
		   	//Resolve template sections
		   	//Grab template sections from container
		   	$sections = $translator->getContentsBy(Compiler::SECTION);
		   	//Make sure the layout extender exist before compiling attempt
		   	$translator->makeLayout(array());
		   	//Resolve layout by the given template sections
		   	if ($sections !== null) $translator->resolveLayout($sections);
		   	
		   	$sectionBodies = $sections['body'];
		   	$componentResolved = Collection::make(array());
		   	
		   	foreach($componentBlocks as $name => $block) {
				if(!array_key_exists($name, $sectionBodies))
					throw new TranslatorException(
						"Component [$name] not found in [$target]"
					);
						
				$componentResolved->add($block, $sectionBodies[$name]);
			}
			
			foreach($componentResolved->all() as $block => $content) {
				$this->maker->make(array($block), array($content));
			}
		}
	}

	/**
	 * Resolve internal component from thir own layout file
	 * This is useful when you need to use any component over and over again
	 * in the same file
	 *  
	 * @param array $componentPartials
	 */
	private function resolveInternalComponent($componentPartials)
	{
		$componentBlocks = $componentPartials['block'];
		unset($componentPartials['block']);

		$target = $this->getMainFile();
		//Grab template sections from container
		$sections = $this->getContentsBy(Compiler::SECTION);
		
		$sectionBodies = (array) $sections['body'];
		$componentResolved = Collection::make(array());
		
		foreach ($componentBlocks as $name => $block) {
			if (!array_key_exists($name, $sectionBodies)) {
				throw new TranslatorException(
					"Component [$name] not found in [$target]"
				);
			}
			
			$componentResolved->add($block, $sectionBodies[$name]);
		}
		
		foreach ($componentResolved->all() as $block => $content) {
			$this->maker->make(array($block), array($content));
		}
	}

	/**
	 * Resolve specification in the curly braces container
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveAtCurlyBraces()
	{
		$operator = Compiler::OPERATOR_TYPE;
		$pattern = "/(\@{{)([$operator+]+)(}})/x";
		
		$this->setPattern($pattern);
		//Resolve content by the given regex pattern
		$this->resolveContentBy($pattern, Compiler::CURLY);
		//Resolve missing nesting
		$this->conditional->resolveMissingNesting(
			Compiler::CURLY, '{{', '}}', function() {
			$exception = "Block [". Compiler::CURLY . "] is missing";
			throw new TranslatorException($exception);
		});

		//We should do this
		//to prevent PHP warning in the second execution
		if (isset($this->items[Compiler::CURLY])) {
			$this->resolveContentFilteration(Compiler::CURLY);
		}
		
		$this->resolveCurlySpecifications();
		
		return $this;
	}

	/**
	 * Replace content at curly braces specification
	 * with the resolved content specification
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */		
	public function resolveCurlySpecifications()
	{
		if (preg_match_all($this->getPattern(), $this->getContent(), $matches)) {

			$content = str_replace($matches[0], $this
				->getFilteredContents(), $this
				->getContent()
			);
			
			$this->make($content);
		}
		
		return $this;
	}

	/**
	 * Resolve specification in the section container
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveAtSections()
	{	
		//Set pattern
		$pattern = "/
			(@section\([\'\"]?)
			([\.\w\-\_]+[^\'])([\'\"]?\))
			([\s\W\w\d\D]+?)
			(@stop)/x";
		
		$this->setPattern($pattern);
		$this->resolveContentBy($pattern, Compiler::SECTION);
		
		return $this;
	}

	/**
	 * Resolve specification in the yield generator container
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveAtYieldGenerator()
	{	
		//Set pattern
		$pattern = "/
			(@yield\(\')
			([\.\w\-\_]+[^\'])
			(\'\))/x";

		$this->setPattern($pattern);
		$this->resolveContentBy($pattern, Compiler::GENERATOR);
		
		return $this;
	}

	/**
	 * Resolve specification in the extend container
	 * 
	 * @throw \Repository\Component\View\Comiler\Exception\TranslatorException
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveAtExtend()
	{	
		//Set pattern
		$pattern = "/
			(@extend\([\'\"]?)
			(.+[^\'])
			([\'\"]?\))/x";
		
		$this->setPattern($pattern);
		$target = $this->getMainFile();
		$this->resolveContentBy($pattern, Compiler::EXTEND);

		//We should do this
		//to prevent PHP warning in the second execution
		if (isset($this->items[Compiler::EXTEND])) {
			$parameter = $this->items[Compiler::EXTEND]['content'];

			$number = count($parameter);

			if($number > Compiler::INITIAL)
				throw new \Exception("Only can have #1 extend [$target]");

			$this->setGroupName(Compiler::EXTEND);
		} else {
			throw new \Exception("Missing layout extender [$target]");
		}
	}

	/**
	 * Resolve whitespace length on the layout target specification
	 * 
	 * @param string $sections Layout target
	 * @param array $sections Section specification
	 * @param array $generators Generator specification
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function resolveLayoutWhitespace($layout, $pattern, $group)
	{
		$contents = $this->fs->fileToArray($layout);
		
		Collection::make($contents)->filter(function($layout) use ($pattern, $group) {
			if (preg_match_all($pattern, $layout, $matches)) {
				
				preg_match("/(.+$group)/", $layout, $matches);
				
				foreach ($matches as $index => $value) {
					$length = str_replace($group, '', $value);
					$this->items[$group]['whitespace'][$index] = strlen($length);
				}
			}
		});
	}

	/**
	 * Rearrange layout whitespace based on the content length
	 * 
	 * @param array $sections Section specification
	 * @param array $generators Generator specification
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function setUpLayoutWhitespace(array $sections, array $generators)
	{
		$collection = Collection::make(array());

		foreach ($sections['body'] as $index => $body) {
			foreach( $generators['whitespace'] as $key => $length) {
				
				$whitespace = str_repeat(Compiler::WHITESPACE_LENGTH, $length);

				if (preg_match_all('/\s+[\w\W\"\'\=\d\D\s]+/', $body, $matches)) {
					foreach ($matches as $whitespaces) {
					
						$container = '';

						foreach ($whitespaces as $content) {
							$content = str_replace("\n", '', $content);
							$container .= "\n" . $whitespace . $content;
							$collection->add($index, $container);
						}		
					}
				}
				
				$collection->add($index, $body);
			}
		}
		
		return $collection;
	}

	/**
	 * Resolve yield generator specifications in the layout target
	 * 
	 * @param string $layout Layout target
	 * @param array $sections
	 * 
	 * @throw \Repository\Component\View\Compiler\Exception\TranslatorException
	 *  
	 * @return void
	 */	
	public function resolveGenerator($layout, array $sections)
	{
		$this->resolveLayoutWhitespace($layout, $this->getPattern(), Compiler::GENERATOR);
		$generators = $this->getContentsBy(Compiler::GENERATOR);
		
		foreach ((array) $generators['content'] as $index => $generator) {
		
			if (Collection::make($sections['content'])->contains($generator)) {
				$collection = Collection::make(array());
			
				if (isset($generators['whitespace'])) {
					$contents = $this->setUpLayoutWhitespace(
						$sections, 
						$generators
					);

					foreach ($generators['matches'] as $index => $generator) {
						if (array_key_exists($index, $contents->all())) {
							$layouted = $collection->add($generator, $contents->get($index));
						}
					}
				} else {
					foreach ($generators['matches'] as $index => $generator) {
						if (array_key_exists($index, $sections['body'])) {
							$layouted = $collection->add($generator, $sections['body'][$index]);
						}
					}
				}

				foreach ($layouted->all() as $generator => $content) {
					$content = str_replace($generator, $content, $this->getContent());
					$this->make($content);
				}

			} else {
				$ex = "Generator [$generator] not found";
				throw new TranslatorException($ex);
			}
		}
	}

	/**
	 * Resolve specification by the given pattern and group name
	 * 
	 * @param string $pattern
	 * @param string $group
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveContentBy($pattern, $group)
	{
		if (preg_match_all($pattern, $this->content, $matches)) {
			$generators = Collection::make(array());
			
			if (Compiler::GENERATOR === $group) {
				foreach ($matches[0] as $index => $generator) {
					$generators->add($matches[2][$index], $generator);
				}
				
				$matches[0] = $generators->all();
			}

			$this->items[$group]['matches'] = $matches[0];
			$this->items[$group]['content'] = $matches[2];
			$this->items[$group]['open'] = array_unique($matches[1]);
			$this->items[$group]['close'] = array_unique($matches[3]);

			if (count($matches) > 4) {
				foreach ($matches[4] as $index => $body) {
					$matches[4][$index] = str_replace("\t", '', $body);
				}

				$bodies = Collection::make(array());
			
				if (Compiler::SECTION === $group) {
					foreach ($matches[2] as $index => $content) {
						$bodies->add($content, $matches[4][$index]);
					}
				
					$matches[4] = $bodies->all();
				}

  				$this->items[$group]['body'] = $matches[4];
  				$this->items[$group]['stop'] = $matches[5];
  			}
		}
		
		return $this;
	}

	/**
	 * Resolve specification in the specific group items
	 * 
	 * @param string $group
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function resolveContentFilteration($group)
	{
		foreach ($this->items[$group]['content'] as $index => $content) {
			
			//Set item identity
			$this->setIdentity($index);
			//Set group for each identity
			$this->setGroupName($group);
			
			if ($offset = $this->isContainsPipeCharacter($content)) {

				$contentLength = strlen($content);
				
				//Grab a keyword from specification				
				$keyword = substr($content, $offset+Compiler::INITIAL, $contentLength);
				$keyword = trim($keyword, Compiler::WHITESPACE);

				//Grab a the content from specification
				//This content can be string, general variable, array and object
				$content = substr($content, -$contentLength, $offset);
				$content = trim($content, Compiler::WHITESPACE);
				
				//Filter content by the given keyword specification
				$this->filterContentBy($keyword, $content);
			} else {
				//So far we don't have keyword specification
				//So that we can do is just populate original content
				$this->filterContentBy(null, $content);
			}
		}
		
		return $this;
	}

	/**
	 * Resolve non string specification
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */	
	public function resolveNonStringCase($content)
	{
		if ($this->isGetterVariable($content)) {
			if ($this->isSetterVariable($content)) {
				$content = Compiler::PHP_OPEN.$content.Compiler::PHP_CLOSE;
			} else {
				$content = Compiler::PHP_OPEN. "$content" .Compiler::PHP_CLOSE;
			}
		} else {
			if ($this->isFunction($content) || $this->isReversedLanguage($content)) {
				$content = $this->resolvePhpContainer($content);
			}
		}
		return $content;
	}

	/**
	 * The language reversed list
	 *  
	 * @return bool
	 */		
	public function languageReversedList()
	{
		$reversed = array(
			'foreach', 
			'endforeach;', 
			'if', 
			'endif;', 
			'for', 
			'endfor', 
			'array'
		);
		
		return $reversed;
	}

	/**
	 * Determine if the given item is formed as language reversed
	 *  
	 * @param string $item
	 * 
	 * @return bool
	 */	
	public function isReversedLanguage($item)
	{
		$reversed = Collection::make($this->languageReversedList());
		
		if ($reversed->contains($item)) return true;
		
		return false;
	}

	/**
	 * Determine if the given name is PHP function
	 * 
	 * @return bool
	 */
	public function isFunction($name)
	{
		if (preg_match("/^[\w\:]+\(.*?\)$/i", $name, $matches)) return true;
	}

	/**
	 * Resolve string specification
	 * 
	 * @param string $content
	 * @param string $callback Callback name to resolve specification
	 * 
	 * @return string
	 */		
	public function resolveStringCase($content, $callback)
	{
		if ($this->isGetterVariable($content)) {
			if ($this->isSetterVariable($content)) {
				$contents = $this->getSetterValueVariables($content);
				$content = Compiler::PHP_OPEN. 
					$contents[0]. 
					Compiler::EQUAL_WITH . 
					"$callback($contents[1])". 
					Compiler::PHP_CLOSE;	
			} else {
				$content = Compiler::PHP_OPEN. "$callback($content)" .Compiler::PHP_CLOSE;
			}
		} else {
			$content = $callback($content);
		}
		
		return $content;
	}

	/**
	 * Resolve default content specification
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */		
	private function resolveDefaultCase($content)
	{
		$content = trim($content, Compiler::WHITESPACE);

		if ($this->isGetterVariable($content) || $this->isFunction($content)) {
			$content = Compiler::PHP_OPEN.
				Compiler::ECHO. 
				Compiler::WHITESPACE.
				"$content".
				Compiler::PHP_CLOSE;
		}

		return $content;
	}

	/**
	 * Resolve lower case specification
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */		
	public function resolveLowerCase($content)
	{
		$content = $this->resolveStringCase($content, "strtolower");
		
		return $content;
	}

	/**
	 * Resolve upper case specification
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */		
	public function resolveUpperCase($content)
	{
		$content = $this->resolveStringCase($content, "strtoupper");
		
		return $content;
	}

	/**
	 * Resolve html entities specification
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */		
	public function resolveEntityCase($content)
	{
		$content = $this->resolveStringCase($content, "htmlentities");
		
		return $content;
	}

	/**
	 * Resolve result dumping specification
	 * 
	 * @param string $content
	 * 
	 * @return string
	 */			
	public function resolveDumpCase($content)
	{
		if($this->isGetterVariable($content)) {
			if($this->isSetterVariable($content)) {
				throw new \Exception(
					"Can't dumping variable as referenced"
				);
			}
			
			$content = Compiler::PHP_OPEN. 
				"var_dump($content)".  
				Compiler::PHP_CLOSE;
			
			return $content;
		}
	}

	/**
	 * Filter content by the given keyword specification
	 * 
	 * @param string $keyword
	 * @param string $content
	 * 
	 * @return void
	 */		
	public function filterContentBy($keyword, $content)
	{
		switch ($keyword) {
			case Compiler::LOWER:
				$content = $this->resolveLowerCase($content);
			break;
			case Compiler::UPPER:
				$content = $this->resolveUpperCase($content);
			break;
			case Compiler::ENTITIES:
				$content = $this->resolveEntityCase($content);
			break;
			case Compiler::UNECHOABLE:
				$content = $this->resolveNonStringCase($content);
			break;
			case Compiler::DUMP:
				$content = $this->resolveDumpCase($content);
			break;
			default:
				$content = $this->resolveDefaultCase($content);
			break;
		}

		$this->setFilteredContent($content);
	}

	/**
	 * Determine if the given content is a variable type
	 * 
	 * @param string $content
	 * 
	 * @return bool
	 */	
	public function isSetterVariable($content)
	{
		return strstr($content, Compiler::EQUAL_WITH)?true:false;
	}

	/**
	 * Get paired setter variable key and value by the given content
	 * Ex. $example = 'some example'
	 * 
	 * @param string $content
	 * 
	 * @return bool
	 */	
	public function getSetterValueVariables($content)
	{
		if($this->isSetterVariable($content)) {
			$content = trim($content, ' ');
			$contents = explode(Compiler::EQUAL_WITH, $content);
			
			return $contents;
		}
	}

	/**
	 * Determine if the given content is a getter variable
	 * 
	 * @param string $content
	 * 
	 * @return bool
	 */		
	public function isGetterVariable($content)
	{
		if(strstr($content, '$')) {
			return true;
		}
		return false;
	}

	/**
	 * Set resolved specification at curly braces container
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */	
	public function setFilteredContent($content)
	{
		$this->items[$this
			->getGroupName()]['content'][$this
			->getIdentity()] = $content;
		
		return $this;
	}

	/**
	 * Get filtered content specification
	 * 
	 * @return array
	 */		
	public function getFilteredContents()
	{
		return $this->items[$this->getGroupName()]['content'];
	}

	/**
	 * Get current group name
	 * 
	 * @return string
	 */		
	public function getGroupName()
	{
		return $this->groupName;
	}

	/**
	 * Set group name by the given group name
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */		
	public function setGroupName($groupName)
	{
		$this->groupName = $groupName;
		
		return $this;
	}

	/**
	 * Get current identity for the specific item
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */		
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 * Set current identity for the specific item
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */
	public function setIdentity($identity)
	{
		$this->identity = $identity;
		
		return $this;
	}

	/**
	 * Set layout target basepath
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */
	public function setBasepath($path)
	{
		$this->basepath = $path;

		return $this;
	}

	/**
	 * Get layout target basepath
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */
	public function getBasepath()
	{
		return $this->basepath;
	}

	/**
	 * Get content specification
	 * 
	 * @return string
	 */		
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Get entire contents
	 * 
	 * @return array
	 */
	public function getContents()
	{
		return $this->items;
	}

	/**
	 * Get the layout target from the current view file
	 * 
	 * @return string
	 */
	public function getLayoutTarget()
	{
		return $this->layoutTarget;
	}

	/**
	 * Set layout target from the current view file
	 * 
	 * @return string
	 */
	public function setLayoutTarget($target)
	{
		$this->layoutTarget = $target;
	}

	/**
	 * Get current view file
	 * 
	 * @return string
	 */
	public function getMainFile()
	{
		$target = $this->mainFile;

		if($this->fs->exists($this->mainFile))
			return $target;
	}

	/**
	 * Get contents by the given group name
	 * 
	 * @return array
	 */
	public function getContentsBy($group)
	{
		if (isset($this->items[$group])) {
			return $this->items[$group];
		}
	}

	/**
	 * Set pattern specification
	 * 
	 * @return \Repository\Component\View\Compiler\Translator
	 */		
	public function setPattern($pattern)
	{
		return $this->pattern = $pattern;

		return $this;
	}

	/**
	 * Get pattern specification
	 * 
	 * @return string
	 */		
	public function getPattern()
	{
		return $this->pattern;
	}
}