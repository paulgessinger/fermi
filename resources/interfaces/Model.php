<?php
/**
 * Interface for Model.
 * @author Paul Gessinger
 *
 */
interface Model
{
	function __construct();
	function load($criterion) ;
	function save() ;
}
