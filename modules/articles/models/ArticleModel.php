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