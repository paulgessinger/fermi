<?php

/**
 * Central manager of everything sent to the client.
 *
 * @package Core
 * @author Paul Gessinger
 */
class Response extends FermiObject
{
	var $template_dir ;
	var $compile_dir ;
	protected $root_template = false ;
	var $bind_array = array() ;
	var $skin = false ;
	protected $accumulated ;
	protected $functions = array() ;
	
	/**
	 * Registers a listener to onAgentDispatch.
	 */
	function __construct()
	{
		Core::addListener('onDatabaseReady', function() {
			
			if(!Response::getSkin())
			{
				/**
				 * get default skin
				 * @todo implement fetching from DB, as soon as we have DB
				 */
				$skin = Core::getModel('core:Setting')->find('name=?', array('skin')) ;
				Response::setSkin($skin->value) ;
			}
			
		}) ;
		
		
		
		$this->bindTemplateFunction('link', function ($target, $params = array()) {
			
			$array = explode('/', $target) ;
			if(count($array) != 3)
			{
				throw new Exception('Invalid path format') ;
			}
			
			return HTML::link($array[0], $array[1], $array[2], $params) ;
			
		})	;
		
		$this->bindTemplateFunction('subtemplate', function ($tpl) {
			
			return Response::getTemplate($tpl) ;
			
		})	;
	}	

	/**
	 * Makes Response ready to deliver content to the client.
	 */
	function _prepareResponse()
	{	
		
		Core::fireEvent('onResponseGoingHot') ;
		
		if($this->root_template === false)
		{
			$this->root_template = 'index.phtml' ;
		}

		
			
		
		$this->template_dir = SYSPATH.'skins/'.$this->skin.'/' ;
				
		$this->bind_array['syspath'] = SYSPATH ;
		$this->bind_array['sysuri'] = SYSURI ;
		$this->bind_array['skin'] = SYSURI.'skins/'.$this->skin.'/' ;
		$this->bind_array['jquery'] = '<script type="text/javascript" src="'.SYSURI.'core/libs/js/jquery/jquery.js"></script>' ;
		$this->bind_array['aux_js'] = '' ;
		$this->bind_array['aux_head'] = '' ;
				
		if(!isset($this->bind_array['title']))
		{
			if($title = Core::getModel('core:Setting')->find('name=?', array('pagetitle')))
			{
				$this->bind_array['title'] = $title->value ;
			}
		
		}
		
		

		
	}
	
	/**
	 * Set a new root template that should be used to construct the output.
	 * @param string $template The root template. make sur it exists.
	 */
	function _setRootTemplate($template)
	{
		$this->root_template = $template ;	
	}
	
	/**
	 * Override the currently set Skin. Make sure it exists.
	 * @param string $skin The folder name of the Skin.
	 */
	function _setSkin($skin)
	{
		//if($this->skin === false)
		{
			$this->skin = $skin ;
		}
	}
	
	/**
	 * Returns the currently set Skin.
	 * @return string The name of the currently set Skin.
	 */
	function _getSkin()
	{
		return $this->skin ;
	}
	
	/**
	 * Binds a Key-value pair to the GLOBAL template vars. Use this only if you want all templates to be
	 * able to access your data.
	 * @param string $key
	 * @param mixed $value
	 */
	function _bind($key, $value)
	{
		$this->bind_array[$key] = $value ;
	}
	
	/**
	 * Appends a string value to a currently set key.
	 * @param string $key The key the value is to be appended to.
	 * @param string $value The value that is to be appended. 
	 */
	function _append($key, $value)
	{
		if(!isset($this->bind_array[$key]))
		{
			$this->bind_array[$key] = $value ;
			return true ;
		}
		
		if(is_string($this->bind_array[$key]))
		{
			$this->bind_array[$key] .= $value ;
			return true ;
		}
		
		if(is_array($this->bind_array[$key]))
		{
			array_push($this->bind_array[$key], $value) ;
			return true ;
		}
		
		return false ;
	}
	
	/**
	 * Prepends a string value to a currently set key.
	 * @param string $key The key the value is to be prepended to.
	 * @param string $value The value that is to be prepended. 
	 */
	function _prepend($key, $value)
	{
		
		if(!isset($this->bind_array[$key]))
		{
			$this->bind_array[$key] = $value ;
			return true ;
		}
		
		if(is_string($this->bind_array[$key]))
		{
			$this->bind_array[$key] = $value.$this->bind_array[$key] ;
			return true ;
		}
		
		if(is_array($this->bind_array[$key]))
		{
			$reversed = array_reverse($this->bind_array[$key]) ;
			array_push($reversed, $value) ;
			$this->bind_array[$key] = array_reverse($reversed) ;
			return true ;
		}
		
		return false ;
		
	}
	
