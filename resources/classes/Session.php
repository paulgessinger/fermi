<?php

class Session extends FermiObject
{
	
	function __construct()
	{
		//$this->session = $_SESSION ;
	}
	
	function _get($key)
	{
		if(isset($this->session[$key]))
		{
			return $this->session[$key] ;
		}
		else
		{
			return false ;
		}
	}
	
	function _set($key, $value)
	{
		$this->session[$key] = $value ;
		$_SESSION[$key] = $value ;
	}
	
	function getUser()
	{
		if($user = $this->get('user'))
		{
			return $user ;
		}
		else
		{
			return Core::getModel('core:User') ;
		}
	}
	
}