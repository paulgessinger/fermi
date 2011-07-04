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
	protected $appended_area_widgets = array() ;
	
	/**
	 * Registers default widgets.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function launch()
	{
		$this->_registerWidget('Text', 'widgets:text/input.phtml', 'widgets:text/output.phtml') ;
		$this->_registerWidget('Headline', 'widgets:headline/input.phtml', 'widgets:headline/output.phtml') ;
		$this->_registerWidget('Links', 'widgets:links/input.phtml', 'widgets:links/output.phtml') ;	
		
		Response::bindTemplateFunction('getWidgetArea', array($this, 'getWidgetArea')) ;
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $area 
	 * @return void
	 * @author Paul Gessinger
	 */
	function _getWidgetArea($area) 
	{
		$widget_area = Core::getModel('widgets:WidgetArea') ;
		
		if(!$widget_area->find('name=?', array($area)))
		{
			throw new ErrorException('Cannot load widget area "'.$area.'"') ;
		}
		
		$xml = new SimpleXMLElement((string)$widget_area->content) ;
		
		$widget_array = array() ;
		
		foreach($xml->widgets->widget as $widget_node)
		{
			$widget = Widgets::getWidget((string)$widget_node['type'], array('context' => $area)) ;
			$widget->fromXML($widget_node) ;
			
			array_push($widget_array, $widget->getOutput()) ;
		}
		
		if(isset($this->appended_area_widgets[$area]))
		{
			foreach($this->appended_area_widgets[$area] as $appended_widget)
			{
				array_push($widget_array, $appended_widget->setContext($area)->getOutput()) ;
			}
		}
		
		return implode('', $widget_array) ;
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $area 
	 * @param Array $widgets 
	 * @return void
	 * @author Paul Gessinger
	 */
	function _addWidgetsToArea($area, Array $widgets)
	{
		if(!isset($this->appended_area_widgets[$area]))
		{
			$this->appended_area_widgets[$area] = array() ;
		}
		
		$this->appended_area_widgets[$area] = array_merge($this->appended_area_widgets[$area], $widgets) ;
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
	function _getWidget($name, $options = array()) 
	{
		if(!isset($this->widgets[$name]))
		{
			throw new ErrorException('Widget "'.$name.'" could not be found.') ;
		}
		
		
		$widget = new FermiWidget($name, $this->widgets[$name]['input'], $this->widgets[$name]['output'], $options) ;
		
		return $widget ;
	}
	
}