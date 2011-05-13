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
		
		$this->performAutoInstances() ;
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
				
								if(isset($xml->instances->instance))
								{
									foreach($xml->instances->instance as $instance)
									{
										$this->auto_instances[(string)$instance] = SYSPATH.'modules/'.$module.'/classes/'.$instance.'.php' ;
									}
								}
								
								if(isset($xml->controllers->controller))
								{
									foreach($xml->controllers->controller as $controller)
									{
										$this->controllers[(string)$controller] = SYSPATH.'modules/'.$module.'/classes/'.$controller.'.php' ;
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
							echo $e->getMessage() ;
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
		$this->reg = include_once SYSPATH.'core/registry.inc.php' ;
	}
	
	/**
	 * assemble the configuration
	 */
	private function buildConfig()
	{
		$this->conf_string = file_get_contents(SYSPATH.'/config.inc.php') ;
		$this->conf_string = substr($this->conf_string, 8) ;
		$this->conf = parse_ini_string($this->conf_string, true) ;
	}
	
	/**
	 * Includes all files mentioned in config section "libs"
	 */
	private function includeLibs()
	{
		foreach($this->conf('libs') as $lib)
		{
			$lib = '/'.$lib ;
			if(!file_exists(SYSPATH.'core/libs/'.$lib))
			{
				throw new Exception('<strong>'.SYSPATH.'core/libs'.$lib.'</strong> not found. It was configured in config.inc.php as a lib.') ;
			}
			else
			{
				include SYSPATH.'core/libs'.$lib ;
			}
			
		}
	}
	
	/**
	 * retrieves a value from the configuration.
	 * @param string $key The key to get, in form SECTION:KEY
	 */
	function conf($key)
	{
		if($this instanceof Registry)
		{
			$key_arr = explode(':', $key) ;
			
			if(array_key_exists($key, $this->conf))
			{
				return $this->conf[$key] ;
			}
			elseif(count($key_arr) == 1)
			{
				$sec = 'default' ;
				$item = $key_arr[0] ;
			}
			else
			{
				$sec = $key_arr[0] ;
				$item = $key_arr[1] ;
			}
			
		
			if(array_key_exists($sec, $this->conf))
			{
				if(array_key_exists($item, $this->conf[$sec]))
				{
					return $this->conf[$sec][$item] ;
				}
			}
		}
		else
		{
			return Registry::_()->conf($key) ;
		}
	}
	
	/**
	 * returns an instance of Registry.
	 */
	function _()
	{
		return Registry::$_self ;
	}
	
	
	/**
	 * retrieves a value from the registry.
	 * @param string $key The key to get, in form SECTION:KEY
	 */
	function get($key)
	{
		if($this instanceof Registry)
		{
		
			$key_arr = explode(':', $key) ;
			if(count($key_arr) == 1)
			{
				if(array_key_exists($key_arr[0], $this->reg))
				{
					foreach($this->reg[$key_arr[0]] as $key => $value)
					{
						if(!isset($this->unserialized[$key_arr[0]][$key]))
						{
							$this->unserialized[$key_arr[0]][$key] = unserialize($this->reg[$key_arr[0]][$key]) ;
						}
						
						$return[$key] = $this->unserialized[$key_arr[0]][$key] ;
					}
					
					return $return ;
					
				}
				else 
				{
					$sec = 'default' ;
					$item = $key_arr[0] ;
				}
				
			}
			else
			{
				$sec = $key_arr[0] ;
				$item = $key_arr[1] ;
			}
			
			
			if(array_key_exists($sec, $this->reg))
			{
				if(array_key_exists($item, $this->reg[$sec]))
				{	
					if(!isset($this->unserialized[$sec][$item]))
					{
						$this->unserialized[$sec][$item] = unserialize($this->reg[$sec][$item]) ;	
					}
					return $this->unserialized[$sec][$item] ;
				}
			}
			return false ;
			
			
		}
		else
		{
			return Registry::_()->get($key) ;
		}
	}
	
	/**
	 * Set a Registry value.
	 * @param $key The Key in form of SECTION:KEY
	 * @param $value The value.
	 */
	function set($key, $value)
	{
		if($this instanceof Registry)
		{
			

			$key_arr = explode(':', $key) ;
			if(count($key_arr) == 1)
			{
				$sec = 'default' ;
				$item = $key_arr[0] ;
			}
			else
			{
				$sec = $key_arr[0] ;
				$item = $key_arr[1] ;
			}
			
			$this->unserialized[$sec][$item] = $value ;
			
			$value = serialize($value) ;
						
			$this->reg[$sec][$item] = $value ;
			
			$this->modified = true ;
			
		}
		else
		{
			return Registry::_()->set($key, $value) ;
		}
	}
	
	/**
	 * permanently removes a key-value pair from the registry.
	 * @param string $key The Key to remove.
	 */
	function remove($key)
	{
		if($this instanceof Registry)
		{
			
			$key_arr = explode(':', $key) ;
			if(count($key_arr) == 1)
			{
			$sec = 'default' ;
			$item = $key_arr[0] ;
			}
			else
			{
				$sec = $key_arr[0] ;
				$item = $key_arr[1] ;
			}
			
			unset($this->reg[$sec][$item]) ;
			
			if(count($this->reg[$sec]) == 0)
			{
				unset($this->reg[$sec]) ;
			}	
			
			$this->modified = true ;
					
		}
		else
		{
			return Registry::_()->remove($key) ;
		}
	}
	
	/**
	 * Retrieves a class and creates an instance if there is none.
	 * USE Core::get()
	 * @param string $class Class to get.
	 */
	function getInstance($class)
	{
		if($this instanceof Registry)
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
		else
		{
			return Registry::_()->getInstance($class) ;
		}
	}
	
	
	/**
	 * rewrites registry.inc.php if anything was modified.
	 */
	function __destruct()
	{
		if($this->_modified === true)
		{
			$data = '<?php
' ;
			foreach($this->reg as $section => $keys)
			{
				foreach($keys as $key => $value)
				{
					$data .= '$registry[\''.$section.'\'][\''.$key.'\'] = \''.$value.'\' ;
' ;
				}
			}
			
		$data .= 'return $registry ;' ;
		
		file_put_contents(SYSPATH.'core/registry.inc.php', $data) ;
		
		}
	}
}