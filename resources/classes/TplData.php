<?php

/**
 * Template Wrapper class that encapsulates the .php templates. Exposes all relevant variables and functions to the template under $tpl.
 *
 * @package Core
 * @author Paul Gessinger
 */
class TplData extends FermiObject
{
	static $_autoInstance = false ;
	protected $functions ;
	
	/**
	 * Accepts an array of values that are to be exposed to the template as well as a set of functions to expose.
	 * @param array $bind_array An array that contains key/value pairs.
	 * @param array $functions An array that contains function names and callable resources to be exposed as template functions.
	 */
	function __construct($bind_array, $functions) 
	{
	
		$this->values = $bind_array ;
		$this->functions = $functions ;

	}
	
	/**
	 * Magic get method to access template values as properties.
	 * @param string $name The name of the variable that is to be accessed.
	 * @return mixed The content of the variable in question.
	 */
	function __get($name)
	{
		if(!isset($this->values[$name]))
		{
			return false ;
		}
		
		if(is_object($this->values[$name]) AND get_class($this->values[$name]) == 'Template')
		{
			return $this->values[$name]->render() ;
		}
		
		return $this->values[$name] ;
	}
	
	/**
	 * Magic call method that exposes all the template functions.
	 * @param string $function The function that is to be called.
	 * @param array $arguments All the arguments that the call within the templates wants to pass on to the function.
	 * @return mixed The result of the function that is called.
	 */
	function __call($function, $arguments)
	{
		if(array_key_exists($function, $this->functions))
		{	
			return call_user_func_array($this->functions[$function], $arguments) ;
		}
		else
		{
			throw new ErrorException('Call to undefined function "'.$function.'"') ;
		}
	}
	
	function __isset($name)
	{
		if(array_key_exists($name, $this->values))
		{
			return true ;
		}
		else
		{
			return false ;
		}
	}
	
	
}