<?php

/**
 * Provides general and static helper methods.
 *
 * @package Core
 * @author Paul Gessinger
 */
abstract class Util
{
	static function sanitize_url($str, array $replace=array(), $delimiter='-')
	{
		if( !empty($replace) )
		{
				$str = str_replace((array)$replace, ' ', $str);
		}
		
		
		$clean = str_replace('ü', 'ue', $str);
		$clean = str_replace('Ü', 'ue', $clean);
		
		$clean = str_replace('ö', 'oe', $clean);
		$clean = str_replace('Ö', 'oe', $clean);
		
		$clean = str_replace('ä', 'ae', $clean);
		$clean = str_replace('Ä', 'ae', $clean);
		
		$clean = str_replace('ß', 'ss', $clean);
		
		$clean = iconv('ISO-8859-1', 'ASCII//TRANSLIT', $clean);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}
	
	
	static function is_ie6()
	{
		$ie6 = false;
		$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($ua,'msie') !== FALSE) {
		    if(strpos($ua,'opera') == FALSE) {
		        if(preg_match('/(?i)msie [1-6]/',$ua)) $ie6 = true;
		    }
		}
		
		return $ie6 ;
	}
	
	
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