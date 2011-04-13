<?php
$elements = array('interfaces', 'abstracts', 'agents', 'controllers', 'classes', 'models') ;

//$modules = new DirectoryIterator(SYSPATH.'modules/') ;

if(!$modules = Registry::get('modules'))
{
	throw new SystemException('Modules table was not found.') ;
}
array_multisort($modules) ;


foreach($modules as $module => $active)
{
	if(file_exists(SYSPATH.'modules/'.$module) AND $active)
	{	
		foreach($elements as $element)
		{
			if(file_exists(SYSPATH.'modules/'.$module.'/'.$element))
			{
				$subdir = new DirectoryIterator(SYSPATH.'modules/'.$module.'/'.$element) ;
				foreach($subdir as $subelement)
				{
					if(!$subdir->isDot())
					{
						if($subdir->isFile())
						{
							$c_name = substr($subelement, 0, strpos($subelement, '.')) ;
							
							include_once SYSPATH.'modules/'.$module.'/'.$element.'/'.$subelement ;
											

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
								case 'models':
										Core::$_models[] = $c_name ;
							}
						}
					}
				}
			}
		}
	}
}