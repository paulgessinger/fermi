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
	
	var $_controllers ;
	var $_controller_instances ;
	var $_agents ;
	var $_agent_instances ;
	var $_models = array() ;
	var $events = array() ;
	var $launch_exception ;
	var $agent ;
	var $controller ;
	var $action = 'index' ;
	var $params ;
	
	
	protected $agent_instance ;
	
	protected $error_triggered = false ;
	
	
	/**
	 * registers error exception to have custom handling of all php errors
	 */
	function __construct()
	{		
		header("Content-type: text/html; charset=utf-8");
		
		error_reporting(-1);
	
		function exception_error_handler($errno, $errstr, $errfile, $errline) 
		{			
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline) ;
		}
		set_error_handler("exception_error_handler") ;
		
		spl_autoload_register(array($this, 'loader')) ;
	}
	
	
	private function loader($class)
	{
		$places = array('classes', 'abstracts', 'interfaces') ;
			
		foreach(Registry::$_modules as $module)
		{
			foreach($places as $place)
			{
				if(file_exists(SYSPATH.'modules/'.$module.'/'.$place.'/'.$class.'.php'))
				{
					include SYSPATH.'modules/'.$module.'/'.$place.'/'.$class.'.php' ;
					return true ;
				}
			}
		}
		
		foreach($places as $place)
			{
				if(file_exists(SYSPATH.'resources/'.$place.'/'.$class.'.php'))
				{
					include SYSPATH.'resources/'.$place.'/'.$class.'.php' ;
					return true ;
				}
			}
		

		throw new Exception('Class "'.$class.'" could not be found.') ;
	}
	
	public static function __callStatic($function, $arguments)
	{
		if(method_exists(get_called_class(), '_'.$function))
		{
			return call_user_func_array(array(Core::$_self, '_'.$function), $arguments) ;
		}
	}
	
	public function __call($function, $arguments)
	{
		if(method_exists($this, '_'.$function))
		{
			return call_user_func_array(array($this, '_'.$function), $arguments) ;
		}
	}
	
	
	/**
	 * Is called directly from index.php. It's the starting point of the system and instanciates Core itself
	 * as well as Registry.
	 */
	public static function _launch()
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
		Core::fireEvent('onAfterClassesReady') ;
		
		
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		Core::$starttime = $mtime ;
			
		/*foreach(Core::$_agents as $agent)
		{
			Core::$_agent_instances[$agent] = new $agent ;
		}
		
		foreach(Core::$_controllers as $controller)
		{
			Core::$_controller_instances[$controller] = new $controller ;
		}*/
		
		
		
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
	public function __route($force = false)
	{	
		try 
		{
			if(count(Registry::$_errors) != 0)
			{
				throw new RegistryException(implode('<br />', Registry::$_errors)) ;
			}
					
				
			//print_r($request) ;
				
			$this->agent = Registry::get('default_agent') ;
			$this->controller = Registry::get('default_controller') ;
				
			$rounds = array('action', 'controller', 'agent') ;
				
			foreach($rounds as $round)
			{
				if($round_value = Request::get($round))
				{
					$this->$round = $round_value ;		
				}
				else
				{
					Request::set($round, $this->$round) ;
				}
			}	
				
			//echo $this->agent.'/'.$this->controller.'/'.$this->action ;
			//print_r($request['params']) ;


			Core::fireEvent('onRoute') ;
				
			if(!isset(Core::$_registry->agents[$this->agent]))
			{
				throw new RoutingException('Agent "'.$this->agent.'" could not be retrieved.') ;
			}
			if(!isset(Core::$_registry->controllers[$this->controller]))
			{
				throw new RoutingException('Controller "'.$this->controller.'" could not be retrieved.') ;
			}
				

			include Core::$_registry->agents[$this->agent] ;
				
			$this->agent_instance =  new $this->agent ;
				
				
			$this->agent_instance->notify() ;
				
			Core::fireEvent('onAgentDispatch') ;
				
			include Core::$_registry->controllers[$this->controller] ;
			$this->agent_instance->dispatch(new $this->controller, $this->action) ;
				
				
				
		}
		catch(Exception $e)
		{	
			
			$default_agent = Registry::get('default_agent') ;
			if(get_class($this->agent_instance) != $default_agent)
			{
				include Core::$_registry->agents[$default_agent] ;
				$this->agent_instance = new $default_agent ;
			}
			
			Request::set('exception', $e) ;	
			
			include Core::$_registry->controllers['ErrorController'] ;
			$this->agent_instance->dispatch(new ErrorController, 'display') ;				
		}
	}
	
	
	/**
	 * returns an instance of Core, shortcut to Core::$_self
	 */
	static function _()
	{
		return Core::$_self ;
	}
	
	
	/**
	 * Sets the agent to the one provided in $agent. Must implement the interface Agent.
	 * @param Agent $agent The Agent to set.
	 */
	function _setAgent(Agent $agent)
	{
		$this->agent = $agent ;
	}
	
	/**
	 * Uses Registry to retrieve an instance of the class given in $class. Returns a new one if there is none.
	 * @param string $class The class to retrieve.
	 * @return object
	 */	
	function _get($class)
	{
		return $this->reg->getInstance($class) ;
	}
	
	function _getModel($model)
	{
		$arr = explode(':', $model) ;
			
		if(array_key_exists($arr[1], $this->_models))
		{
			$class = $arr[1].'Model' ;
			return new $class ;
		}
		else
		{
				
				
			if($arr[0] == 'core')
			{
				if(file_exists(SYSPATH.'resources/models/'.$arr[1].'Model.php'))
				{
					include SYSPATH.'resources/models/'.$arr[1].'Model.php' ;
						
					$this->_models[$arr[1]] = true ;
						
					$class = $arr[1].'Model' ;
					return new $class ;
				}
			}
				
				
			if($path = $this->reg->getModule($arr[0]))
			{
				if(file_exists($path.'models/'.$arr[1].'Model.php'))
				{
					include $path.'models/'.$arr[1].'Model.php' ;
				
					$this->_models[$arr[1]] = true ;
						
					$class = $arr[1].'Model' ;
					return new $class ;
				}
			}
				
				
				
			throw new ErrorException('Model "'.$model.'" could not be retrieved.') ;
				
		}
	}
	
	
	/**
	 * registers an event listener to an event specified
	 * @param string The Event you want to add the listener to
	 * @param string/array Either a string name of a function or an array containing both reference to an object and method name
	 * @return void
	 */
	function _addListener($event, $function)
	{
		if(!array_key_exists($event, $this->events))
		{
			$this->events[$event] = new Event($event) ;
		}
			
		$this->events[$event]->registerListener($function) ;
	}
	
	/**
	 * Fires an event executing all registered listeners
	 * @param string Event to be fired
	 * @return unknown_type All the returns of functions registered to the Event
	 */
	function _fireEvent($event)
	{
		if(!array_key_exists($event, $this->events))
		{
			$this->events[$event] = new Event($event) ;
			//$this->events[$event]->fired = true ;
		}
		else
		{
			
			$arg_arr = func_get_args() ;
			unset($arg_arr[0]) ;
			
			return call_user_func_array(array($this->events[$event], 'fire'), $arg_arr) ;
			//$this->events[$event]->fire($arg_arr) ;
		}
	}
	
	/**
	 * Prevents the event from being fired again, despite being recharged.
	 * @param string $event
	 */
	function _sealEvent($event)
	{
		$this->events[$event]->seal() ;
	}
	
	/**
	 * Resets an event to its status previous to being fired. Enables it to be fired again.
	 * @param string $event The event to recharge
	 */
	function _rechargeEvent($event)
	{
		$this->events[$event]->recharge() ;
	}
	
	/**
	 * Works like rechargeEvent() but for all Events at once.
	 */
	function _resetEvents()
	{
		foreach($this->events as $event)
		{
			$event->recharge() ;
		}
	}
}