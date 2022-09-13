<?php
namespace Repository\Component\Collection;

use Closure;
use Countable;
use ArrayAccess;
use Traversable;
use ArrayIterator;
use IteratorAggregate;

/**
 * Array Manipulation.
 *
 * @package	  \Repository\Component\Collection
 * @author    Hariyanto - harrieanto31@yahoo.com
 * @version   1.0
 * @link      https://www.bandd.web.id
 * @copyright Copyright (C) 2019 Hariyanto
 * @license   https://github.com/harrieanto/repository/blob/master/LICENSE.md
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * @var array
	 */
	private $items;

	/**
	 * Create a new Collection
	 *
	 * @param array $items
	 * 
	 * @return void
	 */
	public function __construct(array $items = [])
	{
		$this->items = $items;
	}

	/**
	 * Create a new collection instance if the value isn't one already.
	 *
	 * @param  mixed  $items
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public static function make($items)
	{
		if (is_null($items)) return new static;

		if ($items instanceof Collection) return $items;

		return new static(is_array($items) ? $items : array($items));
	}

	/**
	 * Add a new item by key
	 *
	 * @param string $key
	 * @param mixed $value
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function add($key, $value)
	{
		$this->items[$key] = $value;
		return $this;
	}

	/**
	 * Get all the items of the Collection
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->items;
	}

	/**
	 * Determine if the given value is in the items
	 *
	 * @param mixed $value
	 * 
	 * @return bool
	 */
	public function contains($value)
	{
		return in_array($value, $this->items);
	}

	/**
	 * Count the number of items in the Collection
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * Determine if the items is empty
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->items);
	}

	/**
	 * Run a callback on each item
	 *
	 * @param \Closure $callback
	 * 
	 * @return array
	 */
	public function each(Closure $callback)
	{
		return array_map($callback, $this->items);
	}

	/**
	 * Filter the Collection and return a new Collection
	 *
	 * @param \Closure $callback
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function filter(Closure $callback)
	{
		return new Collection(array_filter($this->items, $callback));
	}

	/**
	 * Determine if an item exists in the collection by key.
	 *
	 * @param  mixed  $key
	 * 
	 * @return bool
	 */
	public function has($key)
	{
		return $this->offsetExists($key);
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param  mixed  $key
	 * 
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return array_key_exists($key,  $this->items);
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param  mixed  $key
	 * 
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->items[$key];
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed  $key
	 * @param  mixed  $value
	 * 
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		if (is_null($key)) {
			$this->items[] = $value;
		} else {
			$this->items[$key] = $value;
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string  $key
	 * 
	 * @return void
	 */
	public function offsetUnset($key)
	{
		unset($this->items[$key]);
	}

	/**
	 * Remove an item from the collection by key.
	 *
	 * @param  mixed  $key
	 * 
	 * @return void
	 */
	public function forget($key)
	{
		unset($this->items[$key]);
	}

	/**
	 * Get the first item of the Collection
	 *
	 * @return array
	 */
	public function first()
	{
		return reset($this->items);
	}

	/**
	 * Return the Collection's keys
	 *
	 * @return array
	 */
	public function keys()
	{
		return array_keys($this->items);
	}

	/**
	 * Reset the values of the Collection
	 *
	 * @return void
	 */
	public function values()
	{
		$this->items = array_values($this->items);
	}

	/**
	 * Return only unique items from the collection.
	 *
	 * @return Collection
	 */
	public function unique()
	{
		return new static(array_unique($this->items));
	}

	/**
	 * Get an item from the Collection by key
	 *
	 * @param mixed $key
	 * 
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->items[$key];
	}

	/**
	 * Get the last item of the Collection
	 *
	 * @return mixed
	 */
	public function last()
	{
		return end($this->items);
	}

	/**
	 * Get the current item of the Collection
	 *
	 * @return mixed
	 */
	public function current()
	{
		return current($this->items);
	}

	/**
	 * Get the next item of the Collection
	 *
	 * @return mixed
	 */
	public function next()
	{
		return next($this->items);
	}

	/**
	 * Run a Closure over each item and return a new Collection
	 *
	 * @param \Closure $callback
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function map(Closure $callback)
	{
		return new Collection(array_map($callback,  $this->items, array_keys($this->items), array($this)));
	}

	/**
	 * Transform each item in the collection using a callback.
	 *
	 * @param \Closure  $callback
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function transform(Closure $callback)
	{
		$this->items = array_map($callback, $this->items);

		return $this;
	}

	/**
	 * Pop the last item off the Collection
	 *
	 * @return array
	 */
	public function pop()
	{
		return array_pop($this->items);
	}

	/**
	 * Push an item onto the start of the Collection
	 *
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public function prepend($value)
	{
		array_unshift($this->items, $value);
	}

	/**
	 * Push an item onto the end of the Collection
	 *
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public function push($value)
	{
		$this->items[] = $value;
	}

	/**
	 * Replace an item by key
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * 
	 * @return void
	 */
	public function replace($key, $value)
	{
		$this->items[$key] = $value;
	}

	/**
	 * Remove all item from the collection
	 *
	 * @return void
	 */
	public function flush()
	{
		foreach ($this->items as $key => $value) {
			unset($this->items[$key]);
		}
	}

	/**
	 * Search the Collection for a value
	 *
	 * @param mixed $value
	 * 
	 * @return mixed
	 */
	public function search($value)
	{
		return array_search($value,  $this->items, true);
	}

	/**
	 * Get and remove the first item
	 *
	 * @return mixed
	 */
	public function shift()
	{
		return array_shift($this->items);
	}

	/**
	 * Sort through each item with a callback
	 *
	 * @param Closure $callback
	 * 
	 * @return void
	 */
	public function sort(Closure $callback)
	{
		uasort($this->items, $callback);
	}

	/**
	 * Reverse items order.
	 *
	 * @return \Repository\Component\Collection\Collection
	 */
	public function reverse()
	{
		return new static(array_reverse($this->items));
	}

	/**
	 * Reduce the collection to a single value.
	 *
	 * @param  callable  $callback
	 * @param  mixed  $initial
	 * 
	 * @return array
	 */
	public function reduce($callback,  $initial = null)
	{
		return array_reduce($this->items,  $callback,  $initial);
	}
	
	/**
	 * Flip every pieces value items position with the key
	 */
	public function flipForce()
	{
		$results = array();
		foreach ($this->items as $key => $value) {
			if (is_array($value)) {
				for ($i=0;$i<count($value);$i++) {
					$results[array_values($value)[$i]] = $key;
				}
			}
			if (is_string($value)) {
				$results[$value] = $key;
			}
		}

		$this->items = $results;
		return new static($this->items);
	}

	/**
	 * Take the first or last {$limit} items.
	 *
	 * @param  int  $limit
	 * 
	 * @return Collection
	 */
	public function take($limit = null)
	{
		if ($limit < 0) {
			return $this->slice($limit, abs($limit));
		}

		return $this->slice(0, $limit);
	}

	/**
	 * Slice the underlying collection array.
	 *
	 * @param  int   $offset
	 * @param  int   $length
	 * @param  bool  $preserveKeys
	 * 
	 * @return \Repository\Component\Collection\Collection
	 */
	public function slice($offset,  $length = null,  $preserveKeys = false)
	{
		return new static(array_slice($this->items,  $offset,  $length,  $preserveKeys));
	}

	/**
	 * Chunk the underlying collection array.
	 *
	 * @param  int $size
	 * @param  bool  $preserveKeys
	 * 
	 * @return Collection
	 */
	public function chunk($size, $preserveKeys = false)
	{
		$chunks = new static;

		foreach (array_chunk($this->items, $size, $preserveKeys) as $chunk) {
			$chunks->push(new static($chunk));
		}

		return $chunks;
	}

	/**
	 * Get one or more items randomly from the collection.
	 *
	 * @param  int $amount
	 * 
	 * @return mixed
	 */
	public function random($amount = 1)
	{
		$keys = array_rand($this->items,  $amount);

		return is_array($keys) ? array_intersect_key($this->items,  array_flip($keys)) : $this->items[$keys];
	}

	/**
	 * Pull out single or multiple items by key
	 * 
	 * @param  mixed $key item keys
	 * 
	 * @return array
	 */
	public function anypull($keys): Array
	{
		array_map(function(&$item) use ($keys) {
			//when key variable part of array
			if (is_array( $keys )) {
				$items = $this->items;
				unset($this->items);
				(!in_array($item,  $keys))?
					array():
					$this->items[$item] = $items[$item];

			//when key variable part of single string
			} else {
				if($item !== $keys){
					unset($this->items[$item]);
				}
			}
		},  array_flip($this->items));
		
		return $this->items;
	}

	/**
	 * Unset single or multiple items
	 * 
	 * @param  mixed $key  items key
	 * 
	 * @return array
	 */
	public function anypop($keys)
	{
		//brightening all mess :)
		array_map(function(&$item) {
			//when key variable part of array
			if (is_array( $keys )) {
				foreach ($keys as $key) {
					//when value same with key variable unset it.
					if($item === $key){
						unset($this->items[$item]);
					}
				}
			//when key variable part of single string
			}else{
				if ($item === $keys) {
					unset($this->items[$item]);
				}
			}
		},  array_flip($this->items));
		
		return $this->items;
	}

	/**
	 * Force unset/reset items explicitly 
	 * 
	 * @param  mixed $key  items key
	 * 
	 * @return array
	 */
	public function anypops($keys)
	{
		//brightening all mess :)
		array_map (function ( &$item ) {
			//when key variable part of array
			if (is_array( $keys )) {
				if (in_array( $item,  $keys )) {
					 unset($this->items[$item]);
					 unset($item);
				}

				if ( !isset($item) )
					$this->items[$item] = $this->items[$item];

			//when key variable part of single string
			} else {
				if ($value === $keys)
					unset($this->items[$item]);
					unset($item);

				if ( !isset($item) )
					$this->items[$item] = $this->items[$item];
			}

		},  array_keys($this->items));
		
		return $this->items;
	}

	/**
	 * Return the Collection as an array
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->items;
	}

	/**
	 * Get an iterator for the items
	 *
	 * @return ArrayIterator
	 */
	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->items);
	}
}