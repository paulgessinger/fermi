<?php
class DebugController extends CController
{
	function __construct()
	{
		$this->registerWith('DebugAgent') ;
	
	}
	
}