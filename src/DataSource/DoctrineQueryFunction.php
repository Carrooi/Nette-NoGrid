<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class DoctrineQueryFunction
{


	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	abstract public function __invoke(EntityRepository $repository);


	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getCount(EntityRepository $repository)
	{

	}

}
