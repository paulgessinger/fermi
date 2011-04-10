<?php
class DebugAgent extends FermiAgent
{
	/*function __construct($agent, $controller, $task)
	{
		parent::__construct($agent, $controller, $task) ;	
	}*/
	
	function dispatch($controller, $task, $params)
	{
		
		
		parent::dispatch($controller, $task, $params) ;
	}
}