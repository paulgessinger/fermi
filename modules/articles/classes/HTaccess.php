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
	 *	Checks if htaccess and mod_rewrite is enabled and overrides Request's default path parser and
	 *	path renderers. 
	 */
	function _launch()
	{
		if(Registry::get('default:htaccess') == true)
		{
			Core::get('Request')->setPathRenderer(function($agent, $controller, $action, $params)
			{
				if(count($params) > 1)
				{
					return $agent.'/'.$controller.'/'.$action.'/'.implode('/', $params) ;
				}
				
				if(count($params) == 0)
				{
					return $agent.'/'.$controller.'/'.$action ;
				}
				
				if(count($params) == 1)
				{
					return $agent.'/'.$controller.'/'.$action.'/'.implode('', $params).'.html' ;
				}
				
			}) ;
			
			
			
			Core::get('Request')->setPathParser(function($query)
			{
				$path['agent'] = false ;
				$path['controller'] = false ;
				$path['action'] = false ;
				$path['params'] = array() ;
				
				
				if(substr($query, strrpos($query, '.')) == '.html')
				{
					
					$query_array = explode('/', substr($query, 0, strrpos($query, '.'))) ;
					
					switch(count($query_array))
					{
						case 1:
							$path['params']['default'] = $query_array[0] ;
						break;
						case 2:
							$path['action'] = $query_array[0] ;
							$path['params']['default'] = $query_array[1] ;
						break;
						case 3:
							$path['controller'] = ucfirst($query_array[0]) ;
							$path['action'] = $query_array[1] ;
							$path['params']['default'] = $query_array[2] ;
						break;
						case 4:
							$path['agent'] = ucfirst($query_array[0]) ;
							$path['controller'] = ucfirst($query_array[1]) ;
							$path['action'] = $query_array[2] ;
							$path['params']['default'] = $query_array[3] ;
						break;
					}
					
				}
				else
				{
					$query_array = explode('/', $query) ;
					
					switch(count($query_array))
					{
						case 1:
							$path['agent'] = ucfirst($query_array[0]) ;
						break;
						case 2:
							$path['agent'] = ucfirst($query_array[0]) ;
							$path['controller'] = ucfirst($query_array[1]) ;
						break;
						case 3:
							$path['agent'] = ucfirst($query_array[0]) ;
							$path['controller'] = ucfirst($query_array[1]) ;
							$path['action'] = $query_array[2] ;
						break;
						default:

							$path['agent'] = ucfirst($query_array[0]) ;
							$path['controller'] = ucfirst($query_array[1]) ;
							$path['action'] = $query_array[2] ;

							if((($tokens-3)%2) == 0)
							{
								for($i=3; $i<$tokens; $i++)
								{
									$path['params'][$query_array[$i]] = $query_array[$i+1] ;			
									$i = $i+2 ;	
								}
							}

						break;
					}	
				}
				

				
				
				
				return $path ;
			}) ;
			
			
			HTML::registerHelper('sitelink', function($agent, $controller, $action, $site) {
				return SYSURI.''.$site.'.html' ;
			}) ;
			
			
			Response::bindTemplateFunction('article', function($site) { 
				return HTML::sitelink('Index', 'Articles', 'index', $site) ;
			}) ;
			
		}
	
	}
}