<?php
/**
 * This Controller manages the output of Exceptions
 * @author Paul Gessinger
 *
 */
class ErrorController extends FermiController
{
	/**
	 * registers the task display
	 */
	function __construct()
	{
	}
	
	/**
	 * Gets the Exception and prepares the information for the template.
	 */
	function displayAction()
	{
		
		//$tpl = Response::getTemplate('error:error.phtml') ;

		
		// this is temporary until we have a template/output system
		//$e = Request::_('Exception') ;
		//echo $e ;
		
		$e = Request::get('exception') ;
		
		Response::bind('message', $e->getMessage()) ;
		Response::bind('exception', get_class($e)) ;
		
		foreach($e->getTrace() as $trace_depth => $trace)
			{
				//$traces[$trace_depth] = '#'.$trace_depth.' '.$trace['file'].' in line '.$trace['line'] ;
				
				$traces[$trace_depth]['depth'] = $trace_depth ;
				$traces[$trace_depth]['file'] = '' ;
				$traces[$trace_depth]['line'] = '' ;
				$traces[$trace_depth]['function'] = '' ;
				
				if(isset($trace['file']))
				{
					$traces[$trace_depth]['file'] = $trace['file'] ;
				}
				
				if(isset($trace['line']))
				{
					$traces[$trace_depth]['line'] = $trace['line'] ;
				}
				
				
				if(!empty($trace['function']) AND $trace['function'] != 'exception_error_handler')
				{
					if(isset($trace['args']))
					{
						foreach($trace['args'] as $key => $arg)
						{
							if(is_object($arg))
							{
								$trace['args'][$key] = get_class($arg) ;
							}
							
							if(is_array($arg))
							{
								$trace['args'][$key] = 'Array' ;
							}
						}
					}
					else
					{
						$trace['args'] = array();
					}
					
					/*$traces[$trace_depth] .= '<br/>&nbsp;&nbsp;&nbsp;
					>> ' ;*/
					if(!empty($trace['class']))
					{
						$traces[$trace_depth]['function'] = $trace['class'].'::' ;
					}
					
					$traces[$trace_depth]['function'] .= $trace['function'].'( '.implode(', ', $trace['args']).' ) ;' ;
					
					//$traces[$trace_depth] .= $trace['function'].'('.implode(',', $trace['args']).')' ;
					
				}
			}
			
		//unset($traces[0]) ;
	
		//$tpl->bind('traces', $traces) ;
		
		
		//echo $tpl->render();
		Response::setRootTemplate('error:error.phtml') ;
		Response::bind('traces', $traces) ;
		Response::render() ;
		
	}
	
	
}