<?php

/**
 * The Site controller shows the site specified.
 *
 * @package Articles
 * @author Paul Gessinger
 */
class Articles extends FermiController
{
	/**
	 * register the task index
	 */
	function __construct()
	{
		parent::__construct();
		
		Response::bindTemplateFunction('article', function($site) { 
			return HTML::sitelink('Index', 'Articles', 'index', $site) ;
		}) ;
		
		Response::bindTemplateFunction('link', function($agent, $controller, $task) { 
		
		}) ;
	}
	
	function modelAction()
	{
		
		$site = Core::getModel('article:Article')->find('name=?', array('index')) ;
		$user = Core::getModel('core:User')->find('name=?', array('paul')) ;
		
		$new_role = Core::getModel('core:Role') ;
		$new_role->name = 'super' ;
		$new_role->save() ;

		Response::render() ;
	}
	
	/**
	 * Retrieves the data belonging to the site requested and forwards it to the template
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function indexAction()
	{
		$article = 'index' ;
		
		if($param = Request::get('default'))
		{
			$article = $param ;
		}
		elseif($param = Request::get('article'))
		{
			$article = $param ;
		}
		
		Request::set('article', $article) ;

		
		$widget_array = array() ;


		if($article_db = Core::getModel('articles:Article')->find('name=?', array($article)))
		{
			$xml = new SimpleXMLElement($article_db->content) ;
			foreach($xml->widgets->widget as $widget_node)
			{
				$widget = Widgets::getWidget((string)$widget_node['type']) ;
				$widget->fromXML($widget_node) ;
				
				array_push($widget_array, $widget->getOutput()) ;
			}
			
			if(isset($xml->areas))
			{
				foreach($xml->areas->area as $area)
				{
					$area_widget_array = array() ;
				
					foreach($area->widget as $area_widget_node)
					{
						$area_widget = Widgets::getWidget((string)$area_widget_node['type']) ;
						$area_widget->fromXML($area_widget_node) ;
						array_push($area_widget_array, $area_widget) ;
					}
				
					Widgets::addWidgetsToArea((string)$area['name'], $area_widget_array) ;		
				}
			}
			
		}
		else
		{
			$widget_array[0] ='Whoop\'s, looks like this site doesn\'t exist.' ;
		}

		

		Response::bind('main', implode('', $widget_array)) ;
		
		

		
		
		//$article_tpl = Response::getTemplate('articles:page.phtml', array('text' => $this->article_content->text)) ;
		
		Response::render() ;
		
		/*$contents = array(
		'index' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
		',
		'hans' => 'Deine mutter'
		) ;
		
		if(isset($contents[$site]))
		{
			$this->site_content = Response::getTemplate('sites:page.phtml', array('text' => $contents[$site])) ;
		}
		else
		{
			$this->site_content = Response::getTemplate('sites:page.phtml', array('text' => 'Whoops, looks like this site doesn\'t exist.')) ;
		}*/
		
	}
}