<?php

/**
 * The default Agent of the frontend. Takes into account which default controller is set.
 *
 * @package Core
 * @author Paul Gessinger
 */
class IndexAgent extends FermiAgent
{	
	
	/**
	 * Runs before dispatch. Looks if a controller has been set in the request, if not, defaults to the default controller stored in registry.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function preDispatch()
	{
		
		if($this->controller)
		{
			return true ;
		}
		
		if(!($controller = Request::get('controller')))
		{
			$controller = Registry::get('default:default_controller') ;
		}
		
		Request::set('controller', $controller) ;
		
		$this->controller = $controller ;
	}
}