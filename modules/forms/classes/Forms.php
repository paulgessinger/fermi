<?php

class Forms extends FermiObject
{
	var $elements = array() ;
	
	function __construct() {}
	
	function launch()
	{
		
		// text field
		
		$this->_registerFormElement('text', function($name, $options) {
			
			$template = Response::getTemplate('forms:text.phtml') ;
			
			foreach($options as $key => $value)
			{
				$template->bind($key, $value) ;
			}
			$template->bind('name', $name) ;
			
			return $template ;
			
		}) ;
		
		// password field
		
		$this->_registerFormElement('password', function($name, $options) {
			
			$template = Response::getTemplate('forms:password.phtml') ;
			
			foreach($options as $key => $value)
			{
				$template->bind($key, $value) ;
			}
			$template->bind('name', $name) ;
			
			return $template ;
			
		}) ;
		
		// submit button
		
		$this->_registerFormElement('submit', function($name, $options) {
			
			$template = Response::getTemplate('forms:submit.phtml') ;
			
			foreach($options as $key => $value)
			{
				$template->bind($key, $value) ;
			}
			$template->bind('name', $name) ;
			
			return $template ;
			
		}) ;
		
		
	}
	
	function _registerFormElement($name, $function)
	{
		if(!is_callable($function))
		{
			throw new ErrorException('Cannot add form element, since function given is not callable.') ;
		}
		
		$this->elements[$name] = $function ;
	}

	function _getElementTemplate($element, $name, $options)
	{
		if(array_key_exists($element, $this->elements))
		{
			return $this->elements[$element]($name, $options) ;
		}
		else
		{
			throw new ErrorException('Unable to add Element "'.$element.'" for it does not exist.') ;
		}
	}


}