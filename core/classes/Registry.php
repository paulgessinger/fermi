<?php
/**
 * Registry provides a storage solution for configuration and installation specific values
 * without resorting to a DB. Registry also acts as a non compulsory Singleton.
 * @author Paul Gessinger
 *
 */
class Registry
{
	static $_self ;
	static $_errors = array() ;
	static $_modules = array() ;
	var $_instances = array() ;
	var $auto_instances = array() ;
	var $includes = array() ;
	var $agents = array() ;
	var $controllers = array() ;
	static $reg ;
	
	protected $_modified = false ;
	protected $conf = array() ;
	
	/**
	 * Calculates certain values and includes all necessary php files.
	 */
	function __construct()
	{	
		Registry::$_self = $this ;
		
		$p = __FILE__ ;
		$p = str_replace('\\', '/', $p) ;
		$p = str_replace('core/classes/', '', $p) ;
		$SYSPATH = substr((string)$p, 0, strrpos((string)$p, '/')+1) ;
		
		$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $SYSPATH) ;
		$URI = 'http://'.$_SERVER['HTTP_HOST'].$relative_path ;
		
		
		
		define('SYSPATH', $SYSPATH) ;
		define('SYSURI', $URI) ;
		define('PATH', $relative_path) ;
		
		
		
		
		//echo $URI ;
		
		$this->buildConfig() ;
		$this->buildRegistry() ;
		
		$this->includeLibs();
		
		$this->initializeResources() ;
		
		$this->initializeModules() ;
		
		$this->performIncludes() ;
		
