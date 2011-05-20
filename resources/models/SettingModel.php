<?php

class SettingModel extends FermiModel 
{
	var $type = 'setting' ;
	var $bean = false ;
	
	function __construct() 
	{
	}
	
	
	function validate() 
	{	
			
		if(empty($this->name))
		{
			$this->addError('Property "name" must not be empty.') ;
		}
		
		
		if($this->isNew())
		{
			$tester = Core::getModel('core:Setting') ;
		
			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('A setting with this identifier already exists.') ;
			}
		}
		
	}
	
}