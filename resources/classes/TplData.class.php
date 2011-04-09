<?php
class TplData
{
	static $_autoInstance = false ;
	protected $functions ;
	
	function __construct($bind_array, $functions) 
	{
	
		$this->values = $bind_array ;
		$this->functions = $functions ;

	}
	
	function __get($name)
	{
		return $this->values[$name] ;
	}
	
	
	function __call($function, $arguments)
	{
		if(array_key_exists($function, $this->functions))
		{	
			return call_user_func_array($this->functions[$function], $arguments) ;
		}
		else
		{
			return false ;
		}
	}
	
	
}