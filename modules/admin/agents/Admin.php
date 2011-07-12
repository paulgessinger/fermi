<?php

/**
 * This is the AdminAgent. It does ACL checks and handles loading of the Admin panel.
 *
 * @package Admin
 * @author Paul Gessinger
 */
class AdminAgent extends FermiAgent
{
	
	/**
	 * Sets the skin to "admin"
	 * @todo Make this be fetched from db or so.
	 */
	function __construct()
	{
		Response::setSkin('admin') ;
	}
	
	
	/**
	 * Override dispatch function in order to perform ACL checks, and redirect to login if necessary.
	 */ 
	function dispatch($action)
	{	
		Response::bind('title', 'fermi - Admin') ;
		
		
		$user = Session::getUser() ;
		
		if(!$user->getId()) // no user is logged in, show login page
		{
			parent::dispatch('login', 'Panel') ;
			return true ;
		}
		
		if($user->authorize('admin')) // user is logged in, and has right to view admin, proceed
		{
			if(!Request::get('controller'))
			{
				Request::set('controller', 'Dashboard') ;
			}
			
			if(!Request::get('action'))
			{
				Request::set('action', 'index') ;
			}
			
			
			Admin::loadMenu() ;
			
			
			parent::dispatch($action, Request::get('controller')) ;
		}
		else // user is logged in, but has no right to view admin, show access denied
		{
			parent::dispatch('accessdenied', 'Panel') ;
			return true ;
		}
	}

}