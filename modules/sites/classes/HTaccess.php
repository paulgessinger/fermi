<?php
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
		if(Registry::get('htaccess') == true)
		{
			Core::get('Request')->setPathRenderer(function($agent, $controller, $task, $params)
			{
				return $agent.'/'.$controller.'/'.$task.'/'.implode('+', $params).'.html' ;
			}) ;
			
			
			
			Core::get('Request')->setPathParser(function($query)
			{
				
				$query = substr($query, 0, strrpos($query, '.')) ;
			
				$query_array = explode('/', $query) ;
				
				$path['agent'] = false ;
				$path['controller'] = false ;
				$path['action'] = false ;
				
				$tokens = count($query_array) ;
				
				switch($tokens)
				{
					case 1: // only one parameter given. loading default agent/controller/action and give that param to it
						$path['params']['default'] = $query_array[0] ;
					break;
					case 2:
						$path['action'] = $query_array[0] ;
						$path['params']['default'] = $query_array[1] ;
					break;
					case 3:
						$path['controller'] = $query_array[0] ;
						$path['action'] = $query_array[1] ;
						$path['params']['default'] = $query_array[2] ;
					break;
					case 4:
						$path['agent'] = $query_array[0] ;
						$path['controller'] = $query_array[1] ;
						$path['action'] = $query_array[2] ;
						$path['params']['default'] = $query_array[3] ;
					break;
					default:
					
						$path['agent'] = $query_array[0] ;
						$path['controller'] = $query_array[1] ;
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
			
				
				return $path ;
			}) ;
			
			
			HTML::registerHelper('sitelink', function($agent, $controller, $task, $site)
			{
				
				return SYSURI.''.$site.'.html' ;
			}) ;
			
			
		}
	
	}
}