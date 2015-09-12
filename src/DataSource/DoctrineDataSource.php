<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineDataSource extends BaseDoctrineDataSource implements IDataSource
{


	/** @var \Doctrine\ORM\QueryBuilder */
	private $qb;


	/**
	 * @param \Doctrine\ORM\QueryBuilder $qb
	 */
	public function __construct(QueryBuilder $qb)
	{
		$this->qb = $qb;
	}


	/**
	 * @return \Doctrine\ORM\QueryBuilder
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
		$paginator = new Paginator($this->qb->getQuery());

		return $paginator->count();
	}


	/**
	 * @return \Doctrine\ORM\QueryBuilder
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
		$query = $this->qb->getQuery();

		return self::fetchDataFromQuery(
			$query,
			$this->getHydrationMode(),
			$this->qb->getMaxResults(),
			$this->qb->getFirstResult(),
			$this->getFetchJoinCollections(),
			$this->getUseOutputWalkers()
		);
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
