<?php

/**
 * Prototype Agent class. This one implements standard behaviour of an Agent.
 *
 * @package Core
 * @author Paul Gessinger
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
	function dispatch($action, $controller = false)
	{
		$this->agent = $this ;
		$this->action = $action ;
		$this->controller = $controller ;
		
		$this->preDispatch() ;


		if($this->controller === false)
		{
			$this->controller = Request::get('controller') ;
		}
		
		if(is_string($this->controller))
		{
			if(!isset(Core::$_registry->controllers[get_class($this)][$this->controller]))
			{
				header("HTTP/1.0 404 Not Found");
				throw new RoutingException('Controller "'.$this->controller.'" could not be retrieved.') ;
			}
			else
			{
				include Core::$_registry->controllers[get_class($this)][$this->controller] ;
				$this->controller = new $this->controller ;
			}
		}
		elseif(is_object($this->controller))
		{	
			if(!isset(Core::$_registry->controllers[get_class($this)][get_class($this->controller)]))
			{
				header("HTTP/1.0 404 Not Found");
				throw new RoutingException('Controller "'.get_class($this->controller).'" is not assigned to this agent "'.get_class($this).'".') ;
			}
		}
		
		
		Core::fireEvent('onAgentReady', array('agent' => $this->agent, 'controller' => $this->controller, 'action' => $this->action)) ;
		Core::rechargeEvent('onAgentReady') ;
		Core::fireEvent('on'.get_class($this).'Ready', array('agent' => $this->agent, 'controller' => $this->controller, 'action' => $this->action)) ;
		Core::rechargeEvent('on'.get_class($this).'Ready') ;
	
		
		$this->controller->execute($this->action) ;
	}
	
	function preDispatch() {}
	
	/**
	 * Is called when Core has decided that this Agent is to get the call. The Agent gets a chance to prepare for dispatch.
	 */
	function notify()
	{
	}
}
