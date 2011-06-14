<?php

/**
 * Form class, that renders a form based on templates and fields that are given.
 *
 * @author Paul Gessinger
 */
class Form extends FermiObject
{
	var $elements = array() ;
	
	/**
	 * Create a new form.
	 *
	 * @param string $name The name of the form.
	 * @param string $action The action value of the form.
	 * @param string $method POST or GET, defaults to POST.
	 */
	function __construct($name, $action, $method = 'POST')
	{
		$this->name = $name ;
		$this->action = $action ;
		$this->method = $method ;
		
	}
	
	/** 
	 * Adds an element to the form. Element types need to be registered.
	 *
	 * @param string $element The element type, e.g. text
	 * @param string $name The name of the input element.
	 * @param array $options An array with additional options such as value.
	 */
	function addElement($element, $name, $options = array())
	{	
		$array = array(
			'element' => $element,
			'options' => $options
		) ;
		
		$this->elements[$name] = $array ;
		
	}
	
	/**
	 * Instructs the form to generate HTML and returns it.
	 * @return string HTML for form.
	 */
	function render()
	{
		
		$form_template = Response::getTemplate('forms:form.phtml') ;
		
		$form_template->bind('name', $this->name) ;
		$form_template->bind('action', $this->action) ;
		$form_template->bind('method', $this->method) ;
		
		$element_array = array() ;
		
		foreach($this->elements as $name => $details)
		{
			array_push($element_array, Forms::getElementTemplate($details['element'], $name, $details['options'])) ;
		}
		
		$form_template->bind('elements', $element_array) ;
		
		
		
		
		return $form_template ;
	}
	
	/**
	 * Uses an associative array to fill the form with values.
	 * @param array $array An associative array with key value pairs.
	 */
	function importPost($array = array())
	{
		foreach($array as $key => $value)
		{
			if(array_key_exists($key, $this->elements))
			{
				$this->elements[$key]['options']['value'] = $value ;
			}
		}
	}
}