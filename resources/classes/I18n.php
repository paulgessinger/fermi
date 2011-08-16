<?php

/**
 * Loads translations from all the modules and translates strings according to the locale specified.
 *
 * @package Core
 * @author Paul Gessinger
 */
class I18n extends FermiObject
{
	var $locales = array() ;
	var $locale_details = array() ;
	protected $locale = '' ;
	protected $localization_mode = false ;
	
	/**
	 * Loads the csv files, parses them and then merges each locale into one array.
	 * @todo Implement caching mechanism so we don't have to parse everything each and every time.
	 */
	function launch()
	{
		$this->loadLocalesFromModules() ;
		$this->loadLocaleDetails() ;
		$this->setLocale('de_DE') ;
		
		try
		{
			date_default_timezone_set(Registry::conf('misc:timezone')) ;
		}
		catch(Exception $e)
		{
			throw new ErrorException('Unable to set timezone from config file, timezone is "'.Registry::conf('misc:timezone').'"') ;
		}
		
	}
	
	private function loadLocaleDetails()
	{
		foreach($this->locales as $locale => $content)
		{
			if(file_exists(SYSPATH.'resources/locale/'.$locale.'.xml'))
			{
				$xml = new SimpleXMLElement(file_get_contents(SYSPATH.'resources/locale/'.$locale.'.xml')) ;
				$this->locale_details[$locale]['xml'] = $xml ;
			
				// months 
				
				foreach($xml->months->month as $month)
				{
					$this->locale_details[$locale]['months'][(int)$month['no']]['long'] = (string)$month['long'] ;
					$this->locale_details[$locale]['months'][(int)$month['no']]['short'] = (string)$month['short'] ;
				}
			}
		}
	}
	
	private function loadLocalesFromModules()
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
					if(!file_exists(SYSPATH.Registry::getModule($module).'/locale/'.$locale.'.csv'))
					{
						throw new ErrorException('Could not find locale "'.$locale.'" in module "'.$module.'"') ;
					}
					
					$locale_data = array() ;
					
					$file = file(SYSPATH.Registry::getModule($module).'/locale/'.$locale.'.csv') ;
					
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
	
	
	function _getMonthLong($month_no) 
	{
		if($month_no < 1 OR $month_no > 12)
		{
			throw new ErrorException('Invalid month number. 1 < x < 12');
		}
		
		if(isset($this->locale_details[$this->locale]['months'][$month_no]))
		{
			return $this->locale_details[$this->locale]['months'][$month_no]['long'] ;
		}
	}
	
	function _getMonthShort($month_no) 
	{
		if($month_no < 1 OR $month_no > 12)
		{
			throw new ErrorException('Invalid month number. 1 < x < 12');
		}
		
		if(isset($this->locale_details[$this->locale]['months'][$month_no]))
		{
			return $this->locale_details[$this->locale]['months'][$month_no]['short'] ;
		}
	}
	
	function _getDate()
	{
		return $this->locale_details[$this->locale]['xml']->date_format ;
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
				return "<input type='text' style='padding:0px;margin:0px;border:1px solid red;width:auto;background-color:white;color:black;' onclick='return false;' value='".$string."'></input>" ;
			}
			
			
			return $string ;
		}
	}
}
