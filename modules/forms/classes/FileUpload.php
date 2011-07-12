<?php

/**
 * undocumented class
 *
 * @package Forms
 * @author Paul Gessinger
 */
class FileUpload extends FermiObject
{
	protected $name ;
	protected $errors = array() ;
	protected $valid = true ;
	
	function __construct($name)
	{
		$this->name = $name ;
		
		$request = Core::get('Request') ;
		if(!isset($request->vars['files'][$name]))
		{
			throw new ErrorException('File specified is not available.') ;
			
		}
		
		$this->file = $request->vars['files'][$name] ;
		
		print_r($this->file) ;
	}
	
	function isExt($ext_array = array())
	{
		if(isset($this->errors['ext']))
		{
			return $this->errors['ext'] ;
		}
		
		
		foreach($ext_array as $extension)
		{
			$ext_given = substr($this->file['name'], strlen($this->file['name'])-strlen($extension)) ;
			
			if($ext_given === $extension)
			{
				return true ;
			}
		}
		
		$this->errors['ext'] = false ;
		$this->valid = false ;
		return false ;	
	}
	
	function isMime($mime_array)
	{
		foreach($mime_array as $mime)
		{
			if($this->file['type'] === $mime)
			{
				return true ;
			}
		}
		
		$this->errors['mime'] = false ;
		$this->valid = false ;
		return false ;
	}
	
	function maxSize($size)
	{
		$size_bytes = $size*1024 ;
		if($this->file['size'] < $size_bytes)
		{
			return true ;
		}
		else
		{
			$this->errors['size'] = false ;
			$this->valid = false ;
			return false ;
		}
	}
	
	
	function move($path, $overwrite = false)
	{
		if($this->valid === true)
		{
			$name = substr($this->file['name'], 0, strrpos($this->file['name'], '.')) ;
			$ori_name = $name ;
			$ext = substr($this->file['name'], strrpos($this->file['name'], '.')) ;
			
			if($overwrite === false)
			{
				$i = 1 ;
				while($i<100)
				{
					if(file_exists($path.$name.$ext))
					{
						$name = $ori_name.'-'.$i ;
					}
					else
					{
						break;
					}
				
					$i++;
				}
			}
			else
			{
				unlink($path.$name.$ext) ;
			}
			
			copy($this->file['tmp_name'], $path.$name.$ext) ;
			return true ;
		}
		else
		{
			return false ;
		}
	}
}
