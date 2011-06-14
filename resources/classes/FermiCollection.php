<?php
/**
 * FermiCollection is a set of FermiModels that are populated from the database.
 *
 * @author Paul Gessinger
 */
class FermiCollection extends FermiObject implements Iterator
{
	protected $_model ;
	protected $_beans = false ;
  	private $position;
 
 	/**
	 * Sets up the collection with the model given as a base.
	 * @param object FermiModel $model The model that is to be used as the base.
	 * @param array $beans An array of beans that populate the collection immediately.
	 */
	function __construct(FermiModel $model, $beans = false)
	{
		$this->_model = $model ;
		if($beans !== false)
		{
			$this->_beans = $beans ;
		}
	}
	
	/**
	 * Performs a search for beans and populates the collection with them.
	 * @param string $where An SQL where clause.
	 * @param array $data The data for the where clause.
	 */
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
	
	/**
	 * Integrate another collection into this one.
	 * @param object FermiCollection $collection The collection to merge into this one.
	 */
	function merge(FermiCollection $collection)
	{
		foreach($collection as $bean)
		{
			array_push($this->_beans, $bean) ;
		}
	}
	
	/**
	 * Converts all of the beans in this collection into models. This is normaly only done while iterating.
	 */
	function convert()
	{
		foreach($this->_beans as $key => $bean)
		{
			if(!($bean instanceof FermiModel))
			{
				$this->_beans[$key] = clone $this->_model ;
				$this->_beans[$key]->bean = $bean ;
			}
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