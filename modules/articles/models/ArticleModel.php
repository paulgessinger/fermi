<?php

class ArticleModel extends FermiModel 
{
	var $type = 'article' ;
	var $bean = false ;
	
	function __construct() 
	{
	}
	
	function setAuthor(UserModel $author)
	{
		//return Database::link($this->bean, $author->bean) ;
		
		return $this->author_id = $author->getId() ;
	}
	
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