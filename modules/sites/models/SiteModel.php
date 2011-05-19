<?php

class SiteModel extends FermiModel 
{
	var $type = 'site' ;
	
	function __construct() 
	{
		
	}
	
	function validate() 
	{
		$errors = array() ;
	
		
		if(empty($this->name))
		{
			$errors[] = 'Property "name" must not be empty.' ;
		}
		
		return $errors ;
	}
	
}