<?php
/**
 * The Site controller shows the site specified.
 * @author Paul Gessinger
 *
 */
class Sites extends FermiController
{
	/**
	 * register the task index
	 */
	function __construct()
	{
		parent::__construct();
		
		Response::bindTemplateFunction('site', function($site) { 
			return HTML::sitelink('Delivery', 'Sites', 'index', $site) ;
		}) ;
		
		Response::bindTemplateFunction('link', function($agent, $controller, $task) { 
		
		}) ;
	}	
	
	/**
	 * retrieves the data belonging to the site requested and forwards it to the template
	 */
	function index($params)
	{
		$site = 'index' ;
		
		if(empty($params['site']))
		{
			if(!empty($params[0]))
			{
				$site = $params[0] ;
				
			}
		}
		else
		{
			$site = $params['site'] ;
		}
		
		Request::set('site', $site) ;

		$this->site_content = Response::getTemplate('sites:page.php') ;
		
		if($site_db = SiteModel::find('name=?', array($site)))
		{
			$this->site_content->text = $site_db->content ;
		}
		else
		{
			$this->site_content->text = 'Whoops, looks like this site doesn\'t exist.' ;
		}
		
		
		
		/*$contents = array(
		'index' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
		',
		'hans' => 'Deine mutter'
		) ;
		
		if(isset($contents[$site]))
		{
			$this->site_content = Response::getTemplate('sites:page.php', array('text' => $contents[$site])) ;
		}
		else
		{
			$this->site_content = Response::getTemplate('sites:page.php', array('text' => 'Whoops, looks like this site doesn\'t exist.')) ;
		}*/
		
	}
	
	/**
	 * embeds the template into the root_template
	 */
	function render()
	{
		$this->site_content->embed('main') ;
	}
}