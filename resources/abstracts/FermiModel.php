<?php

abstract class FermiModel implements Model
{
	var $bean = false ;
	var $values = array() ;
	
	function __construct()
	{
		
	}
	
	function load($id)
	{
		/*$requested_model = get_called_class() ;
		$vars = get_class_vars($requested_model) ;*/
		
		$this->bean = R::load($this->type, $id) ;
		
		$id_format = FermiBeanFormatter::formatBeanID($this->type) ;
	
		if($this->bean->$id_format != 0)
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

		$this->bean = R::findOne($this->type, $sql, $values) ;
		
		$id_format = FermiBeanFormatter::formatBeanID($vars['type']) ;
			
		if($bean->$id_format != 0)
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
		}
		$this->values[$key] = $value ;  
	}
	
	function __get($key)
	{
		
		if(!$this->bean)
		{
			$this->bean = R::dispense($this->type) ;
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
	
	final function save()
	{
		$this->_beforeSave() ;
		$errors = array() ;
		
		try
		{
			$errors = $this->validate() ;
			
			if(count($errors) != 0)
			{
				return $errors ;
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
	
	function _beforeSave() {}
	function validate() {}
}