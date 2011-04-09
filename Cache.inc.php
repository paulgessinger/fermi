<?php
/**
 * Provides a common facade for multiple caching methods such as APC, memcached, Flatfile or database.
 * @author Paul Gessinger
 *
 */
class Cache
{
	var $method ;
	var $sqlite ;
	static $_method ;
	
	static $_autoInstance = true ;

	/**
	 * Opens up the designated caching method's requirements. Priority will be apc->memcached->sqlite->db->file if force_method is set to false
	 * @param string optional A caching method to be forced
	 * @return void
	 */
	function __construct()
	{
		if(CACHE)
		{		
			if(!$this->method = Core::conf('cache:method'))
			{
				if(extension_loaded('apc'))
				{
					$prio[2] = 'apc' ;
				}
				if(extension_loaded('memcache'))
				{
					$prio[1] = 'memcache' ;
				}
				if(extension_loaded('sqlite'))
				{
					$prio[3] = 'sqlite' ;
				}
				
				$prio[4] = 'db' ;
				$prio[5] = 'file' ;
				
				for($i = 1; $i<=5; $i++)
				{
					if(isset($prio[$i]))
					{
						$this->method = $prio[$i] ;
						break;
					}
				}
			}
			/*else
			{
				$this->method = CACHE_METHOD ;
			}*/
			
			// opening connection to whatever
			$c = $this->method.'Cache' ;
			
			$ref = new ReflectionClass($c) ;
			if($ref->implementsInterface('CacheProvider'))
			{
				$this->cache_provider = new $c ;
				//echo 'using <b>'.$this->method.'</b><br/>' ;
			}
			else
			{
				throw new SystemException('Cache provider must implement interface CacheProvider') ;
			}
			Cache::$_method = $this->method ;
			Core::fireEvent('onCacheReady', $this->method) ;
		}
	}
	
	/**
	 * Returns the method currently used for caching
	 * @return void
	 */
	function method()
	{
		return Cache::$_method ;
	}
	
	/**
	 * Store a key value pair in the cache
	 * @param string The key the value can be accessed by
	 * @param mixed The value that is to be stored. Will be serialized.
	 * @return void
	 */
	function set($key, $value = false, $expire = 0)
	{
		if($this instanceof Cache)
		{
			if(!CACHE)
			{
				return false ;
			}
			
			return $this->cache_provider->set($key, $value, $expire) ;
		}
		else
		{
			return Core::get('Cache')->set($key, $value, $expire) ;
		}
	}
	
	/**
	 * Retrieve a value from the cache. Returns false if CACHE is disabled or the key does not exist
	 * @param string The key you want to access
	 * @return mixed
	 */
	function get($key)
	{
		if($this instanceof Cache)
		{
			if(!CACHE)
			{
				return false ;
			}

			return $this->cache_provider->get($key) ;
		}
		else
		{
			return Core::get('Cache')->get($key) ;
		}
	}
	
	/**
	 * Entirely clears the cache
	 * @return void
	 */
	function clear()
	{
		if($this instanceof Cache)
		{
			return $this->cache_provider->clear() ;
		}
		else
		{
			return Core::get('Cache')->clear();
		}
	}
}

/**
 * An interface to ensure that the CacheProvider meets the requirements of our class.
 * Is explicitly checked for in Cache.
 * @author Paul Gessinger
 *
 */
interface CacheProvider
{
	/**
	 * Retrieve a value from the cache. Returns false if CACHE is disabled or the key does not exist
	 * @param string The key you want to access
	 * @return mixed
	 */
	function get($key);
	
	/**
	 * Store a key value pair in the cache
	 * @param string The key the value can be accessed by
	 * @param mixed The value that is to be stored. Will be serialized.
	 * @return void
	 */
	function set($key, $value = false, $expire = 0);
	
	/**
	 * Entirely clears the cache
	 * @return void
	 */
	function clear();
}

class FileCache implements CacheProvider
{
	function __construct()
	{
		
		$this->path = SYSPATH.'cache/file_cache/' ;
		if(!file_exists($this->path))
		{
			mkdir($this->path) ;
		}
	}
	
	function get($key)
	{
		$k = md5($key) ;
		if(Fsys::file_exists($this->path.$k.'.php'))
		{
			$val = include $this->path.$k.'.php' ;
			if($row['expire'] != 0)
			{
				if(mktime() <= $val['expire'])
				{
					Util::removeResource($this->path.$k.'.php') ;
					return false ;
				}
				else
				{
					return unserialize(stripslashes($val['value'])) ;
				}
			}
			else
			{
				return unserialize(stripslashes($val['value'])) ;
			}
		}
		else
		{
			return false ;
		}
	}
	
