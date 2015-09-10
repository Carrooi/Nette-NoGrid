<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineQueryFunctionDataSource implements IDataSource
{


	/** @var \Doctrine\ORM\EntityRepository */
	private $repository;

	/** @var \Carrooi\NoGrid\DataSource\DoctrineQueryFunction */
	private $queryDefinition;

	/** @var \Carrooi\NoGrid\DataSource\DoctrineDataSource */
	private $queryDataSource;


	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @param \Carrooi\NoGrid\DataSource\DoctrineQueryFunction $queryDefinition
	 */
	public function __construct(EntityRepository $repository, DoctrineQueryFunction $queryDefinition)
	{
		$this->repository = $repository;
		$this->queryDefinition = $queryDefinition;
	}


	/**
	 * @return \Carrooi\NoGrid\DataSource\DoctrineQueryFunction
	 */
	public function getQueryDefinition()
	{
		return $this->queryDefinition;
	}


	/**
	 * @return \Carrooi\NoGrid\DataSource\DoctrineDataSource
	 */
	private function getQueryDataSource()
	{
		if (!$this->queryDataSource) {
			$query = $this->queryDefinition;
			$this->queryDataSource = new DoctrineDataSource($query($this->repository));
		}

		return $this->queryDataSource;
	}


	/**
	 * @return int
	 * @throws \Doctrine\ORM\Query\QueryException
	 */
	public function getCount()
	{
		if ($query = $this->queryDefinition->getCount($this->repository)) {
			return (int) $query->getQuery()->getSingleScalarResult();
		} else {
			return $this->getQueryDataSource()->getCount();
		}
	}


	/**
	 * @return mixed
	 */
	public function &getData()
	{
		return $this->getQueryDataSource()->getData();
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		return $this->getQueryDataSource()->fetchData();
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->getQueryDataSource()->limit($offset, $limit);
	}

}
