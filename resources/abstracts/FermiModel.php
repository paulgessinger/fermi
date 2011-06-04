<?php

abstract class FermiModel extends FermiObject implements Model
{
	var $bean = false ;
	var $values = array() ;
	protected $new = false ;
	protected $errors = array() ;
	
	function __construct()
	{
	}
	
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
	
	function load($id)
	{
		if($this->bean)
		{
			throw new OrmException('Model "'.get_class($this).'" has already been tainted. You can not load values from db into it.') ;
		}
		
		$this->bean = R::load($this->type, $id) ;
		
		//$id_format = FermiBeanFormatter::_formatBeanID($this->type) ;
	
		if($this->getId())
		{
			return $this ;
		}
		else
		{
			return false ; //object with this id does not exist
		}
	}
	
	function find($sql, array $values)
	{
		if($this->bean)
		{
			throw new OrmException('Model "'.get_class($this).'" has already been tainted. You can not load values from db into it.') ;
		}
		
		$this->bean = R::findOne($this->type, $sql, $values) ;
		
			
		
		if(!is_object($this->bean))
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
	
	function __set($key, $value)
	{
		if(!$this->bean)
		{
			$this->bean = R::dispense($this->type) ;
			$this->new = true ;
		}
		$this->values[$key] = $value ;  
	}
	
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
	
	final function isNew()
	{
		return $this->new ;
	}
	
	final function addError($error)
	{
		$this->errors[] = $error ;
	}
	
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
	
	
	function getCollection()
	{
		if($this->bean)
		{
			throw new OrmException('Cannot create collection out of loaded model.') ;
		}
		
		$collection = new FermiCollection($this) ;
		return $collection ;
	}
	
	function _beforeSave() {}
	function validate() {}
}