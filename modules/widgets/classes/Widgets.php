<?php
/*
 *
 * @author Paul Gessinger
 */

class Widgets extends FermiObject
{
	protected $widgets ;
	
	function launch()
	{
			
	}
	
	function _registerWidget($name, $path) 
	{
		if(!file_exists($path))
		{
			throw new ErrorException('Cannot find widget at " '.$path.'"') ;
		}
	}
	
}