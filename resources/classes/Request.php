<?php

/**
 * Request handles all Request variables that are available to PHP.
 *
 * @package Core
 * @author Paul Gessinger
 */
class Request extends FermiObject
{
	protected $pathParser ;
	protected $pathRenderer ;
	
	/**
	 * Takes all Request and superglobal values and merges them to an additional array.
	 */
	function __construct()
	{
		$this->vars['server'] = $_SERVER ;
		$this->vars['get'] = $_GET ;
		$this->vars['post'] = $_POST ;
		$this->vars['files'] = $_FILES ;
		$this->vars['cookie'] = $_COOKIE ;
		$this->vars['request'] = $_REQUEST ;
		$this->vars['env'] = $_ENV ;
		
		if(ini_get('register_globals') == 1)
		{
			foreach($_GET as $key => $val)
			{
				if(isset($$key))
				{
					unset($$key) ;
				}
			}
			
			foreach($_POST as $key => $val)
			{
				if(isset($$key))
				{
					unset($$key) ;
				}
			}
		}
		
		$this->query = str_replace(PATH, '', $_SERVER['REQUEST_URI']) ;
		
		if(substr($this->query, 0, 10) === 'index.php/')
		{
			$this->query = substr($this->query, 10) ;
		}
		
		Core::addListener('onAfterClassesReady', array($this, 'getPath')) ;
		
		
		
		$this->setPathParser(function($query){
		
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
							
						$tokens = count($query_array) ;

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
		
		
		$this->setPathRenderer(function($agent, $controller, $action, $params) {
			
			$return = 'index.php/'.$agent.'/'.$controller.'/'.$action.'/' ;
			
			$proto = array() ;
			
			foreach($params as $key => $value)
			{
				$proto[] = $key ;
				$proto[] = $value ;
			}
			
			$return .= implode('/', $proto) ;
			
			return $return ;
				
		}) ;

		
	}
	
	
	/**
	 * Shortcut for accessing POST values.
	 * @param string The key.
	 * @param string A Default value that is to be returned in case nothing is found.
	 * @return string
	 */
	function _getPost()
	{
		if(count(Core::get('Request')->vars['post']) != 0)
		{
			return Core::get('Request')->vars['post'] ;
		}
		else
		{
			return false ;
		}
	}
	


	/**
	 * Uses the current pathParser to extract routing data from the Request values.
	 * @return array The Information on the query, that has been extracted by pathParser. 
	 */
	function _getPath()
	{
		$path = call_user_func_array($this->pathParser, array('query' => $this->query)) ;
		
		$values = array(
			'agent',
			'controller',
			'action'
		) ;
		
		foreach($values as $value)
		{
			
			if(isset($path[$value]))
			{
				$this->set($value, $path[$value]) ;
			}
			
		}
			
		foreach($path['params'] as $key => $value)
		{
			$this->set($key, $value) ;
		}
			
	}
	
	/**
	 * Uses the current pathRenderer to create a callable path out of
	 * @param string $agent
	 * @param string $controller
	 * @param string $action
	 */
	function _renderPath($agent, $controller, $action, $params = array())
	{
		return call_user_func_array($this->pathRenderer, array('agent' => $agent, 'controller' => $controller, 'action' => $action, 'params' => $params)) ;
	}
	
	/**
	 * Assigns a pathParser.
	 * @param $closure Callable resource
	 */
	function setPathParser($closure)
	{
		if(!is_callable($closure))
		{
			throw new SystemException('The resource given is not callable. Yet it must be to be able to act as pathParser.') ;
		}
		$this->pathParser = $closure ;
	}
	
	/**
	 * Assigns a pathRenderer
	 * @param $closure Callable resource
	 */
	function _setPathRenderer($closure)
	{
		if(!is_callable($closure))
		{
			throw new SystemException('The resource given is not callable. Yet it must be to be able to act as pathRenderer.') ;
		}
		$this->pathRenderer = $closure ;
	}
	
	/**
	 * Method for accessing all request data alike.
	 * @param string The key.
	 * @param string A Default value that is to be returned in case nothing is found.
	 * @param string Specifies where the value shall come from.
	 * @return string
	 */
	function _get($key)
	{
		if(isset($this->vars['get'][$key]))
		{
			return $this->vars['get'][$key] ;
		}
		else
		{
			return false ;
		}
	}
	
	/**
	 * Sets a Request value for later use.
	 * @param string The key
	 * @param string The content of the request value
	 * @param string Which type of request var is the value associated with.
	 * @return void
	 */
	function _set($key, $value)
	{
		$this->vars['get'][$key] = $value ;
	}
}