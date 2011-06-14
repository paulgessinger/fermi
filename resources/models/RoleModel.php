<?php

/**
 * Model for Roles.
 *
 * @author Paul Gessinger
 */
class RoleModel extends FermiModel 
{
	var $type = 'role' ;

	/**
	 * Add a Right to this Role. Uses redbean relations.
	 * @param object RightModel $right The right to be added.
	 * @return boolean
	 */
	function addRight(RightModel $right)
	{
		if(!$right->getId())
		{
			return false ;
		}
		
		return R::associate($this->bean, $right->bean) ;
	}
	
	/** 
	 * Removes a right from this role.
	 * @param object RightModel $right The right to be removed.
	 * @return boolean
	 */
	function removeRight(RightModel $right)
	{
		return R::unassociate($this->bean, $right->bean) ;
	}
	
	/**
	 * Get a collection of all the rights assigned to this role.
	 * @return object FermiCollection The collection with the right models.
	 */
	function getRights()
	{
		return new FermiCollection(Core::getModel('core:Right'), R::related($this->bean, 'right')) ;
	}
	
	/**
	 * Set a new parent for this role. Replaces the existing one.
	 * @param object RoleModel $parent The parent that is to be set.
	 * @return boolean
	 */
	function setParent(RoleModel $parent)
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
	
	/**
	 * Get the parent of this Role.
	 * @return boolean / object RoleModel Returns false if role has no parent or the Parent's model.
	 */
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
	
	/**
	 * Get all children assigned to this Role.
	 * @return object FermiCollection A collection with all the children.
	 */
	function getChildren() 
	{
		$children = Database::children($this->bean) ;
		return new FermiCollection(Core::getModel('core:Role'), $children) ;
	}
	
	/**
	 * Performs basic check before saving this role.
	 */
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