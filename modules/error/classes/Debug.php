<?php
class Debug
{
	static $_autoInstance = true ;
	
	function __construct()
	{

		if(Registry::conf('misc:debug') == true)
		{
			Core::addListener('onRender', array($this, 'debug')) ;
		}
	}
	
	function debug()
	{
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round(($endtime - Core::$starttime)*1000, 2); 

		$tpl = Response::getTemplate('error:debug.phtml') ;
		$tpl->bind('runtime', $totaltime) ;
		$tpl->bind('memory', memory_get_peak_usage(true)/1048576) ;
		
		
		
		$tpl->embedPrepend('aux_js') ;
	}
}