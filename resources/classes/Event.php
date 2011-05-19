<?php
/**
 * Serves as a wrapper for Events. 
 * @author Paul Gessinger
 *
 */
class Event 
{
	static $_autoInstance = false ;
	var $event_name ;
	var $fired = false ;
	var $listeners = array();
	var $sealed = false ;
	protected $return_val ;
	
	/**
	 * Takes the event name and stores it for later use.
	 * @param $event_name The name for the Event to be created.
	 */
	function __construct($event_name, $multiple = true)
	{
		$this->multiple = $multiple ;
		$this->event_name = $event_name ;
	}
	
	/**
	 * Returns true if this Event has already been fired.
	 * @return boolean True if Event has already been fired.
	 */
	
	
	function isFired()
	{
		return $this->fired ;
	}
	
	/**
	 * Prevents the event from being fired again despite being recharged.
	 */
	function seal()
	{
		$this->sealed = true ;
	}
	
	/** 
	 * Registers a listener with this Event.
	 * @param $function_resource This must be a function name, an array consisting of class or object and a method name
	 * or a closure. This will be called upon Event fire.
	 */
	function registerListener($function_resource)
	{
		if(is_callable($function_resource))
		{
			$this->listeners[] = $function_resource ;
		}
		else
		{
			throw new EventException('The function given is not callable.') ;
		}
	}
	
	/**
	 * Resets the Event and enables it to be fired again.
	 */
	function recharge()
	{
		$this->fired = false ;
	}
	
	/**
	 * Triggers the Event to fire. At this point all registered listeners are called, and their accumulated returns
	 * are returned.
	 */
	function fire()
	{
	
		if(!$this->fired AND !$this->sealed)
		{
			$arg_arr = func_get_args() ;
			//print_r($this->listeners) ;
			foreach($this->listeners as $listener)
			{
				$return_val .= call_user_func_array($listener, $arg_arr) ;
			}
			
			if($this->multiple === false)
			{
				$this->fired = true ;
			}
			
			return $return_val ;
		}
		elseif(!$this->sealed AND $this->fired)
		{
			throw new EventException('Event <strong>'.$this->event_name.'</strong> could not be fired twice') ;
		}
	}
	
}
