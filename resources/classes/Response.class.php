<?php
/**
 * Central manager of everything sent to the client. Provides abstraction for Twig.
 * @author Paul Gessinger
 *
 */
class Response
{
	static $_autoInstance = true ;
	var $template_dir ;
	var $compile_dir ;
	protected $root_template = true ;
	var $bind_array = array() ;
	var $skin = false ;
	protected $accumulated ;
	protected $functions = array() ;
	
	/**
	 * Registers a listener to onAgentDispatch.
	 */
	function __construct()
	{
		Core::addListener('onAgentDispatch', array($this, '_prepareResponse')) ;
	}	

	/**
	 * Makes Response ready to deliver content to the client.
	 */
	function _prepareResponse()
	{	
		
		Core::fireEvent('onResponseGoingHot') ;
		
		if($this->root_template !== false)
		{
			$this->root_template = 'index.php' ;
		}

		
		if(!$this->skin)
		{
			/**
			 * get default skin
			 * @todo implement fetching from DB, as soon as we have DB
			 */
			$this->skin = 'dynamic' ;
		}		
		
		$this->template_dir = SYSPATH.'skins/'.$this->skin.'/' ;
				
		$this->bind_array = array(
			'syspath' => SYSPATH,
			'sysuri' => SYSURI,
			'skin' => SYSURI.'skins/'.$this->skin.'',
			'title' => 'Charon powered Site'
		);
		

		
	}
	
	/**
	 * Override the currently set Skin. Make sure it exists.
	 * @param string $skin The folder name of the Skin.
	 */
	function setSkin($skin)
	{
		if($this instanceof Response)
		{
			//if($this->skin === false)
			{
				$this->skin = $skin ;
			}
		}
		else
		{
			return Core::get('Response')->setSkin($skin) ;
		}
	}
	
	/**
	 * Returns the currently set Skin.
	 * @return string The name of the currently set Skin.
	 */
	function getSkin()
	{
		if($this instanceof Response)
		{
			return $this->skin ;
		}
		else
		{
			return Core::get('Response')->setSkin() ;
		}
	}
	
	/**
	 * Binds a Key-value pair to the GLOBAL template vars. Use this only if you want all templates to be
	 * able to access your data.
	 * @param string $key
	 * @param mixed $value
	 */
	function bind($key, $value)
	{
		if($this instanceof Response)
		{
			$this->bind_array[$key] = $value ;
		}
		else
		{
			return Core::get('Response')->bind($key, $value) ;
		}
	}
	
	/**
	 * Appends a string value to a currently set key.
	 * @param string $key The key the value is to be appended to.
	 * @param string $value The value that is to be appended. 
	 */
	function append($key, $value)
	{
		if($this instanceof Response)
		{
			if(is_string($this->bind_array[$key]) OR !array_key_exists($key, $this->bind_array))
			{
				$this->bind_array[$key] .= $value ;
			}
			else
			{
				return false ;
			}
		}
		else
		{
			return Core::get('Response')->append($key, $value) ;
		}
	}
	
	/**
	 * Prepends a string value to a currently set key.
	 * @param string $key The key the value is to be prepended to.
	 * @param string $value The value that is to be prepended. 
	 */
	function prepend($key, $value)
	{
		if($this instanceof Response)
		{
			if(is_string($this->bind_array[$key]) OR !array_key_exists($key, $this->bind_array))
			{
				$this->bind_array[$key] = $value.$this->bind_array[$key] ;
			}
			else
			{
				return false ;
			}
		}
		else
		{
			return Core::get('Response')->append($key, $value) ;
		}
	}
	
	/**
	 * Returns a Wrapper for a Template
	 * @param string $template
	 * @param array $bulk_vars Assigns a bulk of vars to the template.
	 * @return object Template instance
	 */
	function getTemplate($template, $bulk_vars = array())
	{
		if($this instanceof Response)
		{
			$var_array = array_merge($this->bind_array, $bulk_vars) ;
			
			return new Template($this->findTemplate($template), $var_array, $this->functions) ;
		}
		else
		{
			return Core::get('Response')->getTemplate($template, $bulk_vars) ;
		}
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
				throw new ResponseException('Unable to load template "'.$name.'". in directory "'.$this->paths[0].'".');
			}
		}
		else
		{
			$mod_arr = Registry::get('modules') ;
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
	 * Registers a function for calling from templates.
	 * @param string $name The name that the function will be exposed to the template under.
	 * @param mixed $closure A callable resource to act upon function calling.
	 */
	function bindTemplateFunction($name, $closure)
	{
		if($this instanceof Response)
		{
			if(!is_callable($closure))
			{
				throw new ResponseException('The resource provided is not callable. Yet it must to answer as a template function.') ;
			}
			$this->functions[$name] = $closure ;
		}
		else
		{
			return Core::get('Response')->bindTemplateFunction($name, $closure) ;
		}	
	}



	/**
	 * Renders the root template and echoes the accumulated string. If root_template is set to false it will
	 * not be rendered.
	 */
	function render()
	{
		
		$this->_doAux();

		if($this->root_template)
		{			

			$tpl = $this->getTemplate($this->root_template) ;
			$this->accumulated .= $tpl->render() ;
		}
		/*ob_end_clean();
		ob_start();*/
		echo $this->accumulated ;
	}

	private function  _doAux()
	{
			$this->bind('aux_js', '<script type="text/javascript">
'.$this->bind_array['aux_js'].'
</script>') ;
			
			$this->bind('aux_head', '
<link rel="stylesheet" href="'.SYSURI.'core/libs/js/jquery/flick/jquery-ui.css" type="text/css" />
<script type="text/javascript" src="'.SYSURI.'core/libs/js/jquery/jquery.js"></script>
<script type="text/javascript" src="'.SYSURI.'core/libs/js/jquery/jquery-ui.js"></script>') ;
	}
	
	/**
	 * Disables the output of the root_template by setting it to false.
	 */
	function disableOutput()
	{
		if($this instanceof Response)
		{
			$this->root_template = false ;
		}
		else
		{
			return Core::get('Response')->disableOutput() ;
		}
	}
}