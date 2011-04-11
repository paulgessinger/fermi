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
	var $_instances = array() ;
	static $_autoQueue = array() ;
	protected $_modified = false ;
	protected $conf = array() ;
	protected $classnames = array() ;
	
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
		
		
		
		define(SYSPATH, $SYSPATH) ;
		define(SYSURI, $URI) ;
		define(PATH, $relative_path) ;
		
		
		
		
		//echo $URI ;
		
		$this->buildConfig() ;
		$this->buildRegistry() ;
		
		$this->includeLibs();
		//Registry::set('modules:error', '1') ;
		//Registry::set('modules:sites', '0') ;
		//Registry::set('modules:', '1') ;
		
		
		require_once SYSPATH.'resources/loader.inc.php' ;
		
		require_once SYSPATH.'modules/loader.inc.php' ;
		
		$this->runQueue() ;
		
		
	}
	
	/**
	 * Exectues the queue, instanciating all classes that have autoInstance set to true.
	 */
	private function runQueue()
	{
		/*
		 *  Round one, copy all classnames into storage and call _extendClass on everyone. 
		 *	First come first served, overwriting only works if desired parent class has already
		 *	been registered. Use alphabetic order.
		 */
		foreach(Registry::$_autoQueue as $class)
		{	
			$props = get_class_vars($class) ;

			$this->classnames[$class] = $class ;
			
			if(isset($props['_extendClass'])) 
			{
				$this->classnames[$props['_extendClass']] = $class ;
			}
		}
		
		
		/*
		 * Round two, do autoInstance 
		 */
		foreach(Registry::$_autoQueue as $class)
		{	
			$props = get_class_vars($class) ;
			
			
			if($props['_autoInstance'] === true) 
			{
				try
				{
					$instance = $this->getInstance($this->classnames[$class]) ;
					if(method_exists($instance, 'launch'))
					{
						Core::addListener('onClassesReady', array($instance, 'launch')) ;
					}
				}
				catch(Exception $e)
				{
					Core::_()->launch_exception = $e ;
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
				
				/*
				 * FUCK THIS
				 * We will now just get an instance of the class stored under the name requested. 
				 * If you want to extend a class, then use alphabetic order.
				 */
				
				if(array_key_exists($class, $this->classnames))
				{
					if(class_exists($this->classnames[$class]))
					{
						$new_instance = new $this->classnames[$class] ;
						
						$this->_instances[$class] = $new_instance ;
						$this->_instances[$this->classnames[$class]] = $new_instance ;
						
						return $new_instance ;
					}
				}
				
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
		if($this->modified === true)
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