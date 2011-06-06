<?php

class AdminAgent extends FermiAgent
{
	function __construct()
	{
		Response::setSkin('admin') ;
	}
	
	function dispatch($action)
	{	
		$user = Session::getUser() ;
		
		if(!$user->getId()) // no user is logged in, show login page
		{
			parent::dispatch('login', 'Adminindex') ;
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
			
			parent::dispatch($action, Request::get('controller')) ;
		}
		else // user is logged in, but has no right to view admin, show access denied
		{
			parent::dispatch('accessdenied', 'Adminindex') ;
			return true ;
		}
	
	
	
	
	}

}