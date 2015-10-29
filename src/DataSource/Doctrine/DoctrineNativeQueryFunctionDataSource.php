<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Carrooi\NoGrid\DataSource\IDataSource;
use Carrooi\NoGrid\InvalidArgumentException;
use Doctrine\ORM\Query;
use Kdyby\Doctrine\EntityRepository;
use Kdyby\Doctrine\NativeQueryBuilder;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DoctrineNativeQueryFunctionDataSource implements IDataSource
{


	/** @var int */
	private $hydrationMode = Query::HYDRATE_OBJECT;

	/** @var \Kdyby\Doctrine\EntityRepository */
	private $repository;

	/** @var \Carrooi\NoGrid\DataSource\Doctrine\AbstractQuery */
	private $queryDefinition;

	/** @var \Kdyby\Doctrine\NativeQueryBuilder */
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
	 * @return \Kdyby\Doctrine\NativeQueryBuilder
	 * @throws \Carrooi\NoGrid\InvalidArgumentException
	 */
	public function getQuery()
	{
		if (!$this->query) {
			$query = $this->queryDefinition->getQuery($this->repository);

			if (!$query instanceof NativeQueryBuilder) {
				throw new InvalidArgumentException('Doctrine\NativeQueryFunctionDataSource::getQuery must return instance of NativeQueryBuilder, '. get_class($query). ' given.');
			}

			$this->query = $query;
		}

		return $this->query;
	}


	/**
	 * @return int
	 * @throws \Carrooi\NoGrid\InvalidArgumentException
	 */
	public function getCount()
	{
		if (($count = $this->queryDefinition->getTotalCount($this->repository)) === null) {
			throw new InvalidArgumentException('Doctrine\NativeQueryFunctionDataSource: Please implement method getTotalCount().');
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
		$data = $this->getQuery()->getQuery()->getResult($this->getHydrationMode());

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
