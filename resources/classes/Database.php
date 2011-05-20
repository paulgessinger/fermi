<?php
/**
 * Database class. Utilises PDO to (mainly) connect to MySQL Servers and perform queries against them
 * @author Paul Gessinger
 *
 */
class Database
{
	static $_autoInstance = true ;
	var $_redbean ;
	
	/**
	 * Initiates Database connection utilizing Redbean
	 * @return void
	 */
	function __construct()
	{
		
	}
	
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
		

		$this->redbean = RedBean_Setup::kickstart(
			'mysql:host='.Registry::conf('db:host').';dbname='.Registry::conf('db:name').'', 
			Registry::conf('db:user'), 
			Registry::conf('db:pwd'), 
			$mode) ;
				
		R::configureFacadeWithToolbox($this->redbean) ;
		R::$writer->setBeanFormatter(new FermiBeanFormatter()) ;
		RedBean_ModelHelper::setModelFormatter(new FermiModelFormatter());
			
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
        return $table.'_id'; // append table name to id. The table should not inclide the prefix.
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
