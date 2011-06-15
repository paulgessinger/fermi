<?php

/**
 * Loads translations from all the modules and translates strings according to the locale specified.
 *
 * @author Paul Gessinger
 */
class I18n extends FermiObject
{
	var $locales = array() ;
	protected $locale = 'en_US' ;
	protected $localization_mode = false ;
	
	/**
	 * Loads the csv files, parses them and then merges each locale into one array.
	 * @todo Implement caching mechanism so we don't have to parse everything each and every time.
	 */
	function launch()
	{
		$localization_mode = Registry::conf('misc:localization_mode') ;
		if($localization_mode === 'true')
		{
			$this->localization_mode = true ;
		}
		elseif($localization_mode === 'false')
		{
			$this->localization_mode = false ;
		}
		
		foreach(Registry::_()->module_xml as $module => $xml)
		{
			if(isset($xml->locales))
			{
				foreach($xml->locales->children() as $locale => $empty)
				{
					if(!file_exists(Registry::getModule($module).'/locale/'.$locale.'.csv'))
					{
						throw new ErrorException('Could not find locale "'.$locale.'" in module "'.$module.'"') ;
					}
					
					$locale_data = array() ;
					
					$file = file(Registry::getModule($module).'/locale/'.$locale.'.csv') ;
					
					foreach($file as $row)
					{
						$csv = str_getcsv($row, ';', '"') ;
						$locale_data[$csv[0]] = utf8_encode($csv[1]) ;
					}
					
					if(!isset($this->locales[$locale]))
					{
						$this->locales[$locale] = $locale_data ;
					}
					else
					{
						$this->locales[$locale] = array_merge($this->locales[$locale], $locale_data) ;
					}
				}
			}
		}

	}
	
	/**
	 * Set the locale for I18n to translate strings into
	 * @param string $locale The locale you want to set.
	 */
	function _setLocale($locale)
	{
		if(!array_key_exists($locale, $this->locales))
		{
			throw new ErrorException('Trying to set locale "'.$locale.'" that is not available.') ;
		}
		
		$this->locale = $locale ;
	}
	
	/**
	 * Translates the given string into the locale specified in I18n.
	 * @param string $string The string you want to translate.
	 * @return string A translated string, or the original string, if no translation has been found.
	 */
	function __($string)
	{
		if(empty($string))
		{
			return '' ;
		}
		
		$string = trim($string) ;
	
		if(array_key_exists($string, $this->locales[$this->locale]))
		{
			return $this->locales[$this->locale][$string] ;
		}
		else
		{	
			if($this->localization_mode)
			{
				return '<input type="text" style="padding:0px;margin:0px;border:0px;width:auto;" onclick="return false;" value="'.$string.'"></input>' ;
			}
			
			
			return $string ;
		}
	}
}
