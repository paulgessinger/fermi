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
	
	function setCategory(ArticleCategoryModel $category)
	{
		$this->category_id = $category->getId() ;
		
		return $this ;
	}
	
	function unsetCategory()
	{
		$this->category_id = null ;
		
		return $this ;
	}
	
	function getCategory()
	{
		$category = Core::getModel('articles:ArticleCategory')->load($this->category_id) ;
		
		return $category ;
	}
	
	function getSiblings()
	{
		$articles = Core::getModel('articles:Article')->getCollection()->find('category_id=?', array($this->category_id)) ;
		
		return $articles ;
	}
	
	function getRootArticles() 
	{
		return $this->getCollection()->find('category_id IS NULL') ;
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