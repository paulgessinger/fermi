<?php

/**
 * The Structure controller allows editing the structure.
 *
 * @package Articles
 * @author Paul Gessinger
 */
class Structure extends FermiController
{
	
	function __construct() 
	{
		Response::addStylesheet(Registry::getModuleUri('articles').'structure.css') ;
	}
	
	function indexAction()
	{
		$user = Session::getUser() ;
		$prop = Core::getModel('core:Property')->find('name=?', array('structure_open_categories')) ;
		
		
		R::associate($user->bean, $prop->bean, array('value' => 'biatch')) ;

		
		$tpl = Response::getTemplate('articles:admin/index.phtml') ;
		
		$categories = Core::getModel('articles:ArticleCategory')->getRootCategories() ;
		$articles = Core::getModel('articles:Article')->getRootArticles() ;
		
		$tpl->bind('categories', $categories) ;
		$tpl->bind('articles', $articles) ;
		
		Response::bind('main', $tpl) ;
		Response::render() ;
	}
}