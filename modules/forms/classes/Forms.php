<?php

/**
 * Forms singleton that is used to register Element types for forms.
 *
 * @package Forms
 * @author Paul Gessinger
 */
class Forms extends FermiObject
{
	var $elements = array() ;
	
	/**
	 * Is called onClassesReady and registers default element types.
	 */
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
		
		// textarea
		
		$this->_registerFormElement('textarea', function($name, $options) {
			
			$template = Response::getTemplate('forms:textarea.phtml') ;
			
			foreach($options as $key => $value)
			{
				$template->bind($key, $value) ;
			}
			$template->bind('name', $name) ;
			
			return $template ;
			
		}) ;
		
		// hidden
		
		$this->_registerFormElement('hidden', function($name, $options) {
			
			$proto = array(
				'value' => ''
			) ;
			
			$options = array_merge($proto, $options) ;
			
			return '<input type="hidden" name="'.$name.'" value="'.$options['value'].'" />' ;
			
		}) ;
		
		// file
		
		$this->_registerFormElement('file', function($name, $options) {
			
			$template = Response::getTemplate('forms:file.phtml') ;
			
			foreach($options as $key => $value)
			{
				$template->bind($key, $value) ;
			}
			$template->bind('name', $name) ;
			
			return $template ;
			
		}) ;
		
		
	}
	
	/** 
	 * Registers a form element with the singleton.
	 * @param string $name The name of the element type.
	 * @param closure / array $function A callable resource that returns HTML for an element.
	 */
	function _registerFormElement($name, $function)
	{
		if(!is_callable($function))
		{
			throw new ErrorException('Cannot add form element, since function given is not callable.') ;
		}
		
		$this->elements[$name] = $function ;
	}

	/**
	 * Is used to load HTML for a given element from the elements that are registered.
	 * @param string $element The element type.
	 * @param string $name The name of the input.
	 * @array options An array of additional options for the input.
	 */
	function _getElementTemplate($element, $name, $options = array() )
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