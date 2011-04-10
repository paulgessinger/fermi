<?php
class DebugController extends FermiController
{
	function __construct()
	{
		$this->registerWith('DebugAgent') ;
	
	}
	
}