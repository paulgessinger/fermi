<?php

/**
 * This is the prototype for Controllers. It implements standard Controller behaviour.
 * @author Paul Gessinger
 *
 */
abstract class FermiController implements Controller
{
	var $tasks = array() ;
	
	function __construct()
	{
	}
	
	/**
	 * Registers this Controller with the Agent specified. It can now be called through said Agent.
	 * @param string $agent The name of the Agent the Controller is to be registered with.
	 */
	final function registerWith($agent) 
	{
		if(isset(Core::$_agent_instances[$agent]))
		{
			Core::$_agent_instances[$agent]->registerController($this) ;
		}
		else
		{
			throw new SystemException('Controller "'.get_class($this).'" tried to register with Agent "'.$agent.'", but it does not exist.') ;
		}
	}
	
	/**
	 * registers a task with the controller
	 * @param string $task The task name.
	 * @param string $method The method associated with the task. Must be inside the Controller.
	 */
	function registerTask($task, $method = false)
	{
		if(!$method)
		{
			$this->tasks[$task] = $task ;
		}
		else
		{
			$this->tasks[$task] = $method ;
		}
		
	}
	
	/**
	 * executes the given task.
	 * @param string $task The task to be executed.
	 */
	function execute($task, $params)
	{
		if(array_key_exists($task, $this->tasks) AND is_callable(array($this, $this->tasks[$task])))
		{
			call_user_func_array(array($this, $this->tasks[$task]), array('params' => $params)) ;
		}
		else
		{
			throw new RoutingException('Task "'.$task.'" is not registered in controller "'.get_class($this).'"') ;
		}
	}
	
	/**
	 * Prepares content for being sent.
	 */
	function render()
	{	
	}
}