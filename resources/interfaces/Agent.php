<?php
/**
 * Interface for Agent.
 * @author Paul Gessinger
 *
 */
interface Agent
{
	function dispatch($controller, $task, $params) ;
	function render() ;
	function notify() ;
}
