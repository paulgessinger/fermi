<?php

/**
 * This is Core, the frame of Charon. Core launches Registry and coordinates Events and render.
 * @author Paul Gessinger
 *
 */


class Core
{
	static $_self ;
	static $_registry ;
	static $starttime ;
	static $_controllers ;
	static $_controller_instances ;
	static $_agents ;
	static $_agent_instances ;
	protected $agent = 'Delivery' ;
	protected $controller = 'Sites' ;
	protected $task = 'index' ;
	protected $error_triggered = false ;
	var $events = array() ;
	var $launch_exception ;
	
	/**
	 * registers error exception to have custom handling of all php errors
	 */
	function __construct()
	{		
		header("Content-type: text/html; charset=utf-8");
		
		error_reporting(E_ALL ^ E_NOTICE) ;
		
		function exception_error_handler($errno, $errstr, $errfile, $errline ) 
		{
   			throw new ErrorException($errstr, 0, $errno, $errfile, $errline) ;
		}
		set_error_handler("exception_error_handler", E_ALL ^ E_NOTICE) ;
	}
	
	/**
	 * Is called directly from index.php. It's the starting point of the system and instanciates Core itself
	 * as well as Registry.
	 */
	public function _launch()
	{
		
		Core::$_self = new Core ;
			
			
		try
		{
			$reg = new Registry ;		
		}
		catch(Exception $e)
		{
			throw new Exception('Registry was unable to launch. <br/><br/><pre>'.$e.'</pre>') ;
		}
		
		Core::$_registry = $reg ;
		Core::_()->reg = $reg ;
		
		Core::fireEvent('onClassesReady') ;
		
		
		
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		Core::$starttime = $mtime ;
			
		foreach(Core::$_agents as $agent)
		{
			Core::$_agent_instances[$agent] = new $agent ;
		}
		
		foreach(Core::$_controllers as $controller)
		{
			Core::$_controller_instances[$controller] = new $controller ;
		}
		
		
		
		Core::fireEvent('onCoreReady') ;
		
	}
	
	/**
	 * Is called after _launch() has finished, directly from index.php. _route() uses Request to
	 * determine the requested resource. It creates an Agent and dispatches the call of a specific
	 * Controller to it.
	 * 
	 * Set $force to true to bypass overwriting of default or set values through uri
	 * @param bool $force
	 */
	public function _route($force = false)
	{	
		if($this instanceof Core)
		{
			try 
			{
			
				if(!$force)
				{
					$req = Request::getPath() ;
				
					if(!empty($req['agent']))
					{
						$this->agent = $req['agent'] ;
					}
					if(!empty($req['controller']))
					{
						$this->controller = $req['controller'] ;
					}
					if(!empty($req['task']))
					{
						$this->task = $req['task'] ;
					}
					
				
					if(is_object($this->launch_exception))
					{
						throw $this->launch_exception ;
					}
					
				}
			
			
			

				Core::fireEvent('onRoute') ;
				
				
				
				
				if(!Core::$_agent_instances[$this->agent])
				{
					throw new RoutingException('Agent "'.$this->agent.'" could not be retrieved.') ;
				}
				if(!isset(Core::$_controller_instances[$this->controller]))
				{
					throw new RoutingException('Controller "'.$this->controller.'" could not be retrieved.') ;
				}
				
				
				
				$this->agent_instance = Core::$_agent_instances[$this->agent] ;
				
				$this->agent_instance->notify() ;
				
				Core::fireEvent('onAgentDispatch') ;
				
				$this->agent_instance->dispatch($this->controller, $this->task, $req['params']) ;
				
				
				
			}
			catch(Exception $e)
			{				
				if(!$this->error_triggered)
				{
					$this->error_triggered = true ;
					//$this->agent = 'DebugAgent' ;
					$this->controller = 'ErrorController' ;
					$this->task = 'display' ;
					Request::set('Exception', $e) ;
					Response::disableOutput();
					Core::resetEvents() ;
					$this->_route(true) ;
				}
				
			}
			
		}
		else
		{	
			return Core::_()->_route() ;
		}
	}
	
