<?php

class UserModel extends FermiModel 
{
	var $type = 'user' ;

	
	function __construct() 
	{
	}
	
	
	function validate() 
	{	
			
		if(empty($this->name))
		{
			$this->addError('Property "name" must not be empty.') ;
		}
		
		if(empty($this->email))
		{
			$this->addError('Property "email" must not be empty.') ;
		}
		
		
		if($this->isNew())
		{
			$tester = Core::getModel('core:User') ;
		
			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('A user with this username already exists.') ;
			}
			
			
			$tester = Core::getModel('core:Role') ;
			if($tester->find('email=?', array($this->email)))
			{
				$this->addError('A user with this email already exists.') ;
			}
		}
		
	}
	
}