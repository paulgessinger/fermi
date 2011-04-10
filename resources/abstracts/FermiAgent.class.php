<?php

/**
 * Prototype Agent class. This one implements standard behaviour of an Agent.
 * @author Paul Gessinger
 *
 */
abstract class CAgent implements Agent
{
	var $controllers = array() ;
	
	/**
	 * takes the Request parameters and fires several events based on those.
	 * @param $agent
	 * @param $controller
	 * @param $task
	 */
	function __construct()
	{
		//$this->registerTask('index') ;
	}
	
	/**
	 * Registers the Controller given to the Agent. Once the Agent is aware of the attached Controllers, it can dispatch
	 * calls to them
	 * @param Controller $controller The Controller that is to be registered.
	 */
	function registerController(Controller $controller)
	{
		$this->controllers[get_class($controller)] = $controller ;
	}
	
	/**
	 * dispatches the call to the controller given.
	 * @param Controller $controller The Controller that is to process the request
	 */
	function dispatch($controller, $task, $params)
	{
		$this->agent = $this ;
		$this->controller = $controller ;
		$this->task = $task ;
		
		
		Core::fireEvent('onAgentReady', $this->agent, $this->controller, $this->task) ;
		Core::rechargeEvent('onAgentReady') ;
		Core::fireEvent('on'.get_class($this).'Ready', $this->agent, $this->controller, $this->task) ;
		
		if(!is_object($this->controllers[$this->controller]) OR !array_key_exists($this->controller, $this->controllers))
		{
			throw new RoutingException('Controller "'.$this->controller.'" does not exist.') ;
		}
		
		$this->controllers[$this->controller]->execute($this->task, $params) ;
	}
	
	/**
	 * instructs the Controller to prepare its outputs to be sent.
	 */
	function render()
	{
		$this->controllers[$this->controller]->render() ;
	}
	
	/**
	 * Is called when Core has decided that this Agent is to get the call. The Agent gets a chance to prepare for dispatch.
	 */
	function notify()
	{
	}
}
