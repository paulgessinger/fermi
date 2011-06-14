<?php

/**
 * Abstraction for the PHP session. This is a singleton.
 *
 * @author Paul Gessinger
 */
class Session extends FermiObject
{
	
	/**
	 * Makes the Session ready. Loads models User and Right to make sure we can hydrate User model from the session.
	 */
	function launch()
	{
		Core::getModel('core:User') ;
		Core::getModel('core:Right') ;
		session_start() ;
		$this->session = $_SESSION ;
	}
	
	/**
	 * Retrieve a method from the session.
	 * @param string $key The key that is to be retrieved.
	 */
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
	
	/**
	 * Set a key value pair. Overwrites existing ones.
	 * @param string $key The key tostore under.
	 * @param mixed $value Whatever you want to store.
	 */
	function _set($key, $value)
	{
		$this->session[$key] = $value ;
		$_SESSION[$key] = $value ;
	}
	
	/** 
	 * Attempts to load the user model from the session. Creates one and stores if no model is stored yet.
	 */
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
	
	/** 
	 * Abstract function for the hashing algorithm that we use.
	 */
	static function generateHash($var)
	{
		return sha1($var) ;
	}
	
	/**
	 * Set a UserModel to be stored in the Session.
	 * @param object UserModel $user The UserModel that is to be stored.
	 */
	function _setUser(UserModel $user)
	{
		$this->set('user', $user) ;
	}
	
	/** 
	 * Convenience for asking the UserModel to authorize something.
	 * @param string $path An identifier for a right, that the model is to be asked to authorize.
	 * @return boolean 
	 */
	function _authorize($path)
	{
		return $this->_getUser()->authorize($path) ;
	}
	
}