	function set($key, $value = false, $expire = 0)
	{
		$k = md5($key) ;
		Util::removeResource($this->path.$k.'.php') ;
		if($value)
		{
			if($expire != 0)
			{
				$expire = mktime()+$expire ;
			}
			
			$to_file = '<?php
' ;
			
			$to_file .= '$array[\'key\'] = \''.$key.'\' ;
$array[\'value\'] = \''.addslashes(serialize($value)).'\' ;
$array[\'expire\'] = '.$expire.' ;

return $array ;' ;
			
			Fsys::create($this->path.$k.'.php', $to_file) ;
		}
	}
	
	function clear()
	{
		Util::removeResource($this->path) ;
		mkdir($this->path) ;
	}
}


/**
 * Provides Cache storage inside the Database that is currently in use with the system.
 * Values are only unserialized on request to minimize overhead.
 * @author Paul Gessinger
 *
 */
class DbCache implements CacheProvider
{
	function __construct()
	{
		foreach(Sql::query('SELECT * FROM DBPREFIXcache', false) as $row)
		{
			if($row['expire'] != 0)
			{
				if(mktime() <= $row['expire'])
				{	
					$this->cached[$row['key']] = $row['value'] ; 
				}
				else
				{
					Sql::query('DELETE FROM DBPREFIXcache WHERE `key` = "'.$row['key'].'"') ;
				}
			}
			else
			{
				$this->cached[$row['key']] = $row['value'] ; 
			}
			
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#get($key)
	 */
	function get($key)
	{
		if(isset($this->cached[$key]))
		{
			if(!isset($this->unserialized[$key]))
			{
				$this->unserialized[$key] = $key ;
				$this->cached[$key] = unserialize($this->cached[$key]) ;
			}
			return $this->cached[$key] ;
		}
		else
		{
			return false ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#set($key, $value, $expire)
	 */
	function set($key, $value = false, $expire = 0)
	{
		if($expire != 0)
		{
			$expire = mktime()+$expire ;
		}
		Sql::query('DELETE FROM DBPREFIXcache WHERE `key` = "'.$key.'"') ;
		unset($this->cached[$key]) ;
		if($value)
		{
			Sql::prepped("INSERT INTO DBPREFIXcache (`key`, `value`, `expire`) 
			VALUES (:kkey, :value, :eexpire)",
			array(':kkey' => Util::remesc($key), ':value' => serialize($value), ':eexpire' => $expire)) ;
			$this->cached[$key] = $value ;
			$this->unserialized[$key] = $value ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#clear()
	 */
	function clear()
	{
		Sql::query('TRUNCATE TABLE `DBPREFIXcache`') ;
	}
}

/**
 * Uses APC to provide caching
 * @author Paul Gessinger
 *
 */
class ApcCache implements CacheProvider
{
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#get($key)
	 */
	function get($key)
	{
		$fetch = apc_fetch($key) ;
		if(empty($fetch))
		{
			return false ;
		}
		else
		{
			return $fetch ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#set($key, $value, $expire)
	 */
	function set($key, $value = false, $expire = 0)
	{
		if(empty($value))
		{
			return apc_delete($key) ;
		}
		else
		{
			return apc_add($key, $value, $expire) ;
		}
	}
	
	function clear()
	{
		return apc_clear_cache() ;
	}
}

/**
 * Uses memcached to provide caching.
 * @author Paul Gessinger
 *
 */
class MemcacheCache implements CacheProvider
{
	function __construct()
	{
		Memcache::connect('localhost') ;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#get($key)
	 */
	function get($key)
	{
		$return = Memcache::get($key) ;
		if(empty($return))
		{
			return false ;
		}
		else
		{
			return $return ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#set($key, $value, $expire)
	 */
	function set($key, $value = false, $expire = 0)
	{
		if(empty($value))
		{
			return Memcache::delete($key) ;
		}
		else
		{
			if($expire >= 2592000)
			{
				$expire = 2591999 ;
			}
			if(!Memcache::get($key))
			{
				return Memcache::add($key, $value, false, $expire) ;
			}
			else
			{
				return Memcache::replace($key, $value, false, $expire) ;
			}	
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#clear()
	 */
	function clear()
	{
		return Memcache::flush() ;
	}
}

/**
 * Uses an Sqlite Database located in /cache/cache.db to provide cache. 
 * Values are only unserialized on request to minimize overhead.
 * @author Paul Gessinger
 *
 */
class SqliteCache implements CacheProvider
{
	function __construct()
	{
		if(!file_exists(SYSPATH.'cache/cache.db'))
		{
			$new = true ;
		}
		if($this->sqlite = sqlite_factory(SYSPATH.'cache/cache.db', 0666, $error))
		{
			if($new)
			{
				$this->sqlite->query('create table cache (key varchar(32) unique primary key,value text, expire integer(10));', SQLITE_ASSOC, $error1) ;
			}
					
			$result = $this->sqlite->query('SELECT * FROM cache', SQLITE_ASSOC, $error1) ;
			foreach($result->fetchAll() as $row)
			{
				if($row['expire'] != 0)
				{
					if(mktime() <= $row['expire'])
					{	
						$this->cached[$row['key']] = $row['value'] ;
					}
					else
					{
						$this->sqlite->query('DELETE FROM cache WHERE key = "'.$row['key'].'"', SQLITE_ASSOC, $error1) ;
					}
				}	
				else
				{
					$this->cached[$row['key']] = $row['value'] ;
				}	
			}
		}
		else
		{
			echo $error ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#get($key)
	 */
	function get($key)
	{
		if(isset($this->cached[$key]))
		{
			if(!isset($this->unserialized[$key]))
			{
				$this->unserialized[$key] = $key ;
				$this->cached[$key] = unserialize($this->cached[$key]) ;
			}
			return $this->cached[$key] ;
		}
		else
		{
			return false ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#set($key, $value, $expire)
	 */
	function set($key, $value = false, $expire = 0)
	{
		if($expire != 0)
		{
			$expire = mktime()+$expire ;
		}
		$this->sqlite->query('DELETE FROM cache WHERE key = "'.$key.'"', SQLITE_ASSOC, $error1) ;
		unset($this->cached[$key]) ;
		if($value)
		{
			$this->sqlite->query("INSERT INTO cache (key, value, expire) VALUES ('".Util::remesc($key)."', '".sqlite_escape_string(serialize($value))."', '".$expire."')", SQLITE_ASSOC, $error1) ;
			$this->cached[$key] = $value ;
			$this->unserialized[$key] = $value ;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see resources/core/CacheProvider#clear()
	 */
	function clear()
	{
		if(isset($this->cached))
		{
			foreach($this->cached as $key => $value)
			{
				$this->set($key) ;
			}
		}
	}
}


?>