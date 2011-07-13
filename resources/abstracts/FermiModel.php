<?php

/**
 * Prototype Model. All models should inherit from this.
 *
 * @package Core
 * @author Paul Gessinger
 */
abstract class FermiModel extends FermiObject implements Model, IteratorAggregate
{
	var $bean = false ;
	var $values = array() ;
	protected $new = false ;
	protected $errors = array() ;

	function __construct() {}

	/**
	 * Get the ID of the Item.
	 * @return int / bool The ID of the bean if there is one, if not or if it is empty returns false
	 */
	function getId() 
	{
		$format = FermiBeanFormatter::_formatBeanID($this->type) ;
		if(isset($this->bean->$format))
		{
			return $this->bean->$format ;
		}
		else
		{
			return false ;
		}
	}
	
	/** 
	 * Load a data set by id. Returns false if it does not exist.
	 * @param int $id The id of the data set.
	 * @return object FermiModel / boolean $this or false
	 */
	function load($id)
	{
		if(!Database::$connection)
		{
			return false ;
		}
		
		if($this->bean)
		{
			throw new OrmException('Model "'.get_class($this).'" has already been tainted. You can not load values from db into it.') ;
		}
		
		
		$this->bean = R::load($this->type, $id) ; // use redbean facade to retrieve a bean from database.
	
		if($this->getId())
		{
			return $this ;
		}
		else
		{
			return false ; //object with this id does not exist
		}
	}
	
	/**
	 * Find a data set based on complex criteria.
	 * @params string $sql An SQL like Where statement like "name=?". Is used in prepared statement.
	 * @param array $values The values for the where statement.
	 * @return object FermiModel / boolean $this or false
	 */
	function find($sql, array $values)
	{
		if(!Database::$connection)
		{
			return false ;
		}
		
		
		if($this->bean)
		{
			throw new OrmException('Model "'.get_class($this).'" has already been tainted. You can not load values from db into it.') ;
		}
		

		$this->bean = R::findOne($this->type, $sql, $values) ; // use redbean facade to retrieve a bean from database.

		
			
		
		if(!is_object($this->bean)) // should not be the case
		{
			return false ;
		}
		
		if($this->getId())
		{
			return $this ;
		}
		else
		{
			return false ; //object with this id does not exist
		}
	}
	
	function delete()
	{
		if(!$this->bean)
		{
			throw new OrmException('Model "'.get_class($this).'" cannot be deleted without it being loaded with a data record.') ;
		}
		
		R::trash($this->bean) ;
	}
	
	/**
	 * Magic method for setting properties of the data record through redbean.
	 * @param string $key The key that the value is to be stored under.
	 * @param mixed $value What you want to store.
	 */
	function __set($key, $value)
	{
		if(!$this->bean)
		{
			$this->bean = R::dispense($this->type) ;
			$this->new = true ;
		}
		$this->values[$key] = $value ;  
	}
	
	/**
	 * Magic getter for accessing data record properties.
	 * @param string $key The key to fetch.
	 * @return mixed What was stored.
	 */
	function __get($key)
	{
		
		if(!$this->bean)
		{
			$this->bean = R::dispense($this->type) ;
			$this->new = true ;
		}
		
		if(array_key_exists($key, $this->values))  
		{
			return $this->values[$key] ;
		}
		else
		{
			return $this->bean->__get($key) ;
		}
	}
	
	/** 
	 * Magic method for checking if a property exists.
	 * @param string $key The key to check for.
	 */
	function __isset($key)
	{
		$value = $this->__get($key) ;
		if(empty($value))
		{
			return false ;
		}
		else
		{
			return true ;
		}
	}
	
	/**
	 * Returns whether this Model is a new entry or one that has previously been fetched.
	 * @return boolean
	 */
	final function isNew()
	{
		return $this->new ;
	}
	
	/**
	 * Add an error to the Model error queue. If it is not empty on save, save will be aborted, and this array will be returned.
	 * @param string $error The error that is to be added.
	 */
	final function addError($error)
	{
		$this->errors[] = $error ;
	}
	
	/** 
	 * Saves the data record back to the database.
	 * @return boolean true on success, array An array of errors on failure.
	 */
	final function save()
	{
		$this->_beforeSave() ;
		$errors = array() ;
		
		try
		{
			$this->validate() ;
			
			if(count($this->errors) != 0)
			{
				return $this->errors ;
			}
		
			foreach($this->values as $key => $value)
			{
				$this->bean->__set($key, $value) ; 
			}
			
			
			R::store($this->bean) ;
		
			$this->bean->save() ;
			
			
		}
		catch(DatabaseException $e)
		{
			return $e->getMessage() ;
		}
		
		return true ;
	}
	
	
	/**
	 * Get a collection instance based on this model type.
	 * @return object FermiCollection
	 */
	function getCollection()
	{
		if($this->bean)
		{
			throw new OrmException('Cannot create collection out of loaded model.') ;
		}
		
		$collection = new FermiCollection($this) ;
		return $collection ;
	}
	
	/**
	 * Is called before saving, can be extended to perform additional actions before anything is written to the database.
	 */
	function _beforeSave() {}
	
	/**
	 * Is called before saving, can be extended to validate Model data.
	 */
	function validate() {}
	
	public function getIterator()
	{
	        return $this->bean->getIterator() ;
	}
}