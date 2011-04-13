<?php

abstract class FermiModel implements Model
{
	var $bean = false ;
	var $values = array() ;
	
	function __construct()
	{
		
	}
	
	static function load($id)
	{
		$requested_model = get_called_class() ;
		$vars = get_class_vars($requested_model) ;
		
		$bean = R::load($vars['type'], $id) ;
		
		$id_format = FermiBeanFormatter::formatBeanID($vars['type']) ;
	
		if($bean->$id_format != 0)
		{
			$model = new $requested_model ;
			$model->bean = $bean ;
			return $model ;
		}
		else
		{
			return false ; //object with this id does not exist
		}
	}
	
	static function find($sql, array $values)
	{
		$requested_model = get_called_class() ;
		$vars = get_class_vars($requested_model) ;

		$bean = R::findOne($vars['type'], $sql, $values) ;
		
		$id_format = FermiBeanFormatter::formatBeanID($vars['type']) ;
			
		if($bean->$id_format != 0)
		{
			$model = new $requested_model ;
			$model->bean = $bean ;
			return $model ;
		}
		else
		{
			return false ; //object with this id does not exist
		}
	}
	
	function __set($key, $value)
	{
		$this->values[$key] = $value ;  
	}
	
	function __get($key)
	{
		
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