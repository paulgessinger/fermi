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
		
		$traces = array() ;
		
		foreach($e->getTrace() as $trace_depth => $trace)
		{
				
			/*
			$traces[$trace_depth]['depth'] = $trace_depth ;
			$traces[$trace_depth]['file'] = '' ;
			$traces[$trace_depth]['line'] = '' ;
			$traces[$trace_depth]['function'] = '' ;
			*/
				
			$depth_array = array(
				'depth' => $trace_depth
			) ;	
			
			if(isset($trace['file']))
			{
				$depth_array['file'] = $trace['file'] ;
			}
			else
			{
				$depth_array['file'] = '' ;
			}
				
			if(isset($trace['line']))
			{
				$depth_array['line'] = $trace['line'] ;
			}
			else
			{
				$depth_array['line'] = '' ;
			}
				
				
			
			if(isset($trace['function']))
			{
									
				if(isset($trace['class']))
				{
					$depth_array['function'] = $trace['class'].'::' ;
				}
				else
				{
					$depth_array['function'] = '' ;
				}
				
				$depth_array['function'] .= $trace['function'] ;
					
					
				if(isset($trace['args']) AND is_array($trace['args']))
				{
					
					foreach($trace['args'] as $i => $arg)
					{
						if(is_object($arg))
						{
							$trace['args'][$i] = get_class($arg) ;
						}
						
						if(is_array($arg))
						{
							$trace['args'][$i] = 'Array' ;
						}
					}
					
					//echo implode(', ', $trace['args']) ;
					$depth_array['function'] .= '( '.implode(', ', $trace['args']).' ) ;' ;
				}
				else
				{
					$depth_array['function'] .= '() ;' ;
				}
					
			}
			
			
			array_push($traces, $depth_array) ;
		}

		Response::setRootTemplate('error:error.phtml') ;
		Response::bind('traces', $traces) ;
		Response::render() ;
		
	}
	
	
}