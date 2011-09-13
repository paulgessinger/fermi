<?php

/**
 * Model for User.
 *
 * @package Core
 * @author Paul Gessinger
 */
class UserModel extends FermiModel 
{
	var $type = 'user' ;
	var $test = 'leer' ;
	var $rights = array() ;
	
	function __construct() {}
	
	function getProperty($property)
	{
		/*if(!($property instanceof PropertyModel))
		{
			$property = Core::getModel('core:Property')->loadByName($property) ;
		}*/
	}
	
	function setProperty($property, $value)
	{
		if(!($prop = Core::getModel('core:Property')->loadByName($property)))
		{
			throw new ErrorException('Property "'.$property.'" does not exist.') ;
		}
		
		$relation = R::findOrDispense('user_property', 'user_id=? AND property_id=?', array($this->getId(), $prop->getId())) ;
		$relation = array_shift($relation) ;
		
		/*echo '<pre>' ;
		print_r($relation) ;*/
		
		
		
		$relation->value = $value ;
		$relation->user_id = $this->getId() ;
		$relation->property_id = $prop->getId() ;
		
		
		
		
		R::store($relation) ;
	}
	
	
	/**
	 * Adds a role to the user.
	 *
	 * @param RoleModel $role The role that is to be assigned to this user.
	 * @return boolean True on success.
	 * @author Paul Gessinger
	 */
	function addRole(RoleModel $role)
	{
		return R::associate($this->bean, $role->bean) ;
	}
	
	/**
	 * Removes a role from this user
	 *
	 * @param RoleModel $role The role that is to be removed
	 * @return boolean True on success.
	 * @author Paul Gessinger
	 */
	function removeRole(RoleModel $role)
	{
		return R::unassociate($this->bean, $role->bean) ;
	}
	
	/**
	 * Returns a FermiCollection of all roles that are assigned to this user
	 *
	 * @return object FermiCollection The collection containing all the roles.
	 * @author Paul Gessinger
	 */
	function getRoles()
	{
		return new FermiCollection(Core::getModel('core:Role'), R::related($this->bean, 'role')) ;
	}
	
	/**
	 * Iterates over all the roles and retrieves rights that are assigned to them.
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
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
	
	/**
	 * Internal recursive method for traversing up the role hierarchy in order to implement right inheritance.
	 *
	 * @param RoleModel $role The role whose ancestors are to be traversed over.
	 * @return object FermiCollection A collection containing all the rights.
	 * @author Paul Gessinger
	 */
	private function extractRights(RoleModel $role) 
	{
		$rights = $role->getRights();
		if($parent = $role->getParent())
		{
			$rights->merge($this->extractRights($parent)) ;
		}
		
		return $rights ;
	}
	
	/**
	 * Authorizes an ACL against this user.
	 *
	 * @param string $path The ACL path that is to be tested.
	 * @return boolean True on right exists, false if not.
	 * @author Paul Gessinger
	 */
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
	
	/**
	 * Validator for the role model data record.
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