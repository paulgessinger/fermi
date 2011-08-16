<?php

/**
 * Database class. Utilises Redbean to persist data.
 *
 * @package Core
 * @author Paul Gessinger
 */
class Database extends FermiObject
{
	protected $assocManager ;
	protected $linkManager ;
	protected $treeManager ;
	var $_redbean ;
	static $connection = false ;
	var $error ;
	
	function __construct() 
	{
		Core::addListener('onRoute', array($this, 'testConnection')) ;
	}
	
	function testConnection()
	{
		if(Database::$connection === false)
		{
			throw new DatabaseException('There has been an error establishing a Database connection:<br/>'.$this->error) ;
		}
	}
	
	/**
	 * Initiates Database connection utilizing Redbean
	 * @return void
	 */
	function launch()
	{
		if(Registry::conf('db:database') == false)
		{
			return true ;
		}
			
		if(Registry::conf('misc:debug') == true) 
		{
			$mode = 'fluid' ;
		}
		else
		{
			$mode = 'frozen' ;
		}
		
		if($explicit = Registry::conf('db:mode'))
		{
			$mode = $explicit ;
		}
		
		try
		{
			$this->redbean = RedBean_Setup::kickstart(
				'mysql:host='.Registry::conf('db:host').';dbname='.Registry::conf('db:name').'', 
				Registry::conf('db:user'), 
				Registry::conf('db:pwd'), 
				$mode) ;
			
			$dbo = $this->redbean->getDatabaseAdapter() ;
			$dbo->exec("SET CHARACTER SET utf8") ; 
				
			R::configureFacadeWithToolbox($this->redbean) ;
			R::$writer->setBeanFormatter(new FermiBeanFormatter()) ;
			RedBean_ModelHelper::setModelFormatter(new FermiModelFormatter());
			//R::debug( true );
				
			$this->linkManager = new RedBean_LinkManager($this->redbean);
			$this->assocManager = new RedBean_AssociationManager($this->redbean);
			$this->treeManager = new RedBean_TreeManager($this->redbean);
			
			Database::$connection = true ;
			
			Core::fireEvent('onDatabaseReady', array('database' => $this)) ;
		}
		catch(Exception $e)
		{
			Database::$connection = false ;
			$this->error = $e->getMessage() ;
			throw $e ;
 		}
	}
	
	/**
	 * Using Magic getters and setters to route calls through to specific redbean objects.
	 */
	public static function __callStatic($function, $arguments)
	{	
		$database = Core::get('Database') ;

		if(is_callable(array($database->linkManager, $function)))
		{
			return call_user_func_array(array($database->linkManager, $function), $arguments) ;
		}
		
		if(is_callable(array($database->assocManager, $function)))
		{
			return call_user_func_array(array($database->assocManager, $function), $arguments) ;
		}
		
		if(is_callable(array($database->treeManager, $function)))
		{
			return call_user_func_array(array($database->treeManager, $function), $arguments) ;
		}
		
		
		throw new ErrorException('Call to undefined method "'.$function.'" in class "'.get_class($this).'"') ;
		
	}
	

}

class FermiBeanFormatter implements RedBean_IBeanFormatter
{
    public function formatBeanTable($table)
    {
        return Registry::conf('db:prefix').'_'.$table;
    }
     
    public function formatBeanID( $table ) 
    {
        return $table.'_id'; // append table name to id. The table should not include the prefix.
    }
	
	static function _formatBeanId($table)
	{
		 return $table.'_id';
	}
	
}

class FermiModelFormatter implements RedBean_IModelFormatter
{
	public function formatModel($model)
	{
		return false ;
	}
}




?>
