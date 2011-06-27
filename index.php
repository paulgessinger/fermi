<?php
try
{
	require 'core/classes/Core.php' ;
	require 'core/classes/Registry.php' ;

	Core::_launch() ;
	
	Core::_route() ;	
}
catch(Exception $e)
{
	die('<strong>Uncatchable error:</strong><br/> <pre>'.$e.'</pre><br /> <strong>Shutdown.</strong>') ;
}