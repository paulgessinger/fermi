<?php

/**
 * FermiWidget is the basis for all Widgets. It loads the correct templates, fills them with values, and returns the Template object.
 *
 * @package Widgets
 * @author Paul Gessinger
 */
class FermiWidget 
{
	protected $template ;
	protected $name ;
	protected $input ;
	protected $output ;
	protected $values = array() ;
	protected $context = '' ;
	
	/**
	 * Constructs the Object.
	 *
	 * @param string $name The name of the Widget.
	 * @param string $input The Template path to the template file to be used for input.
	 * @param string $output The Template path to the template file to be used for output.
	 * @param string $values An array of key -> values that are assigned to this widget right away
	 * @author Paul Gessinger
	 */
	function __construct($name, $input, $output, $options = array())
	{
		$this->options = $options ;
		
		if(isset($this->options['values']))
		{
			$this->values = $this->options['values'] ;
		}
		
		$this->values['widget'] = $this ;
		
		$this->name = $name ;
		$this->input = $input ;
		$this->output = $output ;
		
		
		
		if(isset($this->options['context']))
		{
			$this->context = $this->options['context'] ;
		}
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $context 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setContext($context)
	{
		$this->context = $context ;
		
		return $this ;
	}
	
	/**
	 * Merges a given associative array into the existing values array.
	 *
	 * @param Array $values An array of values to be merged.
	 * @return void
	 * @author Paul Gessinger
	 */
	function setValues(Array $values) 
	{
		$this->values = array_merge($this->values, $values) ;
		return $this ;
	}
	
	/**
	 * Extracts values from an xml node
	 *
	 * @param mixed $xml Either XML in string form, or a SimpleXMLElement. 
	 * @return void
	 * @author Paul Gessinger
	 */
	function fromXML($xml)
	{
		if(!($xml instanceof SimpleXMLElement))
		{
			$xml = new SimpleXMLElement($xml) ;
		}
		
		foreach($xml->values->value as $value)
		{
			$this->values[(string)$value['name']] = $value ;
		}
		
		return $this ;
	}
	
	/**
	 * Creates the required template and assigns the values to it. Then returns the Template.
	 *
	 * @return Template $tpl The Template belonging to the widget input.
	 * @author Paul Gessinger
	 */
	function getInput()
	{
		$tpl = $this->getTemplate($this->input) ;
		
		return $tpl ;
	}
	
	/**
	 * Creates the required template and assigns the values to it. Then returns the Template.
	 *
	 * @return Template $tpl The Template belonging to the widget output.
	 * @author Paul Gessinger
	 */
	function getOutput()
	{
		$tpl = $this->getTemplate($this->output) ;
		
		return $tpl ;
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $tpl 
	 * @return void
	 * @author Paul Gessinger
	 */
	private function getTemplate($tpl)
	{
		$array = explode(':', $tpl) ;
		$array[1] = $this->context.'/'.$array[1] ; 		
		
		$new_tpl = $array[0].':'.$array[1] ;
		if(Response::templateExists($new_tpl))
		{
			return Response::getTemplate($new_tpl, $this->values) ;
		}
		else
		{
			return Response::getTemplate($tpl, $this->values) ;
		}
	}
}