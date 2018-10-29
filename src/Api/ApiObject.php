<?php

namespace Blaze\Myst\Api;

use Blaze\Myst\Api\Objects\Raw;
use Illuminate\Support\Collection;

class ApiObject extends BaseObject
{
	/**
	 * If the instance has other nested api objects they should be mentioned in the array returned by implementation
	 * of this method on the extended class.
	 * This method should return an associative array where keys will be property of object, and values will be the
	 * api object class that it represents.
	 * This method should return only objects that have only a single instance of itself (like parent object).
	 * eg:  protected abstract function singleObjectRelations() {
	 *          return [
	 *              'from'               => User::class,
	 * 				'chat'               => Chat::class,
	 *          ];
	 *      }
	 * @return  array
	 */
	protected function singleObjectRelations() : array
	{
		return [
			//'property_name' => api_object_class::class
		];
	}
	
	/**
	 * If the instance has arrays of other nested api objects they should be mentioned in the array returned by
	 * implementation of this method on the extended class.
	 * This method should return an associative array where keys will be property of object, and values will be the
	 * api object class that it represents.
	 * This method should return only objects that have multiple instance of itself (like multiple attachments).
	 * eg:  protected abstract function multipleObjectRelations() {
	 *          return [
	 *              'entities'           => MessageEntity::class,
	 * 				'new_chat_members'   => User::class,
	 *          ];
	 *      }
	 * @return  array
	 */
	protected function  multipleObjectRelations() : array
	{
		return [
			//'property_name' => api_object_class::class
		];
	}
	
	/**
	 * If you like a property in the instance to be aliased to another key (property name) for every time an instance
	 * of this is returned to application, this is where you do it.
	 * All you have to do is provide a key value pair to the array that is returned by this method, where key is
	 * an existing property (item) name and value is the new property(item) name.
	 * eg:  protected abstract function proposedPropertyAliases() {
	 *          return [
	 *              'id_no'     => 'identifier_no',
	 *              'phone_no   => 'contact_no'
	 *          ];
	 *      }
	 * @return array
	 */
	protected function proposedPropertyAliases() : array
	{
		return [
			//'existing_property_name' => 'proposed_property_name'
		];
	}
	
	/**
	 * same as proposedPropertyAliases except for globalPropertyAliases cannot (and should not) be inherited (and set)
	 * by any child classes.
	 * The items in this property will be aliased in all the child instances of all api objects.
	 *
	 * @return array
	 */
	private function globalPropertyAliases() : array
	{
		return [
		
		];
	}
	
	/**
	 * Initialize a new instance of this class.
	 *
	 * @param $data
	 */
	public function __construct($data)
	{
	    if (!is_array($data)) return new Raw($data);
	    
		// Merge $proposed_property_aliases and $global_property_aliases into $property_aliases collection
		$property_aliases = $this->propagatePropertyAliases();
		
		$constructed_data = [];
		$single_relations = collect($this->singleObjectRelations());
		$multiple_relations = collect($this->multipleObjectRelations());
		
		foreach ($data as $key => $item) {
			
			if ($item === null) {
				
				$constructed_data[$key] = $item;
				if ($property_aliases->has($key)) $constructed_data[$property_aliases->get($key)] = $item;
				
			} elseif ($single_relations->has($key)) {
				
				$className = $single_relations->get($key);
				$temp = new $className($item);
				$constructed_data[$key] = $temp;
				
				if ($property_aliases->has($key)) $constructed_data[$property_aliases->get($key)] = $temp;
				
			} elseif ($multiple_relations->has($key)) {
				
				$className = $multiple_relations->get($key);
				$temp = [];
				
				foreach ($item as $value) {
					$temp[] = new $className($value);
				}
				
				$temp = new Collection($temp);
				$constructed_data[$key] = $temp;
				
				if ($property_aliases->has($key)) $constructed_data[$property_aliases->get($key)] = $temp;
				
			} else {
				
				$constructed_data[$key] = $item;
				if ($property_aliases->has($key)) $constructed_data[$property_aliases->get($key)] = $item;
			}
		}
		parent::__construct($constructed_data);
	}
	
	/**
	 * propagates all property aliases by merging global and proposed property_aliases
	 *
	 * @return Collection
	 */
	private function propagatePropertyAliases()
	{
		$property_aliases = collect();
		if (!is_array($this->globalPropertyAliases())) throw new CitadelException("\"method globalPropertyAliases()\" should return an array.");
		foreach ($this->globalPropertyAliases() as $original => $alias) {
			if (!is_string($alias) || is_int($alias)) continue;
			$property_aliases->put($original, $alias);
		}
		if (!is_array($this->proposedPropertyAliases())) throw new CitadelException("\"method proposedPropertyAliases\" should return an array.");
		foreach ($this->proposedPropertyAliases() as $original => $alias) {
			if (!is_string($alias) || is_int($alias)) continue;
			$property_aliases->put($original, $alias);
		}
		
		return $property_aliases;
	}
}