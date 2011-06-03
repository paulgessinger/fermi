<?php

/**
 * Interface for Agent.
 * @author Paul Gessinger
 *
 */
interface Agent
{
	function dispatch(FermiController $controller, $action) ;
	function render() ;
	function notify() ;
}
