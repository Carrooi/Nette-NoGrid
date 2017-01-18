<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Carrooi\NoGrid\DataSource\IDataSource;
use Carrooi\NoGrid\NoGrid;
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
	 * @param \Carrooi\NoGrid\NoGrid $grid
	 */
	public function configure(NoGrid $grid)
	{
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
		foreach ($this->conditions as $condition) {
			BaseDataSource::makeWhere($this->qb, $condition);
		}

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
