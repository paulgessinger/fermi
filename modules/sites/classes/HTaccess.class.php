<?php
class HTaccess
{
	static $_autoInstance = true ;
	
	function __construct()
	{
		Core::addListener('onHTMLReady', array($this, '_launch')) ;
	}
	
	function _launch()
	{
		if(Registry::get('htaccess') == true)
		{
			Core::get('Request')->setPathRenderer(function($agent, $controller, $task, $params)
			{
				return $agent.'/'.$controller.'/'.$task.'/'.implode(',', $params).'.html' ;
			}) ;
			
			Core::get('Request')->setPathParser(function($query)
			{
				$query_array = explode('/', $query) ;
				
				if(count($query_array) == 1)
				{
					$path['agent'] 		= false ;
					$path['controller'] = false ;
					$path['task']		= false ;
					$path['params']		= array(substr($query_array[0], 0, strrpos($query_array[0], '.'))) ;
					
				}
				else
				{
				
					$path['agent'] 		= $query_array[0] ;
					$path['controller'] = $query_array[1] ;
					$path['task'] 		= $query_array[2] ;
				
					$params = substr($query_array[3], 0, strrpos($query_array[3], '.')) ;
					$params = explode(',', $params) ;
				
					if(count($params) != 1) // no guesswork to be done
					{
					
					}
				
					$path['params'] = $params ;
				}
				
				return $path ;
			}) ;
			
			HTML::registerHelper('sitelink', function($agent, $controller, $task, $site)
			{
				// bla bla bla get url format
				return SYSURI.$site.'.html' ;
			}) ;
			
		}
	
	}
}