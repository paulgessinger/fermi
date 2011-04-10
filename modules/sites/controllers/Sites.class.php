<?php
/**
 * The Site controller shows the site specified.
 * @author Paul Gessinger
 *
 */
class Sites extends FermiController
{
	/**
	 * register the task show
	 */
	function __construct()
	{
		$this->registerWith('Delivery') ;
		$this->registerTask('index') ;
		
		Response::bindTemplateFunction('site', function($site) { 
			
			/*if(Registry::get('htaccess') == false)
			{
				return Request::renderPath('Delivery', 'Sites', 'index', array('site' => $site)) ;
			}
			else
			{
				return $site.'.html' ;
			}*/
			
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
		
		if(empty($params['site']))
		{
			$site = $params[0] ;
			Request::set('site', $site) ;
		}
		else
		{
			$site = $params['site'] ;
		}
		
		

		$contents = array(
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
		}
		
	}
	
	/**
	 * embeds the template into the root_template
	 */
	function render()
	{
		$this->site_content->embed('main') ;
	}
}