	/**
	 * Is called after _route() has ended, and triggers the agent to render its contents, if this has not
	 * yet been done. Subsequently, Response is instructed to assemble the root_template if per-se 
	 * output has not been disabled, and send the accumulated string to the client.
	 */
	function _render()
	{
		if($this instanceof Core)
		{
			try 
			{
				Core::fireEvent('onRender') ;
				$this->agent_instance->render() ;
				$this->get('Response')->render() ;
			}
			catch(Exception $e)
			{
				$this->agent = 'DebugAgent' ;
				$this->controller = 'ErrorController' ;
				$this->task = 'display' ;
				Request::set('Exception', $e) ;
				Response::disableOutput();
				Core::resetEvents() ;
				Core::sealEvent('onRender') ;
				$this->_route(true) ;
				$this->_render();
			}
		}
		else
		{	
			return Core::_()->_render() ;
		}
	}
	
	/**
	 * returns an instance of Core, shortcut to Core::$_self
	 */
	function _()
	{
		return Core::$_self ;
	}
	
	/**
	 * Sets the agent to the one provided in $agent. Must implement the interface Agent.
	 * @param Agent $agent The Agent to set.
	 */
	function setAgent(Agent $agent)
	{
		if($this instanceof Core)
		{
			$this->agent = $agent ;
		}
		else
		{
			Core::_()->setAgent($agent) ;
		}
	}
	
	/**
	 * Uses Registry to retrieve an instance of the class given in $class. Returns a new one if there is none.
	 * @param string $class The class to retrieve.
	 * @return object
	 */	
	function get($class)
	{
		if($this instanceof Core)
		{
			return $this->reg->getInstance($class) ;
		}
		else
		{
			return Core::_()->get($class) ;
		}
	}
	
	
	/**
	 * registers an event listener to an event specified
	 * @param string The Event you want to add the listener to
	 * @param string/array Either a string name of a function or an array containing both reference to an object and method name
	 * @return void
	 */
	function addListener($event, $function)
	{
		if($this instanceof Core)
		{
			if(!array_key_exists($event, $this->events))
			{
				$this->events[$event] = new Event($event) ;
			}
			
			$this->events[$event]->registerListener($function) ;
		}
		else
		{
			return Core::$_self->addListener($event, $function) ;
		}
	}
	
	/**
	 * Fires an event executing all registered listeners
	 * @param string Event to be fired
	 * @return unknown_type All the returns of functions registered to the Event
	 */
	function fireEvent($event)
	{
		if($this instanceof Core)
		{
			if(!array_key_exists($event, $this->events))
			{
				$this->events[$event] = new Event($event) ;
				$this->events[$event]->fired = true ;
			}
			else
			{
			
				$arg_arr = func_get_args() ;
				unset($arg_arr[0]) ;
			
				return call_user_func_array(array($this->events[$event], 'fire'), $arg_arr) ;
				//$this->events[$event]->fire($arg_arr) ;
			}
			
		}
		else
		{
			$arg_arr = func_get_args() ;
			return call_user_func_array(array(Core::$_self, 'fireEvent'), $arg_arr) ;
		}
	}
	
	/**
	 * Prevents the event from being fired again, despite being recharged.
	 * @param string $event
	 */
	function sealEvent($event)
	{
		if($this instanceof Core)
		{
			$this->events[$event]->seal() ;
		}
		else
		{
			return Core::$_self->sealEvent($event) ;
		}
	}
	
	/**
	 * Resets an event to its status previous to being fired. Enables it to be fired again.
	 * @param string $event The event to recharge
	 */
	function rechargeEvent($event)
	{
		if($this instanceof Core)
		{
			$this->events[$event]->recharge() ;
		}
		else
		{
			return Core::$_self->rechargeEvent($event) ;
		}
	}
	
	/**
	 * Works like rechargeEvent() but for all Events at once.
	 */
	function resetEvents()
	{
		if($this instanceof Core)
		{
			foreach($this->events as $event)
			{
				$event->recharge() ;
			}
		}
		else
		{
			return Core::$_self->rechargeEvent($event) ;
		}
	}
}