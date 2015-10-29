<?php

/**
 * Test: Carrooi\NoGrid\NoGrid
 *
 * @testCase CarrooiTests\NoGrid\NoGridTest
 */

namespace CarrooiTests\NoGrid;

use Carrooi\NoGrid\DataSource\IDataSource;
use Carrooi\NoGrid\IPaginatorTemplateProvider;
use Carrooi\NoGrid\NoGrid;
use Carrooi\NoGrid\View;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGridTest extends TestCase
{


	/** @var \Carrooi\NoGrid\DataSource\IDataSource|\Mockery\MockInterface */
	private $source;

	/** @var \Carrooi\NoGrid\NoGrid */
	private $grid;


	public function setUp()
	{
		$this->source = \Mockery::mock(IDataSource::class);

		/** @var \Mockery\MockInterface|\Carrooi\NoGrid\IPaginatorTemplateProvider $paginatorTemplateProvider */
		$paginatorTemplateProvider = \Mockery::mock(IPaginatorTemplateProvider::class);

		$this->grid = new NoGrid($this->source, $paginatorTemplateProvider);
	}


	public function tearDown()
	{
		\Mockery::close();
	}


	public function testViews()
	{
		Assert::count(0, $this->grid->getViews());

		$this->grid->addView('view1', 'View 1', function() {});
		$this->grid->addView('view2', 'View 2', function() {});

		Assert::true($this->grid->hasView('view1'));
		Assert::true($this->grid->hasView('view2'));
		Assert::false($this->grid->hasView('view3'));

		$views = $this->grid->getViews();

		Assert::count(2, $views);
		Assert::type(View::class, $views[0]);
		Assert::type(View::class, $views[1]);

		Assert::same('view1', $views[0]->getName());
		Assert::same('View 1', $views[0]->getTitle());
		Assert::same('view2', $views[1]->getName());
		Assert::same('View 2', $views[1]->getTitle());
	}


	public function testDisablePaginator()
	{
		Assert::true($this->grid->isPaginatorEnabled());

		$this->grid->disablePaginator();

		Assert::false($this->grid->isPaginatorEnabled());

		$this->grid->enablePaginator();

		Assert::true($this->grid->isPaginatorEnabled());
	}


	public function testItemsPerPage()
	{
		Assert::type('int', $this->grid->getItemsPerPage());

		$this->grid->setItemsPerPage(50);

		Assert::same(50, $this->grid->getItemsPerPage());
	}


	public function testGetData_withoutPaginator()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$this->grid->disablePaginator();

		Assert::same($data, $this->grid->getData());
	}


	public function testGetTotalCount_withPaginator()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('getCount')->once()->andReturn(5)->getMock()
			->shouldReceive('limit')->once()->getMock()
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		Assert::same(5, $this->grid->getTotalCount());
	}


	public function testGetTotalCount_withoutPaginator()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('getCount')->once()->andReturn(5)->getMock()
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$this->grid->disablePaginator();

		Assert::same(5, $this->grid->getTotalCount());
	}


	public function testGetData_transformData()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$this->grid->disablePaginator();

		$this->grid->transformData(function($number) {
			return $number + 1;
		});

		Assert::same([
			2, 3, 4, 5, 6
		], $this->grid->getData());
	}


	public function testGetData_withPaginator()
	{
		$itemsPerPage = $this->grid->getItemsPerPage();
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('getCount')->once()->andReturn(2)->getMock()
			->shouldReceive('limit')->once()->with(0, $itemsPerPage)->getMock()
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		Assert::same($data, $this->grid->getData());
	}


	public function testGetData_views()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('getData')->once()->andReturn($data)->getMock()
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$viewCalled = false;

		$this->grid->disablePaginator();
		$this->grid->view = 'all';
		$this->grid->addView('all', 'All', function() use (&$viewCalled) {
			$viewCalled = true;
		});

		Assert::same($data, $this->grid->getData());
		Assert::true($viewCalled);
	}

}


run(new NoGridTest);