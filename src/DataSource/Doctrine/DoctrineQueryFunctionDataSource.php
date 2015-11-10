<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Carrooi\NoGrid\DataSource\IDataSource;
use Carrooi\NoGrid\InvalidArgumentException;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\QueryBuilder;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineQueryFunctionDataSource extends BaseDataSource implements IDataSource
{


	/** @var \Doctrine\ORM\EntityRepository */
	private $repository;

	/** @var \Carrooi\NoGrid\DataSource\Doctrine\AbstractQuery */
	private $queryDefinition;

	/** @var \Kdyby\Doctrine\QueryBuilder */
	private $qb;

	/** @var \Carrooi\NoGrid\Condition[] */
	private $conditions = [];


	/**
	 * @param \Kdyby\Doctrine\EntityRepository $repository
	 * @param \Carrooi\NoGrid\DataSource\Doctrine\AbstractQuery $queryDefinition
	 */
	public function __construct(EntityRepository $repository, AbstractQuery $queryDefinition)
	{
		$this->repository = $repository;
		$this->queryDefinition = $queryDefinition;
	}


	/**
	 * @return \Carrooi\NoGrid\DataSource\Doctrine\AbstractQuery
	 */
	public function getQueryDefinition()
	{
		return $this->queryDefinition;
	}


	/**
	 * @return \Kdyby\Doctrine\QueryBuilder
	 * @throws \Carrooi\NoGrid\InvalidArgumentException
	 */
	public function getQueryBuilder()
	{
		if (!$this->qb) {
			$qb = $this->queryDefinition->getQuery($this->repository);

			if (!$qb instanceof QueryBuilder) {
				throw new InvalidArgumentException('Doctrine\QueryFunctionDataSource::__invoke must return instance of QueryBuilder, '. get_class($qb). ' given.');
			}

			$this->qb = $qb;
		}

		return $this->qb;
	}


	/**
	 * @return int
	 * @throws \Doctrine\ORM\Query\QueryException
	 */
	public function getCount()
	{
		if (($count = $this->queryDefinition->getTotalCount($this->repository)) === null) {
			$count = (new Paginator($this->getQueryBuilder()->getQuery()))->count();
		}

		return $count;
	}


	/**
	 * @return \Carrooi\NoGrid\DataSource\Doctrine\AbstractQuery
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
		$data = self::fetchDataFromQuery(
			$this->getQueryBuilder(),
			$this->getHydrationMode(),
			$this->conditions,
			$this->getQueryBuilder()->getMaxResults(),
			$this->getQueryBuilder()->getFirstResult(),
			$this->queryDefinition->getQueryHints(),
			$this->getFetchJoinCollections(),
			$this->getUseOutputWalkers()
		);

		if ($postFetch = $this->queryDefinition->postFetch($this->repository, $data)) {
			$data = $postFetch;
		}

		return $data;
	}


	/**
	 * @param \Carrooi\NoGrid\Condition[] $conditions
	 */
	public function filter(array $conditions)
	{
		$this->conditions = $conditions;
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
