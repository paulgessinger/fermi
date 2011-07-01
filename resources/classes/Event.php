<?php

/**
 * Serves as a wrapper for Events.
 *
 * @package Core
 * @author Paul Gessinger
 */
class Event extends FermiObject
{
	var $event_name ;
	var $fired = false ;
	var $listeners = array();
	var $sealed = false ;
	var $arguments = array() ;
	var $multiple = false ;
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
	
	function setArguments(array $arguments)
	{
		$this->arguments = $arguments ;
	}
	
	function __get($key) 
	{
		return $this->arguments[$key] ;
	}
	
	function __set($key, $value) 
	{
		return $this->arguments[$key] = $value ;
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

			foreach($this->listeners as $listener)
			{
				call_user_func_array($listener, array($this)) ;
			}
			
			if($this->multiple === false)
			{
				$this->fired = true ;
			}
			
			return true ;
		}
		elseif(!$this->sealed AND $this->fired)
		{
			throw new EventException('Event <strong>'.$this->event_name.'</strong> could not be fired twice') ;
		}
	}
	
}
