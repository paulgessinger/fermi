<?php

/**
 * Model for Article category data records.
 *
 * @package Articles
 * @author Paul Gessinger
 */
class ArticleCategoryModel extends FermiModel 
{
	var $type = 'category' ;
	var $bean = false ;
	
	function __construct() {}
	
	/**
	 * undocumented function
	 *
	 * @param ArticleModel $article 
	 * @return void
	 * @author Paul Gessinger
	 */
	function addArticle(ArticleModel $article)
	{
		$article->setCategory($this) ;
		$article->save() ;
		
		return $this ;
	}
	
	/**
	 * undocumented function
	 *
	 * @param ArticleModel $article 
	 * @return void
	 * @author Paul Gessinger
	 */
	function removeArticle(ArticleModel $article)
	{
		$article->unsetCategory() ;
		$article->save() ;
		
		return $this ;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getArticles()
	{
		$articles = Core::getModel('articles:Article')->getCollection()->find('category_id=?', array($this->getId())) ;
		
		return $articles ;
	}
	
	/**
	 * undocumented function
	 *
	 * @param ArticleCategoryModel $parent 
	 * @return void
	 * @author Paul Gessinger
	 */
	function setParent(ArticleCategoryModel $parent) 
	{	
		if($this->getId() === $parent->getId())
		{
			throw new ErrorException('Cannot set a category to be their parent.') ;
		}
		
		$this->parent_id = $parent->getId() ;
		
		return $this ;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function unsetParent()
	{
		$this->parent_id = null ;
		
		return $this ;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getParent()
	{
		if($this->parent_id === null)
		{
			return false ;
		}
		
		$parent = Core::getModel('articles:ArticleCategory')->load($this->parent_id) ;
		
		return $parent ;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function getChildren()
	{
		$children = Core::getModel('articles:ArticleCategory')->getCollection()->find('parent_id=?', array($this->getId())) ;
		
		return $children ;
	}
	
	function getSiblings()
	{
		$categories = Core::getModel('articles:ArticleCategory')->getCollection()->find('parent_id=?', array($this->parent_id)) ;
		
		return $categories ;
	}
	
	function getRootCategories() 
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
					$this->addError('Circles are not permitted in category tree.') ;
					break;
				}
				
				
				$tester_array[$current->getId()] = true ;
				
				$current = $current->getParent() ;
				
				
				$i++ ;
			}
			
		}
		
	}
	
}