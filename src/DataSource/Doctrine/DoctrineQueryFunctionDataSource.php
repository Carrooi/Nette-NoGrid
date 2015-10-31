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

	/** @var \Doctrine\ORM\Query */
	private $query;


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
	 * @return \Doctrine\ORM\Query
	 * @throws \Carrooi\NoGrid\InvalidArgumentException
	 */
	public function getQuery()
	{
		if (!$this->query) {
			$query = $this->queryDefinition->getQuery($this->repository);

			if ($query instanceof QueryBuilder) {
				$query = $query->getQuery();
			}

			if (!$query instanceof Query) {
				throw new InvalidArgumentException('Doctrine\QueryFunctionDataSource::__invoke must return instance of Query or QueryBuilder, '. get_class($query). ' given.');
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
		if (($count = $this->queryDefinition->getTotalCount($this->repository)) === null) {
			$count = (new Paginator($this->getQuery()))->count();
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
		$qb = $this->getQuery();

		$data = self::fetchDataFromQuery(
			$qb,
			$this->getHydrationMode(),
			$qb->getMaxResults(),
			$qb->getFirstResult(),
			$this->getFetchJoinCollections(),
			$this->getUseOutputWalkers()
		);

		if ($postFetch = $this->queryDefinition->postFetch($this->repository, $data)) {
			$data = $postFetch;
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
