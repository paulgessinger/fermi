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
		$this->registerTask('display') ;
	}
	
	/**
	 * Gets the Exception and prepares the information for the template.
	 */
	function display()
	{
		
		$tpl = Response::getTemplate('error:error.php') ;

		
		// this is temporary until we have a template/output system
		$e = Request::_('Exception') ;
		//echo $e ;
		
		$tpl->bind('message', $e->getMessage()) ;
		$tpl->bind('exception', get_class($e)) ;
		
		foreach($e->getTrace() as $trace_depth => $trace)
			{
				//$traces[$trace_depth] = '#'.$trace_depth.' '.$trace['file'].' in line '.$trace['line'] ;
				
				$traces[$trace_depth]['depth'] = $trace_depth ;
				$traces[$trace_depth]['file'] = $trace['file'] ;
				$traces[$trace_depth]['line'] = $trace['line'] ;
				
				if(!empty($trace['function']) AND $trace['function'] != 'exception_error_handler')
				{
					if(is_array($trace['args']))
					{
						foreach($trace['args'] as $key => $arg)
						{
							if(is_object($arg))
							{
								$trace['args'][$key] = get_class($arg) ;
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
			
		unset($traces[0]) ;
	
		$tpl->bind('traces', $traces) ;
		
		
		echo $tpl->render();
	}
	
	
}