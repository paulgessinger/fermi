<?php
ob_start();
try
{
	require 'core/classes/Core.php' ;
	require 'core/classes/Registry.php' ;

	Core::_launch() ;
	
	Core::_route() ;
	
}
catch(Exception $e)
{
	die('<strong>Uncatcheable error:</strong> '.$e.'<br /> <strong>Shutdown.</strong>') ;
}
ob_end_flush();