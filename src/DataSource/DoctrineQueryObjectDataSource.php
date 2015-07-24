<?php

namespace Carrooi\NoGrid\DataSource;

use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineQueryObjectDataSource implements IDataSource
{


	/** @var \Kdyby\Persistence\Queryable */
	private $repository;

	/** @var \Kdyby\Doctrine\QueryObject */
	private $query;

	/** @var \Kdyby\Doctrine\ResultSet */
	private $resultSet;


	/**
	 * @param \Kdyby\Persistence\Queryable $repository
	 * @param \Kdyby\Doctrine\QueryObject $query
	 */
	public function __construct(Queryable $repository, QueryObject $query)
	{
		$this->repository = $repository;
		$this->query = $query;
	}


	/**
	 * @return \Kdyby\Doctrine\QueryObject
	 */
	public function getQueryObject()
	{
		return $this->query;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->getResultSet()->getTotalCount();
	}


	/**
	 * @return \Kdyby\Doctrine\QueryObject
	 */
	public function &getData()
	{
		return $this->query;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		return $this->getResultSet()->toArray();
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->getResultSet()->applyPaging($offset, $limit);
	}


	/**
	 * @return \Kdyby\Doctrine\ResultSet
	 */
	private function getResultSet()
	{
		if (!$this->resultSet) {
			$this->resultSet = $this->query->fetch($this->repository);
		}

		return $this->resultSet;
	}

}
