<?php

namespace Carrooi\NoGrid\DataSource;

use Carrooi\NoGrid\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
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

	/** @var \Doctrine\ORM\Query */
	private $query;


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
	 * @return \Doctrine\ORM\Query
	 * @throws \Carrooi\NoGrid\InvalidArgumentException
	 */
	public function getQuery()
	{
		if (!$this->query) {
			$query = $this->queryDefinition->__invoke($this->repository);

			if ($query instanceof QueryBuilder) {
				$query = $query->getQuery();
			}

			if (!$query instanceof Query) {
				throw new InvalidArgumentException('DoctrineQueryFunctionDataSource::__invoke must return instance of Query or QueryBuilder, '. get_class($query). ' given.');
			}

			$this->query = $query;
		}

		return $this->query;
	}


	/**
	 * @return int
	 * @throws \Doctrine\ORM\Query\QueryException
	 */
	public function getCount()
	{
		if ($this->queryDefinition instanceof IDoctrineQueryFunctionCountable) {
			$query = $this->queryDefinition->getCountQueryBuilder($this->repository);

			if ($query instanceof QueryBuilder) {
				$query = $query->getQuery();
			}

			if (!$query instanceof Query) {
				throw new InvalidArgumentException('DoctrineQueryFunctionDataSource::getCountQueryBuilder must return instance of Query or QueryBuilder, '. get_class($query). ' given.');
			}

			return (int) $query->getSingleScalarResult();
		} else {
			$paginator = new Paginator($this->getQuery());
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
		$qb = $this->getQuery();

		$data = self::fetchDataFromQuery(
			$qb,
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
		$this->getQuery()
			->setFirstResult($offset)
			->setMaxResults($limit);
	}

}
