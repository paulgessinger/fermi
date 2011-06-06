<?php
class IndexAgent extends FermiAgent
{	
	function preDispatch()
	{
		
		if($this->controller)
		{
			return true ;
		}
		
		if(!($controller = Request::get('controller')))
		{
			$controller = Registry::get('default_controller') ;
		}
		
		Request::set('controller', $controller) ;
		
		$this->controller = $controller ;
	
		
	}
}