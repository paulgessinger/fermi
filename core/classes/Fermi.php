<?php

/**
 * Wrapper for the Core and Registry calls.
 *
 * @package Core
 * @author Paul Gessinger
 */
class Fermi
{
	/**
	 * Includes Core and Registry and tells Core to launch.
	 *
	 * @author Paul Gessinger
	 */
	function __construct() 
	{
		try
		{
			ob_start() ;
			
			require 'core/classes/Core.php' ;
			require 'core/classes/Registry.php' ;

			Core::_launch() ;

			Core::_route() ;	
		}
		catch(Exception $e)
		{
			die('<strong>Uncatchable error:</strong><br/> <pre>'.$e.'</pre><br /> <strong>Shutdown.</strong>') ;
		}
			
	}
}