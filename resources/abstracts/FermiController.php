<?php

/**
 * This is the prototype for Controllers. It implements standard Controller behaviour.
 *
 * @package Core
 * @author Paul Gessinger
 */
abstract class FermiController extends FermiObject implements Controller
{
	var $actions = array() ;
	
	function __construct()
	{
	}
	
	/**
	 * executes the given task.
	 * @param string $task The task to be executed.
	 */
	function execute($action)
	{
		if(method_exists($this, $action.'Action'))
		{
			call_user_func_array(array($this, $action.'Action'), array()) ;
		}
		else
		{
			header("HTTP/1.0 404 Not Found");
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