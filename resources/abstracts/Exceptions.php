<?php
interface IException
{
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message 
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formated string of trace
    
    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
    public function __construct($message = null, $code = 0);
}

abstract class CustomException extends Exception implements IException
{
    protected $message = 'Unknown exception';     // Exception message
    private   $string;                            // Unknown
    protected $code    = 0;                       // User-defined exception code
    protected $file;                              // Source filename of exception
    protected $line;                              // Source line of exception
    private   $trace;                             // Unknown

    public function __construct($message = null, $code = 0)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
    }
    
    public function __toString()
    {
		$return = '' ;	
		
    	foreach($this->getTrace() as $trace_depth => $trace)
		{
				$keys = array('file', 'line', 'func') ;
				
				foreach($keys as $key)
				{
					if(!isset($trace[$key]))
					{
						$trace[$key] = array() ;
					}
				}

				
				$traces[$trace_depth]['depth'] = $trace_depth ;
				$traces[$trace_depth]['file'] = $trace['file'] ;
				$traces[$trace_depth]['line'] = $trace['line'] ;
				$traces[$trace_depth]['func'] = '' ;
				
				if(!empty($trace['function']) AND $trace['func'] != 'exception_error_handler')
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
						$traces[$trace_depth]['func'] = $trace['class'].'::' ;
					}
					
					$traces[$trace_depth]['func'] .= $trace['function'].'( '.implode(', ', $trace['args']).' ) ;' ;
					
					//$traces[$trace_depth] .= $trace['function'].'('.implode(',', $trace['args']).')' ;
					
				}
		}

    	$return .= '"'.$this->getMessage().'" in <strong>'.$this->getFile().'</strong> on line <strong>'.$this->getLine().'</strong> <br/><br/><strong>Stacktrace:</strong><br/>' ;
		
    	foreach($traces as $trace)
    	{
    		$return .= '#'.$trace['depth'].' - <strong>'.$trace['file'].'</strong> in line <strong>'.$trace['line'].'</strong><br/>
	&nbsp;&nbsp;&rArr; <strong>'.$trace['func'].'</strong><br/>' ;
    	}
    	
    	return $return ;
    }
}

class EventException extends CustomException {}
class ResponseException extends CustomException {}
class RoutingException extends CustomException {}
class SystemException extends CustomException {}
class DatabaseException extends CustomException {}
class ValidationException extends CustomException {}
class OrmException extends CustomException {}