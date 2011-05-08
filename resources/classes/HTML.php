<?php
/**
 * This is an extendable Helper method provider for common HTML constructions.
 * @author Paul Gessinger
 *
 */
class HTML
{
	static $_autoInstance = true ;
	protected $helpers = array() ;
	
	function __construct()
	{	
	}
	
	/**
	 * Registers the default Helpers by registerHelper()
	 */
	function launch()
	{
		HTML::registerHelper('link', function($agent, $controller, $task, $params)
		{
			// bla bla bla get url format
			return SYSURI.Request::renderPath($agent, $controller, $task, array('site' => $site)) ;
		}) ;
		
		HTML::registerHelper('sitelink', function($agent, $controller, $task, $site)
		{
			// bla bla bla get url format
			return SYSURI.Request::renderPath($agent, $controller, $task, array('site' => $site)) ;
		}) ;
		
		
		HTML::registerHelper('dialog', function($dialog_content, $dialog_id, $dialog_title, $trigger_id = true, $height = 100, $width = 300)
		{
			$js = '
$(document).ready(function() {
$("body").append(\'<div id="'.$dialog_id.'" title="'.$dialog_title.'" style="">'.$dialog_content.'</div>\') ;' ;
			
			if($trigger_id === true)
			{
				$js .= '$("#'.$dialog_id.'").dialog({autoOpen: true, width: '.$width.', height: '.$height.' }) ;' ;
			}
			else
			{
				$js .= '$("#'.$dialog_id.'").dialog({autoOpen: false, width: '.$width.', height: '.$height.' }) ;
$("#'.$trigger_id.'").click(function(){
	$("#'.$dialog_id.'").dialog("open");
});' ;
			}
			
			
			$js .= '}) ;' ;
			Response::append('aux_js', $js) ;
		}) ;
		
		//echo HTML::_('link', 'hans', 'peter', 'mofo') ;
		
		Core::fireEvent('onHTMLReady') ;
	}
	
	/**
	 * Registers a helper.
	 * @param string $key A Key to access the Helper.
	 * @param $function A callable entity that processes the given arguments.
	 */
	function registerHelper($key, $function)
	{
		if($this instanceof HTML)
		{
			if(!is_callable($function))
			{
				throw new SystemException('Function "'.print_r($function, true).'" is not callable.') ;
			}
			
			$this->helpers[$key] = $function ;
		}
		else
		{
			return Core::get('HTML')->registerHelper($key, $function) ;
		}
	}
	
	/**
	 * Shorthand method to call a Helper. Takes all additional arguments and passes them to the Helper.
	 * See the Helper to find out what arguments it accepts.
	 * @param string $helper_key The Key of the desired Helper.
	 */
	private function callHelper($helper_key, $arguments)
	{
	
		if(array_key_exists($helper_key, $this->helpers))
		{
			return call_user_func_array($this->helpers[$helper_key], $arguments) ;
		}
		else
		{
			return false ;
		}			

	}
	
	
	public static function __callStatic($helper, $arguments)
	{
		return Core::get('HTML')->callHelper($helper, $arguments) ;
	}
}