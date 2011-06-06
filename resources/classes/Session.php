<?php

class Session extends FermiObject
{
	
	function __construct()
	{
	}
	
	function launch()
	{
		Core::getModel('core:User') ;
		Core::getModel('core:Right') ;
		session_start() ;
		$this->session = $_SESSION ;
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
	
	function _getUser()
	{
		if($user = $this->get('user'))
		{
			return $user ;
		}
		else
		{
			$user = Core::getModel('core:User') ;
			$this->set('user', $user) ;
			
			return $user ;
		}
	}
	
	static function generateHash($var)
	{
		return sha1($var) ;
	}
	
	function _setUser(UserModel $user)
	{
		$this->set('user', $user) ;
	}
	
	function _authorize($path)
	{
		return $this->_getUser()->authorize($path) ;
	}
	
}