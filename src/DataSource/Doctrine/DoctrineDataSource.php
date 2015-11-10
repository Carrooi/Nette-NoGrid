<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Carrooi\NoGrid\DataSource\IDataSource;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\QueryBuilder;


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineDataSource extends BaseDataSource implements IDataSource
{


	/** @var \Kdyby\Doctrine\QueryBuilder */
	private $qb;

	/** @var \Carrooi\NoGrid\Condition[] */
	private $conditions = [];


	/**
	 * @param \Kdyby\Doctrine\QueryBuilder $qb
	 */
	public function __construct(QueryBuilder $qb)
	{
		$this->qb = $qb;
	}


	/**
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function getQueryBuilder()
	{
		return $this->qb;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		return (new Paginator($this->qb->getQuery()))->count();
	}


	/**
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function &getData()
	{
		return $this->qb;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		return self::fetchDataFromQuery(
			$this->qb,
			$this->getHydrationMode(),
			$this->conditions,
			$this->qb->getMaxResults(),
			$this->qb->getFirstResult(),
			[],
			$this->getFetchJoinCollections(),
			$this->getUseOutputWalkers()
		);
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
		$this->qb
			->setFirstResult($offset)
			->setMaxResults($limit);
	}

}
