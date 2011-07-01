<?php

/**
 * Interface for Agent.
 *
 * @package Core
 * @author Paul Gessinger
 */
interface Agent
{
	function dispatch($action) ;
	function render() ;
	function notify() ;
}
