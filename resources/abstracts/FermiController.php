<?php

/**
 * This is the prototype for Controllers. It implements standard Controller behaviour.
 * @author Paul Gessinger
 *
 */
abstract class FermiController implements Controller
{
	var $actions = array() ;
	
	function __construct()
	{
	}
	
	/**
	 * executes the given task.
	 * @param string $task The task to be executed.
	 */
	function execute($action, $params)
	{
		if(method_exists($this, $action.'Action'))
		{
			call_user_func_array(array($this, $action.'Action'), array('params' => $params)) ;
		}
		else
		{
			throw new RoutingException('Action "'.$action.'" is not available in controller "'.get_class($this).'"') ;
		}
	}
	
	/**
	 * Prepares content for being sent.
	 */
	function render()
	{	
	}
}