<?php

$elements = array('interfaces', 'abstracts', 'agents', 'controllers', 'classes') ;
foreach($elements as $element)
{
$subdir = new DirectoryIterator(SYSPATH.'resources/'.$element) ;
	foreach($subdir as $subelement)
	{
		if(!$subdir->isDot() AND $subdir->isFile() AND $subelement != '.DS_Store')
		{	
				$c_name = substr($subelement, 0, strpos($subelement, '.')) ;
				
				include_once SYSPATH.'resources/'.$element.'/'.$subelement ;	
				
				switch($element) 
				{
					case 'classes':
						Registry::$_autoQueue[] = $c_name ;
					break;
					case 'controllers':
						Core::$_controllers[] = $c_name ;
					break;
					case 'agents':
						Core::$_agents[] = $c_name ;
					break;
				}
		}
	}
}


