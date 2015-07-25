<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\AbstractQuery;
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

	/** @var int */
	private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

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
	public function getHydrationMode()
	{
		return $this->hydrationMode;
	}


	/**
	 * @param int $hydrationMode
	 * @return $this
	 */
	public function setHydrationMode($hydrationMode)
	{
		$this->hydrationMode = $hydrationMode;
		return $this;
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
		return $this->getResultSet()->toArray($this->getHydrationMode());
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
