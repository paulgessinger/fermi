<?php

/**
 * Interface for Agent.
 * @author Paul Gessinger
 *
 */
interface Agent
{
	function dispatch(FermiController $controller, $action, $params) ;
	function render() ;
	function notify() ;
}
