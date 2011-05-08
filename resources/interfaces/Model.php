<?php
/**
 * Interface for Model.
 * @author Paul Gessinger
 *
 */
interface Model
{
	function __construct();
	static function load($criterion) ;
	function save() ;
}
