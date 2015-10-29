<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Kdyby\Doctrine\EntityRepository;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class AbstractQuery
{


	/**
	 * Intended to be overridden by descendant.
	 *
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
	 * @param array $data
	 * @return array
	 */
	public function postFetch(EntityRepository $repository, $data)
	{
	}


	/**
	 * @param \Kdyby\Doctrine\EntityRepository $repository
	 * @return mixed
	 */
	abstract public function getQuery(EntityRepository $repository);

}
