<?php

class FermiObject
{
	public static function __callStatic($function, $arguments)
	{
		if(method_exists(get_called_class(), '_'.$function))
		{
			return call_user_func_array(array(Core::get(get_called_class()), '_'.$function), $arguments) ;
		}
		
		throw new ErrorException('Call to undefined method "'.$function.'" in class "'.get_called_class().'"') ;
	}
	
	public function __call($function, $arguments)
	{		
		if(method_exists($this, '_'.$function))
		{
			return call_user_func_array(array($this, '_'.$function), $arguments) ;
		}
	}
}