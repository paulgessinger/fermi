<?php

class SiteModel extends FermiModel 
{
	var $type = 'site' ;
	var $bean = false ;
	
	function __construct() 
	{
	}
	
	function setAuthor(FermiModel $author)
	{
		return Database::link($this->bean, $author->bean) ;
	}
	
	function validate() 
	{	
			
		if(empty($this->name))
		{
			$this->addError('Property "name" must not be empty.') ;
		}
		
		
		if($this->isNew())
		{
			$tester = Core::getModel('sites:Site') ;
		
			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('A site with this identifier already exists.') ;
			}
		}
		
	}
	
}