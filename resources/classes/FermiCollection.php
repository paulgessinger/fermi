<?php

class FermiCollection implements Iterator
{
	protected $_model ;
	protected $_beans ;
  	private $position;
 
	function __construct($model)
	{
		$this->_model = $model ;
	}
	
	function find($where = '', $data = array())
	{
		$this->_beans = R::find($this->_model->type, $where, $data) ;

	}
	
	
	
	public function rewind()
	{
		reset($this->_beans) ;
	}
 
	public function valid()
	{
		return array_key_exists($this->key(), $this->_beans) ;
	}
 
	public function key() 
	{
		return key($this->_beans) ;
	}
 
	public function current()
	{
		if($this->_beans[$this->key()] instanceof FermiModel)
		{
			return $this->_beans[$this->key()] ;
		}

		$new_model = clone $this->_model ;
		$new_model->bean = $this->_beans[$this->key()] ;
		
		$this->_beans[$this->key()] = $new_model ;
		
		return $new_model ;
		//return $this->_beans[$this->key()];
	}
 
	public function next()
	{
		next($this->_beans) ;
	}
}