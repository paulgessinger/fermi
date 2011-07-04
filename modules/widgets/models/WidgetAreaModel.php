<?php

/**
 * Model for WidgetArea.
 *
 * @package Core
 * @author Paul Gessinger
 */
class WidgetAreaModel extends FermiModel 
{
	var $type = 'widgetarea' ;
	
	function __construct() {}
	
	
	/**
	 * Validator for the model data record.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function validate() 
	{	
			
		if(empty($this->name))
		{
			$this->addError('Property "name" must not be empty.') ;
		}
		
		if($this->isNew())
		{
			$tester = Core::getModel('widgets:WidgetArea') ;
		
			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('An area with this name already exists.') ;
			}
		}
		
	}
	
}