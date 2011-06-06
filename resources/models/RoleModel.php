<?php

class RoleModel extends FermiModel 
{
	var $type = 'role' ;

	
	function __construct() 
	{
	}
	
	function addRight(RightModel $right)
	{
		if(!$right->getId())
		{
			return false ;
		}
		
		return R::associate($this->bean, $right->bean) ;
	}
	
	function removeRight(RightModel $right)
	{
		return R::unassociate($this->bean, $right->bean) ;
	}
	
	function getRights()
	{
		return new FermiCollection(Core::getModel('core:Right'), R::related($this->bean, 'right')) ;
	}
	
	function setParent(FermiModel $parent)
	{
		if(!$parent->getId())
		{
			return false ;
		}
		
		
		if(Database::attach($parent->bean, $this->bean))
		{
			return $this ;
		}
		else
		{
			return false ;
		}
	}
	
	function getParent()
	{
		if($this->bean->parent_id != null)
		{
			return Core::getModel('core:Role')->load($this->bean->parent_id) ;
		}
		else
		{
			return false ;
		}
	}
	
	function getChildren() 
	{
		$children = Database::children($this->bean) ;
		return new FermiCollection(Core::getModel('core:Role'), $children) ;
	}
	
	function validate() 
	{	
			
		if(empty($this->name))
		{
			$this->addError('Property "name" must not be empty.') ;
		}
		
		
		if($this->isNew())
		{
			$tester = Core::getModel('core:Role') ;
		
			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('A role with this identifier already exists.') ;
			}
		}
		
	}
	
}