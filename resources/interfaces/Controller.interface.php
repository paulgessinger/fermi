<?php
/**
 * Interface for Controller.
 * @author Paul Gessinger
 *
 */
interface Controller
{
	function __construct();
	function registerTask($task, $method);
	function execute($task, $params);
}
