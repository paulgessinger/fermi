<?php

/**
 * The base class that all classes should inherit from. Is used to give all objects pseudo-static method support.
 *
 * @package Core
 * @author Paul Gessinger
 */
class FermiObject
{
	
	/**
	 * Convenience Method fr creating pseudo-static methods. Static calls to :: are now interpreted as calls to the singleton,
	 * and tried to map on corresponding methods.
	 */
	public static function __callStatic($function, $arguments)
	{
		if(method_exists(get_called_class(), '_'.$function))
		{
			return call_user_func_array(array(Core::get(get_called_class()), '_'.$function), $arguments) ;
		}
	
		throw new ErrorException('Call to undefined method "'.$function.'" in class "'.get_called_class().'"') ;
	}
	
	/**
	 * Convenience Methods allowing pseudo-static methods to be called normally as well.
	 */
	public function __call($function, $arguments)
	{		
		if(method_exists($this, '_'.$function))
		{
			return call_user_func_array(array($this, '_'.$function), $arguments) ;
		}
		
		throw new ErrorException('Call to undefined method "'.$function.'" in class "'.get_class($this).'"') ;
	}
}