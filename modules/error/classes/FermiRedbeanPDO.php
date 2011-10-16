<?php

class FermiRedbeanPDO extends RedBean_Driver_PDO
{



	public function GetAll($sql, $aValues = array())
	{
		Debug::addQuery($sql, $aValues) ;
		
		return parent::GetAll($sql, $aValues) ;
	}

	

	public function Execute($sql, $aValues = array())
	{	
		Debug::addQuery($sql, $aValues) ;
		
		return parent::Execute($sql, $aValues) ;
	}
}