	/**
	 * Returns a Wrapper for a Template
	 * @param string $template
	 * @param array $bulk_vars Assigns a bulk of vars to the template.
	 * @return object Template instance
	 */
	function _getTemplate($template, $bulk_vars = array())
	{
			
		return new Template($this->findTemplate($template), $bulk_vars, $this->functions) ;
	}
	
	/**
	 * undocumented function
	 *
	 * @return void
	 * @author Paul Gessinger
	 */
	function _getBinds()
	{
		return $this->bind_array ;
	}
	

	/**
	 * Locates the Template and checks if a skin overwrites it.
	 *
	 * @access public
	 * @param mixed $qry
	 * @return void
	 */
	function findTemplate($qry)
	{
		if(isset($this->cache[$qry]))
		{
			return $this->cache[$qry];
		}
	
		$path_arr = array();
	    
		$inf_arr = explode(':', $qry) ;
		if(count($inf_arr) == 2)
		{
			$module = $inf_arr[0] ;
			$name = $inf_arr[1] ;
		}
		else
		{
			$name =  $inf_arr[0] ;
			$module = 'default' ;
		}
		
		if(!isset($module) OR $module == 'default')
		{
			//echo $this->paths[0] ;
			if(file_exists($this->template_dir.'/'.$name))
			{
				$file = $this->template_dir.'/'.$name ;
				return $this->cache[$qry] = $file;
			}
			else
			{
				throw new ResponseException('Unable to load template "'.$name.'" in directory "'.$this->template_dir.'".');
			}
		}
		else
		{
			$mod_arr = Registry::$_modules ;
	
	
			if(array_key_exists($module, $mod_arr))
			{

				if($mod_arr[$module] == true AND file_exists(SYSPATH.'modules/'.$module.'/html/'.$name))
				{
					$file = SYSPATH.'modules/'.$module.'/html/'.$name ;
				}
				
				// we have a valid module template from module specified, looking for overrides in current skin
				if(file_exists(SYSPATH.'skins/'.$this->skin.'/html/'.$module.'/'.$name))
				{
					$file = SYSPATH.'skins/'.$this->skin.'/html/'.$module.'/'.$name ;
				}
				
	    		
				if(empty($file))
				{
					throw new ResponseException('Unable to load template "'.$name.'".');
				}
	    		
				return $this->cache[$qry] = $file;
			}
			else
			{
				throw new ResponseException('Unable to load "'.$name.'" from "'.$module.'". No such module available');
			}
		}   
	}
	
	/**
	 * undocumented function
	 *
	 * @param string $tpl 
	 * @return void
	 * @author Paul Gessinger
	 */
	function _templateExists($tpl)
	{
		try
		{
			$this->findTemplate($tpl) ;
			
			return true ;
		}
		catch(ResponseException $e)
		{
			return false ;
		}
	}
	
	
	/**
	 * Registers a function for calling from templates.
	 * @param string $name The name that the function will be exposed to the template under.
	 * @param mixed $closure A callable resource to act upon function calling.
	 */
	function _bindTemplateFunction($name, $closure)
	{
		if(!is_callable($closure))
			{
				throw new ResponseException('The resource provided is not callable. Yet it must to answer as a template function.') ;
			}
			$this->functions[$name] = $closure ;	
	}



	/**
	 * Renders the root template and echoes the accumulated string. If root_template is set to false it will
	 * not be rendered.
	 */
	function _render()
	{
		
		//var_dump($this->bind_array) ;
			
		$this->_prepareResponse() ;
		
		Core::fireEvent('onRender') ;
			
		$this->_doAux();
	
		try
		{
				
			if($this->root_template)
			{			
				$tpl = $this->getTemplate($this->root_template) ;
				$this->accumulated .= $tpl->render() ;
			}
				
		}
		catch(Exception $e)
		{
			ob_end_clean();
			ob_start();
			throw $e ;
		}
			
		echo $this->accumulated ;
	}


	private function  _doAux()
	{
		if(Registry::conf('misc:debug') === 'true')
		{
			$this->bind('aux_js', '
<script type="text/javascript">
'.$this->bind_array['aux_js'].'
</script>') ;
			
			$this->bind('aux_head', '
<link rel="stylesheet" href="'.SYSURI.'core/libs/js/jquery/flick/jquery-ui.css" type="text/css" />
<script type="text/javascript" src="'.SYSURI.'core/libs/js/jquery/jquery-ui.js"></script>') ;
		}
	}
	
	/**
	 * Disables the output of the root_template by setting it to false.
	 */
	function _disableOutput()
	{
		$this->root_template = false ;
	}
}