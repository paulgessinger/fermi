<?php

/**
 * The Structure controller allows editing the structure.
 *
 * @package Articles
 * @author Paul Gessinger
 */
class Structure extends FermiController
{
	
	
	function viewAction()
	{
		
		$tpl = Response::getTemplate('articles:admin/view.phtml') ;
		
		if($category_id = Request::get('id'))
		{
			
		}
		else
		{
			
			$categories = Core::getModel('articles:ArticleCategory')->getRootCategories() ;
			$articles = Core::getModel('articles:Article')->getRootArticles() ;
			
		}
		
		$tpl->bind('categories', $categories) ;
		$tpl->bind('articles', $articles) ;
		
		
		Response::bind('main', $tpl) ;
		Response::render() ;
	}
}