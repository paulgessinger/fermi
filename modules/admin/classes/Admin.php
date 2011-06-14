<?php

class Admin extends FermiObject 
{
	var $menu_xml ;
	
	function __construct() 
	{
	}
	
	function _loadMenu()
	{		
		$this->menu_xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><menu/>') ;
		
		foreach(Registry::_()->module_xml as $xml)
		{
			if(isset($xml->admin->menu))
			{
				foreach($xml->admin->menu->children() as $child)
				{
					if(!isset($this->menu_xml->{(string)$child->getName()}))
					{
					
						$new_child = $this->menu_xml->addChild($child->getName()) ;
		
						foreach($child->attributes() as $attribute => $value)
						{
							$new_child->addAttribute($attribute, $value) ;
						}
					
					}
					else
					{
						$new_child = $this->menu_xml->{(string)$child->getName()} ;
					}
					
					foreach($child->children() as $item)
					{
						$new_item = $new_child->addChild($item->getName(), $item) ;
						
						foreach($item->attributes() as $attribute => $value)
						{
							$new_item->addAttribute($attribute, $value) ;
						}
					}
				}
			}
		}
		
		
		//echo $this->menu_xml->asXML() ;
		
		
	}
	
	function _getSections() 
	{
		$sections = $this->menu_xml->children() ;
		$return = array() ;
		
		foreach($sections as $section)
		{
			$attributes = $section->attributes() ;
			$array = array(
				'target' => (string)$attributes->target,
				'label' => (string)$attributes->label
			) ;
			
			$return[(string)$section->getName()] = $array ;
		}
				
		return $return ;
	}
	
	function _getItems($section)
	{
		if(!($section_node = $this->menu_xml->$section))
		{
			return false ;
		}
		
		$items = $section_node->children() ;
		
		if(count($items) == 0)
		{
			return false ;
		}

		$return = array() ;
		foreach($items as $item)
		{
			$array = array(
				'target' => (string)$item,
				'label' => (string)$item->attributes()->label,
			) ;
			
			array_push($return, $array) ;
		}
				
		return $return ;
	}
}