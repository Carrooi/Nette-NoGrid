<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\AbstractQuery;
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

	/** @var int */
	private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

	/** @var bool */
	private $useOutputWalkers;


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
	public function getHydrationMode()
	{
		return $this->hydrationMode;
	}


	/**
	 * @param int $hydrationMode
	 * @return $this
	 */
	public function setHydrationMode($hydrationMode)
	{
		$this->hydrationMode = $hydrationMode;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function getUseOutputWalkers()
	{
		return $this->useOutputWalkers;
	}


	/**
	 * @param bool $useOutputWalkers
	 * @return $this
	 */
	public function setUseOutputWalkers($useOutputWalkers)
	{
		$this->useOutputWalkers = (bool) $useOutputWalkers;
		return $this;
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
		$query->setHydrationMode($this->hydrationMode);

		if ($this->qb->getMaxResults() !== null || $this->qb->getFirstResult() !== null) {
			$result = new Paginator($query);

			if ($this->useOutputWalkers !== null) {
				$result->setUseOutputWalkers($this->useOutputWalkers);
			}
		} else {
			$result = $query->getResult();
		}

		$data = [];
		foreach ($result as $item) {
			$data[] = is_array($item) && array_key_exists(0, $item) ? $item[0] : $item;
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
