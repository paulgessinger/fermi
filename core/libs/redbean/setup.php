<?php

include SYSPATH.'core/libs/redbean/rb.php' ;

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


R::setup('mysql:host='.Registry::conf('db:host').';dbname='.Registry::conf('db:name').'', Registry::conf('db:user'), Registry::conf('db:pwd'));
R::$writer->setBeanFormatter(new FermiBeanFormatter());


/*$user = R::dispense('user') ;
$user->name = 'hans' ;
$user->email = 'hans@email.de' ;
$user->pass = 'admin' ;
$user->salt = 'asd476' ;
R::store($user) ;*/

/*$role = R::dispense('role') ;
$role->parent = 0 ;
$role->name = 'default' ;
R::store($role) ;*/

$user = R::findOne('user', 'name=?', array('hans')) ;
$role = R::findOne('role', 'name=?', array('default')) ;

R::associate($role, $user) ;





//R::freeze(true);