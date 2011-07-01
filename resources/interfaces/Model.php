<?php

/**
 * Interface for Model.
 *
 * @package Core
 * @author Paul Gessinger
 */
interface Model
{
	function __construct();
	function load($criterion) ;
	function save() ;
}
