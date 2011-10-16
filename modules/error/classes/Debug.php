<?php
class Debug extends FermiObject
{
	var $queries = array() ;	
	
		
	function __construct()
	{
		if(Registry::conf('misc:debug') === 'true')
		{
			Core::addListener('onAfterRender', array($this, 'debugInit')) ;
		}
	}
	
	function _addQuery($sql, $values) 
	{
		$this->queries[] = array($sql, $values) ;
	}
	
	function debugInit()
	{
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round(($endtime - Core::$starttime)*1000, 2); 
		

		$tpl = Response::getTemplate('error:debug.phtml') ;
		$tpl->bind('queries', $this->queries) ;
		$tpl->bind('runtime', $totaltime) ;
		$tpl->bind('memory', memory_get_peak_usage(true)/1048576) ;
		
		
		
		echo $tpl ;
		//$tpl->embedPrepend('aux_js') ;
	}
}