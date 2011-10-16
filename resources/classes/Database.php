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
			$mode = true ;
		}
		else
		{
			$mode = false ;
		}
		
		if($explicit = Registry::conf('db:frozen'))
		{
			if($explicit === 'true')
			{
				$mode = true ;
			}
			else
			{
				$mode = false ;
			}
			
		}
		
		try
		{
			
			/*$this->redbean = RedBean_Setup::kickstart(
				'mysql:host='.Registry::conf('db:host').';dbname='.Registry::conf('db:name').'', 
				Registry::conf('db:user'), 
				Registry::conf('db:pwd'), 
				$mode) ;
			
			$dbo = $this->redbean->getDatabaseAdapter() ;
			$dbo->exec("SET CHARACTER SET utf8") ; 
				
			R::configureFacadeWithToolbox($this->redbean) ;*/
			
			$pdo = new FermiRedbeanPDO(
				'mysql:host='.Registry::conf('db:host').';dbname='.Registry::conf('db:name').'', 
				Registry::conf('db:user'), 
				Registry::conf('db:pwd')
			);

			$adapter = new RedBean_Adapter_DBAdapter( $pdo );
			$writer = new RedBean_QueryWriter_MySQL( $adapter );
			$redbean = new RedBean_OODB( $writer );
			
			if($mode === true) 
			{
				$redbean->freeze(true);
			}
			
			$this->redbean = new RedBean_ToolBox( $redbean, $adapter, $writer );
			
			R::configureFacadeWithToolbox($this->redbean) ;
			
			R::$writer->setBeanFormatter(new FermiBeanFormatter()) ;
			RedBean_ModelHelper::setModelFormatter(new FermiModelFormatter());
			//R::debug( true );
			
			
			
			
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
	
	public function getAlias($type)
	{
		return parent::getAlias($type) ;
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
