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
			if(Registry::conf('misc:debug') == true) 
			{
				$mode = 'fluid' ;
			}
			else
			{
				$mode = 'frozen' ;
			}

			$this->redbean = RedBean_Setup::kickstart(
				'mysql:host='.Registry::conf('db:host').';dbname='.Registry::conf('db:name').'', 
				Registry::conf('db:user'), 
				Registry::conf('db:pwd'), 
				$mode) ;
				
			R::configureFacadeWithToolbox($this->redbean) ;
			R::$writer->setBeanFormatter(new FermiBeanFormatter()) ;
			
			
			/*if($site = SiteModel::find('name=?', array('indexx')))
			{
				$site->name = 'index' ;
				var_dump($site->save()) ;
			}*/
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




?>
