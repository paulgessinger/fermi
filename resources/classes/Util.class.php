<?php
/**
 * Provides general and static helper methods.
 * @author Paul Gessinger
 *
 */
abstract class Util
{
	
	function array_to_ini($array)
	{
		$secs_to_write = array() ;
		foreach($array as $section => $pairs)
		{
			$sec = '['.$section.']
' ;
			foreach($pairs as $key => $value)
			{
				$sec .= $key.' = '.$value.'
' ;
			}
			
			$secs_to_write[] = $sec ;
		}
		
		return implode('
', $secs_to_write) ;
	}
	
}