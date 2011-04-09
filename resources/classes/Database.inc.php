<?php
/**
 * Database class. Utilises PDO to (mainly) connect to MySQL Servers and perform queries against them
 * @author Paul Gessinger
 *
 */
class Database
{
	static $_autoInstance = true ;
	
	/**
	 * Initiates Database connection utilizing PDO
	 * @return void
	 */
	function __construct()
	{
		
		
		
		
		if(Registry::conf('db:database') == false)
		{
			return false ;
		}
	
		$this->db_prefix = Registry::conf('db:prefix') ;
		
		$this->qrycount = 0 ;
		
 
		
		try
		{
			$pers = array();
			if(Registry::conf('db:persistent') == true)
			{
				$pers = array(PDO::ATTR_PERSISTENT => true) ;
			}			
			$this->pdo = new PDO('mysql:dbname='.Registry::conf('db:name').';host='.Registry::conf('db:host').'', Registry::conf('db:user'), Registry::conf('db:pwd'), $pers) ;
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e)
		{
			throw new DatabaseException(trim(substr($e->getMessage(), strrpos($e->getMessage(), ']')+1))) ;
		}
		
		$this->query("SET NAMES 'utf8'") ;
		Core::fireEvent('onDatabaseReady') ;
	}
	

	/**
	 * Prepares statement and returns a PDOStatement object
	 * @param string The SQL Statement
	 * @return object PDOStatement
	 */
	function prepare($stmnt)
	{
		if($this instanceof Database)
		{
			$result = $this->pdo->prepare($stmnt) ;
			$result->setFetchMode(PDO::FETCH_ASSOC) ;
			$this->qrycount++ ;
			return $result ;
		}
		else
		{
			return Core::get('Database')->prepare($stmnt) ;
		}
	}
	
	/**
	 * Creates a prepared statement, binds parameter set in $params, executes it and returns result if available
	 * @param string SQL Statement
	 * @param array An associative Array containing params to be set in the Statement and their values
	 * @return object DBResultSet
	 */	
	function prepared($stmnt, $params)
	{
		if($this instanceof Database)
		{
			$stmnt = str_replace("DBPREFIX", $this->db_prefix, $stmnt) ;
			$this->last_stmnt = $stmnt ;
			$result = Database::prepare($stmnt) ;
			$this->qrycount++ ;
			$result->execute($params);
			
			try
			{
				return new DBResultSet($stmnt, $result) ;
			}
			catch(PDOException $e) 
			{
				// query was not intended to have result set
			}
		}
		else
		{
			return Core::get('Database')->prepared($stmnt, $params) ; 
		}
	}
	
	/**
	 * Performs a query against the DB and returns result if available
	 * @param string SQL Statement
	 * @return object DBResultSet
	 */
	function query($stmnt, $cache = true) 
	{
		if($this instanceof Database)
		{
			/*if($cache AND QUERY_CACHE AND Cache::method() == 'memcache' AND !Core::isBackend())
			{
				// qry caching
				$stmnt = trim($stmnt) ;
				$op = strtoupper(substr($stmnt, 0, strpos($stmnt, ' '))) ;
				$stmnt_md5 = md5($stmnt) ;
				if($op == 'SELECT')
				{
					if($stored = Cache::get('qry_'.$stmnt_md5))
					{
						return $stored ;
					}
				}
			}
			
			$this->stmnts[] = $stmnt ;
			
			$stmnt = str_replace("DBPREFIX", $this->db_prefix, $stmnt) ;
			
			$this->last_stmnt = $stmnt ;
			
			$this->qc++ ;
			
			$result = $this->pdo->query($stmnt) ;
			
			try
			{
				if($op != 'INSERT' AND  $op != 'DELETE' AND $op != 'UPDATE' AND $op != 'SET') 
				{
					$return = new DBResultSet($stmnt, $result) ;
					if($cache AND $op == 'SELECT' AND QUERY_CACHE AND Cache::method() == 'memcache' AND !Core::isBackend())
					{
						Cache::set('qry_'.$stmnt_md5, $return, 30) ;
					}
					return $return ;
				}				
			}
			catch(PDOException $e) 
			{
				// query was not intended to have result set
			}
			return array() ;*/
		}
		else
		{
			return Core::get('Database')->query($stmnt, $cache) ;
		}
	}
	
	
	/**
	 * Uses PDO to retrieve the last auto_increment ID
	 * @return integer The last auto_increment ID
	 */
	function lastID()
	{
		if($this instanceof Database)
		{
			return intval($this->pdo->lastInsertId()) ;
		}
		else
		{
			return Core::get('Database')->lastID() ;
		}
	}
	
