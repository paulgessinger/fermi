<?php
class Delivery extends CAgent
{	
	/*function __construct($agent, $controller, $task)
	{
		parent::__construct($agent, $controller, $task) ;	
	}*/
	
	function dispatch($controller, $task, $params)
	{
		//echo 'delivery' ;
		
		
		
		parent::dispatch($controller, $task, $params) ;
	}
}