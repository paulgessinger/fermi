<?php
/**
 * Wrapper class for the Twig_Template
 * @author Paul Gessinger
 *
 */
class Template
{
	static $_autoInstance = false ;	
	protected $template_resource ;
	var $bind_array = array() ;
	protected $functions ;
	
	/**
	 * Takes the Template and copies global template binds to it.
	 * @param Template $template_resource The template resource that is to be used as a base.
	 * @param array $global_binds Array of values that are to be assigned to this template.
	 */
	function __construct($template_resource, Array $global_binds, Array $functions)
	{
		
		
	 	$this->template_resource = $template_resource ;
	 	$this->bind_array = $global_binds ;
	 	$this->functions = $functions ;
	 	
	}
	
	/**
	 * Binds a Key-Value pair specific to this template. Will NOT be accessible in other templates.
	 * @param string $key
	 * @param mixed $value
	 */
	function bind($key, $value)
	{
		$this->bind_array[$key] = $value ;
	}
	
	function __set($key, $value)
	{
		$this->bind($key, $value) ;
	}
	
	function __get($key)
	{
		if(array_key_exists($key, $this->bind_array))
		{
			return $this->bind_array[$key] ;
		}
	}
	
	/**
	 * Appends a string value to a currently set key. Scope is this Template.
	 * @param string $key The key the value is to be appended to.
	 * @param string $value The value that is to be appended. 
	 */
	function append($key, $value)
	{
		if(is_string($this->bind_array[$key]) OR !array_key_exists($key, $this->bind_array))
		{
			$this->bind_array[$key] .= $value ;
		}
		else
		{
			return false ;
		}
	}
	
	/**
	 * Prepends a string value to a currently set key. Scope is this Template.
	 * @param string $key The key the value is to be prepended to.
	 * @param string $value The value that is to be pepended. 
	 */
	function prepend($key, $value)
	{
		if(is_string($this->bind_array[$key]) OR !array_key_exists($key, $this->bind_array))
		{
			$this->bind_array[$key] = $value.$this->bind_array[$key] ;
		}
		else
		{
			return false ;
		}
	}
	
	/**
	 * Instructs the Template to render and return the HTML output.
	 */
	function render()
	{
		$tpl = new TplData($this->bind_array, $this->functions) ;
		
		
		$previous = ob_get_clean() ; // should be empty
		ob_start() ;
		
		include $this->template_resource ;
		$output = ob_get_clean();
		
		ob_start() ;
		echo $previous ; 
		
		return $output ;
	}
	
	function __toString()
	{
		return $this->render() ;	
	}
}