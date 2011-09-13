<?php

/**
 * Model for Property.
 *
 * @package Core
 * @author Paul Gessinger
 */
class PropertyModel extends FermiModel 
{
	var $type = 'property' ;

	function __construct() {}
	
	function loadByName($name)
	{
		$this->find('name=?', array($name)) ;
		return $this ;
	}
	
	/**
	 * Validator for the Property data record.
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
			$tester = Core::getModel('core:Property') ;

			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('A property with this identifier already exists.') ;
			}
		}
		else
		{
			$tester = Core::getModel('core:Property') ;
			$tester->find('name=?', array($this->name)) ;
			if($tester->getId() !== $this->getId())
			{
				$this->addError('A property with this identifier already exists.') ;
			}
		}
		
		
		
	}
	
}