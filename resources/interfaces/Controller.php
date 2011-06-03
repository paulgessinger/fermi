<?php
/**
 * Interface for Controller.
 * @author Paul Gessinger
 *
 */
interface Controller
{
	function __construct();
	function execute($action);
}