		$this->performAutoInstances() ;
		
	}
	
	/**
	 * Convenience Method fr creating pseudo-static methods. Static calls to Registry:: are now interpreted as calls to the singleton,
	 * and tried to map on corresponding methods.
	 */
	public static function __callStatic($function, $arguments)
	{
		if(method_exists(get_called_class(), '_'.$function))
		{
			return call_user_func_array(array(Registry::_(), '_'.$function), $arguments) ;
		}
	}
	
	/**
	 * Convenience Methods allowing pseude-static methods to be calles normally as well.
	 */
	public function __call($function, $arguments)
	{
		if(method_exists($this, '_'.$function))
		{
			return call_user_func_array(array($this, '_'.$function), $arguments) ;
		}
	}
	
	
	/**
	 * Checks if a Module is installed and loaded. Returns the path to the module.
	 *
	 * @param string $module The module name.
	 * @return string The path to the module.
	 */
	public function _getModule($module)
	{
		if(array_key_exists($module, Registry::$_modules))
		{
			return SYSPATH.'modules/'.$module.'/' ;
		}
		else
		{
			return false ;
		}
	}
	
	
	private function performIncludes()
	{
		foreach($this->includes as $path)
		{
			include $path ;
		}
	}

	private function performAutoInstances()
	{
		foreach($this->auto_instances as $class => $path)
		{
			include $path ;
			$this->_instances[$class] = $this->getInstance($class) ;
			if(method_exists($this->_instances[$class], 'launch'))
			{
				Core::addListener('onClassesReady', array($this->_instances[$class], 'launch')) ;
			}	
		}
	}
	
	private function initializeResources()
	{
		$raw = file_get_contents(SYSPATH.'resources/resources.xml') ;
		$xml = new SimpleXMLElement($raw) ;
		
		if(isset($xml->instances->instance))
		{
			foreach($xml->instances->instance as $instance)
			{
				$this->auto_instances[(string)$instance] = SYSPATH.'resources/classes/'.$instance.'.php' ;
			}
		}
		
		if(isset($xml->includes->include))
		{
			foreach($xml->includes->include as $include)
			{
				$this->includes[] = SYSPATH.'resources/'.$include ;
			}
		}
		
		if(isset($xml->agents->agent))
		{
			foreach($xml->agents->agent as $agent)
			{
				$this->agents[(string)$agent.'Agent'] = SYSPATH.'resources/agents/'.$agent.'.php' ;
			}
		}
		
	}
	
	private function initializeModules()
	{
		$errors = array() ;
		$directory = new DirectoryIterator(SYSPATH.'modules') ;
		foreach($directory as $module)
		{		
			if(!$module->isDot())
			{
				if($module->isDir())
				{
					if(file_exists(SYSPATH.'modules/'.$module.'/module.xml'))
					{
						try
						{
							$raw = file_get_contents(SYSPATH.'modules/'.$module.'/module.xml') ;
							$xml = new SimpleXMLElement($raw) ;
							if($xml->active == 'true')
							{							
								Registry::$_modules[(string)$module] = (string)$module ;
								
								$this->module_xml[(string)$module] = $xml ;
				
								if(isset($xml->includes->include))
								{
									foreach($xml->includes->include as $include)
									{
										$this->includes[(string)$include] = SYSPATH.'modules/'.$module.'/'.$include ;
									}
								}
								
								if(isset($xml->instances->instance))
								{
									foreach($xml->instances->instance as $instance)
									{
										$this->auto_instances[(string)$instance] = SYSPATH.'modules/'.$module.'/classes/'.$instance.'.php' ;
									}
								}
								
								if(isset($xml->controllers))
								{
									
									foreach($xml->controllers->children() as $agent_controller)
									{
										$agent_name = $agent_controller->getName() ;
										
										foreach($agent_controller->children() as $controller)
										{
											$this->controllers[ucfirst($agent_name).'Agent'][(string)$controller] = SYSPATH.'modules/'.$module.'/controllers/'.$controller.'.php' ;
										}
									}

								}
								
								if(isset($xml->agents->agent))
								{
									foreach($xml->agents->agent as $agent)
									{
										$this->agents[(string)$agent.'Agent'] = SYSPATH.'modules/'.$module.'/agents/'.$agent.'.php' ;
									}
								}
									
								if(isset($xml->rewrites->rewrite))
								{
									foreach($xml->rewrites->rewrite as $rewrite)
									{
										$this->rewrites[] = (string)$rewrite ;
									}
								}
							}
							
							
						}
						catch(Exception $e)
						{
							//echo $e->getMessage() ;
							Registry::$_errors[] = $e->getMessage() ;
						}
						
					}
				}
			}
		}
	}
	
	/**
	 * include registry file
	 */
	private function buildRegistry()
	{		
		$raw = json_decode(file_get_contents(SYSPATH.'core/registry.json')) ;
		
		Registry::$reg = Util::json_to_hash($raw) ;

	}
	
	/**
	 * rewrites registry.inc.php if anything was modified.
	 */
	function __destruct()
	{
		if($this->_modified === true)
		{
			file_put_contents(SYSPATH.'core/registry.json', Util::json_format(json_encode(Registry::$reg))) ;
		}
		
	}
	
	
	/**
	 * assemble the configuration
	 */
	private function buildConfig()
	{
		$this->conf_string = file_get_contents(SYSPATH.'/config.inc.php') ;
		$this->conf_string = substr($this->conf_string, 8) ;
		$this->conf = new SimpleXMLElement($this->conf_string) ;
	}
	
	/**
	 * Includes all files mentioned in config section "libs"
	 */
	private function includeLibs()
	{
		foreach($this->conf('libs') as $lib)
		{
			
			$path = SYSPATH.'core/libs/'.$lib->attributes()->name ;
			if(!file_exists($path))
			{
				throw new Exception('<strong>'.SYSPATH.'core/libs'.$lib.'</strong> not found. It was configured in config.inc.php as a lib.') ;
			}

			foreach($lib->file as $file)
			{
				if(file_exists(SYSPATH.'core/libs/'.$lib->attributes()->name.'/'.$file))
				{
					include SYSPATH.'core/libs/'.$lib->attributes()->name.'/'.$file ;
				}
				else
				{
						throw new Exception('"'.$file.'" was not found in <strong>'.SYSPATH.'core/libs'.$lib.'</strong>. It was configured in config.inc.php as a lib file.') ;
				}
			}
			
		}

	}
	
	/**
	 * retrieves a value from the configuration. Due to the config being stored in XML : corresponds to a level down the dom
	 * @param string $key The key to get
	 */
	function _conf($key)
	{
			
		$key_arr = explode(':', $key) ;

		if(count($key_arr) < 1)
		{
			throw new Exception('Unable to retrieve config information for "'.$key.'"') ;
		}
		
		$node = $this->conf ;
		
		foreach($key_arr as $level)
		{
			if(isset($node->$level))
			{
				$node = $node->$level ;
			}
			else
			{
				return false ;
			}
		}
		
		$children = $node->children() ;
		$attributes = $node->attributes() ;
		
		if(count($children) != 0) 
		{
			return $children ;
		}
		else
		{
			if(count($attributes) == 0)
			{
				return (string)$node ;
			}
			else
			{
				return $node ;
			}
		}
	}
	
	/**
	 * returns an instance of Registry.
	 */
	static function _()
	{
		return Registry::$_self ;
	}
	
	
	/**
	 * retrieves a value from the registry.
	 * @param string $key The key to get, in form SECTION:KEY
	 */
	function _get($key)
	{	
		$key_arr = explode(':', $key) ;

		if(count($key_arr) != 2)
		{
			return false ;
		}
		
		if(isset(Registry::$reg[$key_arr[0]][$key_arr[1]]))
		{
			return Registry::$reg[$key_arr[0]][$key_arr[1]] ;
		}
		
		
		return false ;
	}
	
	/**
	 * Set a Registry value.
	 * @param $key The Key in form of SECTION:KEY
	 * @param $value The value.
	 */
	function _set($key, $value)
	{
		$key_arr = explode(':', $key) ;
		
		if(count($key_arr) != 2)
		{
			return false ;
		}
			
		Registry::$reg[$key_arr[0]][$key_arr[1]] = $value ;
			
		$this->modified = true ;
	}
	
	/**
	 * permanently removes a key-value pair from the registry.
	 * @param string $key The Key to remove.
	 */
	function _remove($key)
	{
		$key_arr = explode(':', $key) ;
		if(count($key_arr) != 2)
		{
			return false ;
		}
			
		unset(Registry::$reg[$key_arr[0]][$key_arr[1]]) ;
			
		if(count(Registry::$reg[$key_arr[0]]) == 0)
		{
			unset(Registry::$reg[$key_arr[0]]) ;
		}	
			
		$this->modified = true ;

	}
	
	/**
	 * Retrieves a class and creates an instance if there is none.
	 * USE Core::get()
	 * @param string $class Class to get.
	 */
	function getInstance($class)
	{
		if(array_key_exists($class, $this->_instances))
		{
			return $this->_instances[$class] ;
		}
		else
		{
			$new_instance = new $class ;
						
			$this->_instances[$class] = $new_instance ;
						
			return $new_instance ;
		}
	}

}