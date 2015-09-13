<?php

namespace Carrooi\NoGrid\DataSource;

use Doctrine\ORM\EntityRepository;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
interface IDoctrineMultiQueryFunction extends IDoctrineQueryFunction
{


	/**
	 * @param \Doctrine\ORM\EntityRepository $repository
	 * @param array $data
	 */
	public function postFetch(EntityRepository $repository, $data);

}
