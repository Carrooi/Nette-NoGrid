<?php

/**
 * Test: Carrooi\NoGrid\DataSource\ArrayDataSource
 *
 * @testCase CarrooiTests\NoGrid\DataSource\ArrayDataSourceTest
 */

namespace CarrooiTests\NoGrid\DataSource;

use Carrooi\NoGrid\DataSource\ArrayDataSource;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ArrayDataSourceTest extends TestCase
{


	public function testGetCount()
	{
		$source = new ArrayDataSource([1, 2, 3, 4, 5]);

		Assert::same(5, $source->getCount());
	}


	public function testGetData()
	{
		$data = [1, 2, 3, 4, 5];

		$source = new ArrayDataSource($data);

		Assert::same($data, $source->getData());
	}


	public function testFetchData()
	{
		$data = [1, 2, 3, 4, 5];

		$source = new ArrayDataSource($data);

		Assert::equal($data, $source->fetchData());
	}


	public function testLimit()
	{
		$data = [1, 2, 3, 4, 5];

		$source = new ArrayDataSource($data);

		$source->limit(1, 2);

		Assert::equal([2, 3], $source->fetchData());
		Assert::equal($data, $source->getData());
	}

}


(new ArrayDataSourceTest)->run();