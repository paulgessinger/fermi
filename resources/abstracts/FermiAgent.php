<?php

/**
 * Prototype Agent class. This one implements standard behaviour of an Agent.
 * @author Paul Gessinger
 *
 */
abstract class FermiAgent extends FermiObject implements Agent
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
	}
	
	/**
	 * dispatches the call to the controller given.
	 * @param Controller $controller The Controller that is to process the request
	 */
	function dispatch(FermiController $controller, $action)
	{
		$this->agent = $this ;
		$this->controller = $controller ;
		$this->action = $action ;
		
		
		Core::fireEvent('onAgentReady', $this->agent, $this->controller, $this->action) ;
		Core::rechargeEvent('onAgentReady') ;
		Core::fireEvent('on'.get_class($this).'Ready', $this->agent, $this->controller, $this->action) ;
		Core::rechargeEvent('on'.get_class($this).'Ready') ;
	
		
		$this->controller->execute($this->action) ;
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
