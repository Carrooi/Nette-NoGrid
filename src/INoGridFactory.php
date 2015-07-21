<?php

namespace Carrooi\NoGrid;

use Carrooi\NoGrid\DataSource\IDataSource;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
interface INoGridFactory
{


	/**
	 * @param \Carrooi\NoGrid\DataSource\IDataSource $dataSource
	 * @return \Carrooi\NoGrid\NoGrid
	 */
	public function create(IDataSource $dataSource);

}
