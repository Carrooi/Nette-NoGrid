<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IDoctrineQueryFunctionCountable
{


	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getCountQueryBuilder(EntityRepository $repository);

}
