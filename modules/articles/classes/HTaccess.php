<?php

/**
 * HTaccess registers path parser and renderer
 *
 * @package Articles
 * @author Paul Gessinger
 */
class HTaccess extends FermiObject
{
	static $_autoInstance = true ;
	
	function __construct()
	{
		Core::addListener('onHTMLReady', array($this, '_launch')) ;
	}
	
	/**
	 *	Checks if htaccess and mod_rewrite is enabled and overrides Request's default path renderer. 
	 */
	function _launch()
	{
		if(Registry::get('default:htaccess') == true)
		{
			Core::get('Request')->setPathRenderer(function($agent, $controller, $action, array $params = array()) {
				
				$return = $agent.'/'.$controller.'/'.$action.'/' ;
					
				$proto = array() ;
					
				foreach($params as $key => $value)
				{
					$proto[] = $key ;
					$proto[] = $value ;
				}
					
				$return .= implode('/', $proto) ;
					
				return $return ;
				
			}) ;
			
			
			HTML::registerHelper('sitelink', function($agent, $controller, $action, $site) {
				return SYSURI.''.$site.'.html' ;
			}) ;
			
		}
		else
		{
			HTML::registerHelper('sitelink', function($agent, $controller, $action, $site) {
				return SYSURI.'index.php/'.$site.'.html' ;
			}) ;
		}
		
		
		
		Response::bindTemplateFunction('article', function($site) { 
			return HTML::sitelink('Index', 'Articles', 'index', $site) ;
		}) ;
	
	}
}