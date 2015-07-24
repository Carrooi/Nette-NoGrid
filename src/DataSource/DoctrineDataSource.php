<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineDataSource implements IDataSource
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
		if ($this->qb->getMaxResults() !== null || $this->qb->getFirstResult() !== null) {
			$result = new Paginator($this->qb->getQuery());
		} else {
			$result = $this->qb->getQuery()->getResult();
		}

		$data = [];
		foreach ($result as $item) {
			$data[] = is_array($item) ? $item[0] : $item;
		}

		return $data;
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
