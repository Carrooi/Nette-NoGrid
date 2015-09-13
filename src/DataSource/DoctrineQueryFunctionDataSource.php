<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineQueryFunctionDataSource extends BaseDoctrineDataSource implements IDataSource
{


	/** @var \Doctrine\ORM\EntityRepository */
	private $repository;

	/** @var \Carrooi\NoGrid\DataSource\IDoctrineQueryFunction */
	private $queryDefinition;

	/** @var \Doctrine\ORM\QueryBuilder */
	private $qb;


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
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQueryBuilder()
	{
		if (!$this->qb) {
			$this->qb = $this->queryDefinition->__invoke($this->repository);
		}

		return $this->qb;
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
			$paginator = new Paginator($this->getQueryBuilder()->getQuery());
			return $paginator->count();
		}
	}


	/**
	 * @return \Carrooi\NoGrid\DataSource\IDoctrineQueryFunction
	 */
	public function &getData()
	{
		return $this->queryDefinition;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		$qb = $this->getQueryBuilder();

		$data = self::fetchDataFromQuery(
			$qb->getQuery(),
			$this->getHydrationMode(),
			$qb->getMaxResults(),
			$qb->getFirstResult(),
			$this->getFetchJoinCollections(),
			$this->getUseOutputWalkers()
		);

		if ($this->queryDefinition instanceof IDoctrineMultiQueryFunction) {
			if (($dataUpdated = $this->queryDefinition->postFetch($this->repository, $data)) !== null) {
				$data = $dataUpdated;
			}
		}

		return $data;
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->getQueryBuilder()
			->setFirstResult($offset)
			->setMaxResults($limit);
	}

}