	/**
	 * Uses PDO to retrieve the number of rows affected by the last statement
	 * @return integer Number of affected Rows
	 */
	function numRows()
	{
		if($this instanceof Database)
		{
			if($this->recent_result instanceof PDOStatement)
			{
				return intval($this->recent_result->rowCount()) ;
			}
		}
		else
		{
			return Core::get('Database')->numRows() ;
		}
	}
	
	/**
	 * Uses PDO to escape a string for Database use
	 * @param string String to be escaped
	 * @return string The escaped String
	 */
	function escape($string)
	{
		if($this instanceof Database)
		{
			return $this->pdo->quote($string) ;
		}
		else
		{
			return Core::get('Database')->escape($string) ;
		}
	}	
	
}


/**
 * The DB Result set rearranges results from PDO into a more easy-to-use format allowing for direct access of first-column values and iteration of rows.
 * @author Paul Gessinger
 *
 */
class DBResultSet implements ArrayAccess, Iterator
{
	var $stmnt ;
	var $dbResult ;
	var $result ;
	var $fetch_rows = array() ;
	
	/**
	 * Takes the PDO result and builds result structure
	 * @param string The Original SQL Statement
	 * @param object The PDOStatement object
	 * @return void
	 */
	function __construct($stmnt, $dbResult)
	{
		$this->stmnt = $stmnt ;
		$this->dbResult = $dbResult ;
		$r = 0 ;
		$this->result = false ;
		$this->cols = array();
		
		try
		{
			$this->fetch_rows = $dbResult->fetchAll() ;
			$this->result = true ;
		}
		catch(PDOException $e) 
		{
		}
		
		$this->iterator = new ArrayIterator($this->fetch_rows) ;
	}
	
	/**
	 * Magic sleep function to enable db results to be cached.
	 * @return array Members to store.
	 */
	function __sleep()
	{
		return array('stmnt', 'result', 'fetch_rows', 'iterator') ;
	}
	
	/**
	 * Returns if there was a result from db.
	 * @return boolean
	 */
	function result()
	{
		return $this->result ;
	}
	
	/**
	 * Access a specific column
	 * @param string The column to be fetched
	 * @return string Content of the column
	 */
	function _($col)
	{
		return $this->fetch_rows[0][$col];
	}
	
	
	// for iterator
	public function rewind() 
	{
		$this->iterator->rewind();
	}
	 
	public function valid() 
	{
		return $this->iterator->valid() ;
	}
	 
	public function key() 
	{
		return $this->iterator->key();
	}
	 
	public function current() 
	{
		return $this->iterator->current() ;
	}
	 
	public function next() 
	{
		$this->iterator->next();
	}
	

	/**
	 * @param string Array offset
	 * @return boolean
	 */
	public function offsetExists($offset) 
	{
		if(is_array($this->fetch_rows[0]))
		{
			return array_key_exists($offset, $this->fetch_rows[0]);
		}
	}
	 
	/**
	 * @param string Array offset
	 * @return unkown_type
	 */
	public function offsetGet($offset) 
	{
		return $this->fetch_rows[0][$offset];
	}
	 
	/**
	 * @param string Array offset
	 * @return boolean
	 */
	public function offsetSet($offset, $value) 
	{	
		return false ;
	}
	
	/**
	 * @param string Array offset
	 * @return boolean
	 */
	public function offsetUnset($offset)
	{
		return false ;
	}
}





?>
