<?php

/**
 * The Structure controller allows editing the structure.
 *
 * @package Articles
 * @author Paul Gessinger
 */
class Structure extends FermiController
{
	
	function indexAction()
	{
		$tpl = Response::getTemplate('articles:admin/index.phtml') ;
		
		$articles = Core::getModel('articles:Article')->getRootArticles() ;
		
		Admin::addAction(array(
			'label' => 'admin_action_add',
			'img' => SYSURI.Response::getSkinPath().'img/add.png',
			'href' => '#'
		)) ;
		
		$tpl->bind('root_articles', $articles) ;
		
		Response::bind('main', $tpl) ;
		Response::render() ;
	}
	
	function viewAction()
	{
		
		$tpl = Response::getTemplate('articles:admin/view.phtml') ;
		
		if($category_id = Request::get('id'))
		{
			$category = Core::getModel('articles:ArticleCategory')->load($category_id) ;
			$categories = $category->getChildren() ;
			$articles = $category->getArticles() ;
			
			// build breadcrump
			$i = 0 ;
			$current = $category ;
			$breadcrump = array() ;
			
			
			while($i <= 50)
			{
				$breadcrump[] = $current ;
				
				if(!($current = $current->getParent()))
				{
					break;
				}
				
				
				$i++ ;
			}
			
			$tpl->bind('breadcrump', array_reverse($breadcrump)) ;
			
			if(count($breadcrump) > 1)
			{
				$target = Core::getUrl('admin', 'structure', 'view', array('id' => $category->getParent()->getId())) ;
			}
			else
			{
				$target = Core::getUrl('admin', 'structure', 'view') ;
			}
			
			Admin::addAction(array(
				'label' => 'admin_action_back',
				'img' => SYSURI.Response::getSkinPath().'img/back.png',
				'href' => $target
			)) ;
			
			
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