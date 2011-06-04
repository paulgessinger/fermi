<?php

class FermiCollection extends FermiObject implements Iterator
{
	protected $_model ;
	protected $_beans = false ;
  	private $position;
 
	function __construct(FermiModel $model, $beans = false)
	{
		$this->_model = $model ;
		if($beans !== false)
		{
			$this->_beans = $beans ;
		}
	}
	
	function find($where = '', $data = array())
	{
		if($this->_beans === false)
		{
			$this->_beans = R::find($this->_model->type, $where, $data) ;
		}
		else
		{
			throw new OrmException('Cannot use find on a collection that already has a set of beans loaded.') ;
		}
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