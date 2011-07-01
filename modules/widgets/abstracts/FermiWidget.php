<?php

class FermiWidget 
{
	protected $template ;
	protected $name ;
	protected $input ;
	protected $output ;
	protected $values = array() ;
	
	function __construct($name, $input, $output, $values = null)
	{
		if(is_array($values))
		{
			$this->values = $values ;
		}
		
		$this->values['widget'] = $this ;
		
		$this->name = $name ;
		$this->input = $input ;
		$this->output = $output ;
	}
	
	function setValues($values) 
	{
		$this->values = array_merge($this->values, $values) ;
	}
	
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
	}
	
	function getInput()
	{
		$tpl = Response::getTemplate($this->input, $this->values) ;
		
		return $tpl ;
	}
	
	function getOutput()
	{
		$tpl = Response::getTemplate($this->output, $this->values) ;
		
		return $tpl ;
	}
}