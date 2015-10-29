<?php

namespace Carrooi\NoGrid\DataSource;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ArrayDataSource implements IDataSource
{


	/** @var array */
	private $dataDefinition;

	/** @var array */
	private $data;


	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->dataDefinition = $data;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return count($this->dataDefinition);
	}


	/**
	 * @return array
	 */
	public function &getData()
	{
		return $this->dataDefinition;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		if ($this->data === null) {
			$this->data = $this->dataDefinition;
		}

		return $this->data;
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->data = array_slice($this->fetchData(), $offset, $limit);
	}

}
