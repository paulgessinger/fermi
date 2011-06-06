<?php

class UserModel extends FermiModel 
{
	var $type = 'user' ;
	var $test = 'leer' ;
	var $rights = array() ;
	
	function __construct() 
	{
	}
	
	function addRole(RoleModel $role)
	{
		return R::associate($this->bean, $role->bean) ;
	}
	
	function removeRole(RoleModel $role)
	{
		return R::unassociate($this->bean, $role->bean) ;
	}
	
	function getRoles()
	{
		return new FermiCollection(Core::getModel('core:Role'), R::related($this->bean, 'role')) ;
	}
	
	function loadRights()
	{
		$roles = $this->getRoles() ;
		$rights = array() ;
		
		foreach($roles as $role)
		{
			$rights = $this->extractRights($role) ;
		}
		
		$right_hierarchy = array() ;
		foreach($rights as $right) 
		{
			$path_array = explode('/', $right->path) ;
			$right_hierarchy[$path_array[0]][$path_array[1]][$path_array[2]] = true ;
		}
		
		$this->rights = $right_hierarchy ;
	}
	
	private function extractRights(RoleModel $role) 
	{
		$rights = $role->getRights();
		if($parent = $role->getParent())
		{
			$rights->merge($this->extractRights($parent)) ;
		}
		
		return $rights ;
	}
	
	function authorize($path)
	{
		$path_array = explode('/', $path) ;
		
		if(isset($this->rights['*']))
		{
			return true ;
		}
		elseif(isset($this->rights[$path_array[0]]))
		{
			if(isset($this->rights[$path_array[0]]['*']))
			{
				return true ;
			}
			elseif(isset($this->rights[$path_array[0]][$path_array[1]]))
			{
				if(isset($this->rights[$path_array[0]][$path_array[1]]['*']))
				{
					return true ;
				}
				elseif(isset($this->rights[$path_array[0]][$path_array[1]][$path_array[2]]))
				{
					return true ;
				}
			}
		}
		
		
		return false ;
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