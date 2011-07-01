<?php

/**
 * Singleton for registering and retrieving Widgets.
 *
 * @package Widgets
 * @author Paul Gessinger
 */
class Widgets extends FermiObject
{
	protected $widgets = array() ;
	protected $loaded_widgets = array() ;
	
	/**
	 * Registers default widgets.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function launch()
	{
		$this->_registerWidget('Text', 'widgets:widgets/text/input.phtml', 'widgets:widgets/text/output.phtml') ;
		$this->_registerWidget('Headline', 'widgets:widgets/headline/input.phtml', 'widgets:widgets/headline/output.phtml') ;	
	}
	
	/**
	 * Registers a widget with the Singleton.
	 *
	 * @param string $name The name of the Widget.
	 * @param string $input Template path to the input template.
	 * @param string $output Template path to the output template.
	 * @return void
	 * @author Paul Gessinger
	 */
	function _registerWidget($name, $input, $output) 
	{
		$this->widgets[$name] = array(
			'input' => $input,
			'output' => $output
		) ;
	}
	
	/**
	 * Retrieves a widget if it was registered and returns an instance of it.
	 *
	 * @param string $name The name of the widget you want.
	 * @param array $values An array of values that are to be assigned to the widget right away
	 * @return FermiWidget $widget An instance of the Widget you requested, already filled with the values provided.
	 * @author Paul Gessinger
	 */
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