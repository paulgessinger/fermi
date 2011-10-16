<?php

/**
 * Model for Article data records.
 *
 * @package Articles
 * @author Paul Gessinger
 */
class ArticleModel extends FermiModel 
{
	var $type = 'article' ;
	var $bean = false ;
	
	function __construct() {}
	
	/**
	 * Sets an author for this article.
	 *
	 * @param UserModel $author The Author to be set.
	 * @return void
	 * @author Paul Gessinger
	 */
	function setAuthor(UserModel $author)
	{
		//return Database::link($this->bean, $author->bean) ;
		
		$this->author_id = $author->getId() ;
		
		return $this ;
	}
	
	function getAuthor()
	{
		return Core::getModel('core:User')->load($this->author_id) ;
	}
	
	function getParent() 
	{
		$parent = Core::getModel('articles:Article')->load($this->parent_id) ;
		
		return $parent ;
	}
	
	function setParent(ArticleModel $parent)
	{
		if($this->getId() === $parent->getId())
		{
			throw new ErrorException('Cannot set an article to be their parent.') ;
		}

		$this->parent_id = $parent->getId() ;

		return $this ;
	}
	
	function unsetParent()
	{
		$this->parent_id = NULL ;
	}
	
	function getSiblings()
	{
		$articles = Core::getModel('articles:Article')->getCollection()->find('parent_id=?', array($this->parent_id)) ;
		
		return $articles ;
	}
	
	function getChildren()
	{
		$articles = Core::getModel('articles:Article')->getCollection()->find('parent_id=?', array($this->getId())) ;

		return $articles ;
	}
	
	function addChild(ArticleModel $child)
	{
		$child->setParent($this) ;
		
		return $this ;
	}
	
	function removeChild(ArticleModel $child)
	{
		$child->unsetParent() ;
		
		return $this ;
	}
	
	function getRootArticles() 
	{
		return $this->getCollection()->find('parent_id IS NULL') ;
	}
	
	/**
	 * Validator for a article data record.
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
		
		
		// circle detection
		if($this->parent_id !== null)
		{
			$tester_array = array() ;
        
			$current = $this ;
        
			// safety measure to prevent infinite loop. Assumption is, that there will be no structure with a depth of 50
			$i = 0 ;
			while($i <= 50)
			{
				if($current->parent_id === null AND $current->getId() !== $this->getId())
				{
					break;
				}
        
				if(isset($tester_array[$current->getId()]))
				{
					$this->addError('Circles are not permitted in article tree.') ;
					break;
				}
        
        
				$tester_array[$current->getId()] = true ;
        
				$current = $current->getParent() ;
        
        
				$i++ ;
			}
        
		}
		
		if($this->isNew())
		{
			$tester = Core::getModel('articles:Article') ;
		
			if($tester->find('name=?', array($this->name)))
			{
				$this->addError('An article with this identifier already exists.') ;
			}
		}
		
	}
	
}