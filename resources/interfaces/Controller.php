<?php

/**
 * Interface for Controller.
 *
 * @package Core
 * @author Paul Gessinger
 */
interface Controller
{
	function __construct();
	function execute($action);
}
