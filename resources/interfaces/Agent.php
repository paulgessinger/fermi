<?php

/**
 * Interface for Agent.
 * @author Paul Gessinger
 *
 */
interface Agent
{
	function dispatch($action) ;
	function render() ;
	function notify() ;
}
