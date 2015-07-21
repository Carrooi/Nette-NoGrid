<?php

namespace Carrooi\NoGrid\DataSource;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ArrayDataSource implements IDataSource
{


	/** @var array */
	private $data;


	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->data = $data;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return count($this->data);
	}


	/**
	 * @return array
	 */
	public function &getData()
	{
		return $this->data;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		return $this->data;
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->data = array_slice($this->data, $offset, $limit);
	}

}
