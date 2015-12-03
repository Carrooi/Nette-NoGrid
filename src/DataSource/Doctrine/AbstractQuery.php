<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Kdyby\Doctrine\EntityRepository;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class AbstractQuery
{


	/** @var array */
	private $hints = [];


	/**
	 * Intended to be overridden by descendant.
	 *
	 * @deprecated use ::getTotalCountQuery()
	 * @param \Kdyby\Doctrine\EntityRepository $repository
	 * @return int
	 */
	public function getTotalCount(EntityRepository $repository)
	{
	}


	/**
	 * Intended to be overridden by descendant.
	 *
	 * @param \Kdyby\Doctrine\EntityRepository $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function getTotalCountQuery(EntityRepository $repository)
	{
	}


	/**
	 * Intended to be overridden by descendant.
	 *
	 * @param \Kdyby\Doctrine\EntityRepository $repository
	 * @param array $data
	 * @return array
	 */
	public function postFetch(EntityRepository $repository, $data)
	{
	}


	/**
	 * @param \Kdyby\Doctrine\EntityRepository $repository
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	abstract public function getQuery(EntityRepository $repository);


	/**
	 * @return array
	 */
	public function getQueryHints()
	{
		return $this->hints;
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 * @return $this
	 */
	public function setQueryHint($name, $value)
	{
		$this->hints[$name] = $value;
		return $this;
	}

}
