<?php

/**
 * Provides general and static helper methods.
 *
 * @package Core
 * @author Paul Gessinger
 */
abstract class Util
{
	
	static function array_to_ini($array)
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
	
	static function json_to_hash($object) {
		
		$return = array() ;
		
		$properties = get_object_vars($object) ;
		foreach($properties as $key => $value)
		{
			if(is_object($value))
			{
				if(get_class($value) == 'stdClass')
				{
					$value = Util::json_to_hash($value) ;
				}
			}
			
			$return[$key] = $value ;
		}
		
		return $return ;
		
	}
	
	static function json_format($json) 
	{ 
	    $tab = "	"; 
	    $new_json = "" ; 
	    $indent_level = 0; 
	    $in_string = false; 

	    /*$json_obj = json_decode($json); 

	    if($json_obj === false) 
	        return false; 

	    $json = json_encode($json_obj); */
	
	    $len = strlen($json); 

	    for($c = 0; $c < $len; $c++) 
	    { 
	        $char = $json[$c]; 
	        switch($char) 
	        { 
	            case '{': 
	            case '[': 
	                if(!$in_string) 
	                { 
	                    $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1); 
	                    $indent_level++; 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case '}': 
	            case ']': 
	                if(!$in_string) 
	                { 
	                    $indent_level--; 
	                    $new_json .= "\n" . str_repeat($tab, $indent_level) . $char; 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case ',': 
	                if(!$in_string) 
	                { 
	                    $new_json .= ",\n" . str_repeat($tab, $indent_level); 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case ':': 
	                if(!$in_string) 
	                { 
	                    $new_json .= ": "; 
	                } 
	                else 
	                { 
	                    $new_json .= $char; 
	                } 
	                break; 
	            case '"': 
	                if($c > 0 && $json[$c-1] != '\\') 
	                { 
	                    $in_string = !$in_string; 
	                } 
	            default: 
	                $new_json .= $char; 
	                break;                    
	        } 
	    } 

	    return $new_json; 
	}
	
}