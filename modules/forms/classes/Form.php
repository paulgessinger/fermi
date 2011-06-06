<?php

class Form extends FermiObject
{
	var $elements = array() ;
	
	function __construct($name, $action, $method = 'POST')
	{
		$this->name = $name ;
		$this->action = $action ;
		$this->method = $method ;
		
	}
	
	function addElement($element, $name, $options = array())
	{	
		$array = array(
			'element' => $element,
			'options' => $options
		) ;
		
		$this->elements[$name] = $array ;
		
	}
	
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