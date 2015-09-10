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

	/** @var \Carrooi\NoGrid\DataSource\IDoctrineQueryFunction */
	private $queryDefinition;

	/** @var \Carrooi\NoGrid\DataSource\DoctrineDataSource */
	private $queryDataSource;


	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @param \Carrooi\NoGrid\DataSource\IDoctrineQueryFunction $queryDefinition
	 */
	public function __construct(EntityRepository $repository, IDoctrineQueryFunction $queryDefinition)
	{
		$this->repository = $repository;
		$this->queryDefinition = $queryDefinition;
	}


	/**
	 * @return \Carrooi\NoGrid\DataSource\IDoctrineQueryFunction
	 */
	public function getQueryDefinition()
	{
		return $this->queryDefinition;
	}


	/**
	 * @return int
	 */
	public function getHydrationMode()
	{
		return $this->getQueryDataSource()->getHydrationMode();
	}


	/**
	 * @param int $hydrationMode
	 * @return $this
	 */
	public function setHydrationMode($hydrationMode)
	{
		$this->getQueryDataSource()->setHydrationMode($hydrationMode);
		return $this;
	}


	/**
	 * @return bool
	 */
	public function getUseOutputWalkers()
	{
		return $this->getQueryDataSource()->getUseOutputWalkers();
	}


	/**
	 * @param bool $useOutputWalkers
	 * @return $this
	 */
	public function setUseOutputWalkers($useOutputWalkers)
	{
		$this->getQueryDataSource()->setUseOutputWalkers($useOutputWalkers);
		return $this;
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
		if ($this->queryDefinition instanceof IDoctrineQueryFunctionCountable) {
			return (int) $this->queryDefinition->getCountQueryBuilder($this->repository)->getQuery()->getSingleScalarResult();
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
