<?php
ob_start();
try
{
	require_once 'core/classes/Core.class.php' ;
	require_once 'core/classes/Registry.class.php' ;

	Core::_launch() ;
	
	Core::_route() ;
	
	Core::_render() ;
	
}
catch(Exception $e)
{
	die('<strong>Uncatcheable error:</strong> '.$e.'<br /> <strong>Shutdown.</strong>') ;
}
ob_end_flush();