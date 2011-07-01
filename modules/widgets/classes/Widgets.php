<?php
/*
 *
 * @author Paul Gessinger
 */

class Widgets extends FermiObject
{
	protected $widgets = array() ;
	protected $loaded_widgets = array() ;
	
	function launch()
	{
		$this->_registerWidget('Text', 'widgets:widgets/text/input.phtml', 'widgets:widgets/text/output.phtml') ;
		$this->_registerWidget('Headline', 'widgets:widgets/headline/input.phtml', 'widgets:widgets/headline/output.phtml') ;	
	}
	
	function _registerWidget($name, $input, $output) 
	{
		$this->widgets[$name] = array(
			'input' => $input,
			'output' => $output
		) ;
	}
	
	function _getWidget($name, $values = null) 
	{
		if(!isset($this->widgets[$name]))
		{
			throw new ErrorException('Widget "'.$name.'" could not be found.') ;
		}
		
		
		$widget = new FermiWidget($name, $this->widgets[$name]['input'], $this->widgets[$name]['output'], $values) ;
		
		return $widget ;
	}
	
}