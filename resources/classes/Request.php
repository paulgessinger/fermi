<?php
/**
 * Request handles all Request variables that are available to php.
 * @author Paul Gessinger
 *
 */
class Request
{
	static $_autoInstance = true ;
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
		//$this->vars['session'] = $_SESSION ;
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
		
		
		$this->var_merged = array_merge($this->vars['server'], $this->vars['get'], $this->vars['post'], $this->vars['files'],
		$this->vars['cookie'], /*$this->vars['session'],*/ $this->vars['request'], $this->vars['env']) ;	
	
		
		
		
		
		$this->setPathParser(function($query){
		
			$get_vars = Core::get('Request')->vars['get'] ;
			
			$path['agent'] = $get_vars['agent'] ;
			unset($get_vars['agent']) ;
			
			$path['controller'] = $get_vars['controller'] ;
			unset($get_vars['controller']) ;
			
			$path['task'] = $get_vars['task'] ;
			unset($get_vars['task']) ;
			
			$path['params'] = $get_vars ;

						
			return $path ;
				
		}) ;
		
		
		$this->setPathRenderer(function($agent, $controller, $task, $params) {
			
			foreach($params as $param => $value)
			{
				$par .= '&'.$param.'='.$value ;
			}
			return 'index.php?agent='.$agent.'&controller='.$controller.'&task='.$task.''.$par ;
				
		}) ;

		
	}
	
	
	/**
	 * Shortcut for accessing POST values.
	 * @param string The key.
	 * @param string A Default value that is to be returned in case nothing is found.
	 * @return string
	 */
	function _post($key, $default = '')
	{
		return Request::_($key, $default, 'post') ;
	}
	
	/**
	 * Shortcut for accessing GET values.
	 * @param string The key.
	 * @param string A Default value that is to be returned in case nothing is found.
	 * @return string
	 */
	function _get($key, $default = '')
	{
		return Request::_($key, $default, 'get') ;
	}

	/**
	 * Uses the current pathParser to extract routing data from the Request values.
	 * @return array The Information on the query, that has been extracted by pathParser. 
	 */
	function getPath()
	{
		if($this instanceof Request)
		{
			return call_user_func_array($this->pathParser, array('query' => $this->query)) ;
		}
		else
		{
			return Core::get('Request')->getPath() ;
		}
	}
	
	/**
	 * Uses the current pathRenderer to create a callable path out of
	 * @param string $agent
	 * @param string $controller
	 * @param string $task
	 */
	function renderPath($agent, $controller, $task, $params = array())
	{
		if($this instanceof Request)
		{
			return call_user_func_array($this->pathRenderer, array('agent' => $agent, 'controller' => $controller, 'task' => $task, 'params' => $params)) ;
		}
		else
		{
			return Core::get('Request')->renderPath($agent, $controller, $task, $params) ;
		}
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
	function setPathRenderer($closure)
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
	function _($key, $default = '', $source = false)
	{
		if($this instanceof Request)
		{
			if($source)
			{
				if(isset($this->vars[$source][$key]))
				{
					return $this->vars[$source][$key] ;
				}
				elseif(!empty($default))
				{
					$this->vars[$source][$key] = $default ;
					return $default ;
				}
				else
				{
					return false ;
				}
			}
			else
			{
				if(isset($this->var_merged[$key]))
				{
					return $this->var_merged[$key] ;
				}
				elseif(!empty($default))
				{
					$this->var_merged[$key] = $default ;
					return $default ;
				}
				else
				{
					return false ;
				}
			}
		}
		else
		{
			return Core::get('Request')->_($key, $default, $source) ;
		}
	}
	
	/**
	 * Sets a Request value for later use.
	 * @param string The key
	 * @param string The content of the request value
	 * @param string Which type of request var is the value associated with.
	 * @return void
	 */
	function set($key, $value, $target = false)
	{
		if($this instanceof Request)
		{
			if($target)
			{
				$this->vars[$target][$key] = $value ;
			}
			else
			{
				$this->var_merged[$key] = $value ;
			}
		}
		else
		{
			return Core::get('Request')->set($key, $value, $target) ;
		}
	}
}