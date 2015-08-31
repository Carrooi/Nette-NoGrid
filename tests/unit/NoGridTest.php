<?php

namespace CarrooiTests\Unit;

use Carrooi\NoGrid\NoGrid;
use Codeception\TestCase\Test;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
final class NoGridTest extends Test
{


	/** @var \Carrooi\NoGrid\DataSource\IDataSource|\Mockery\MockInterface */
	private $source;

	/** @var \Carrooi\NoGrid\NoGrid */
	private $grid;


	public function setUp()
	{
		parent::setUp();

		$this->source = \Mockery::mock('Carrooi\NoGrid\DataSource\IDataSource');
		$paginatorTemplateProvider = \Mockery::mock('Carrooi\NoGrid\IPaginatorTemplateProvider');
		$this->grid = new NoGrid($this->source, $paginatorTemplateProvider);
	}


	public function testViews()
	{
		$this->assertCount(0, $this->grid->getViews());

		$this->grid->addView('view1', 'View 1', function() {});
		$this->grid->addView('view2', 'View 2', function() {});

		$this->assertTrue($this->grid->hasView('view1'));
		$this->assertTrue($this->grid->hasView('view2'));
		$this->assertFalse($this->grid->hasView('view3'));

		$views = $this->grid->getViews();

		$this->assertCount(2, $views);
		$this->assertInstanceOf('Carrooi\NoGrid\View', $views[0]);
		$this->assertInstanceOf('Carrooi\NoGrid\View', $views[1]);

		$this->assertSame('view1', $views[0]->getName());
		$this->assertSame('View 1', $views[0]->getTitle());
		$this->assertSame('view2', $views[1]->getName());
		$this->assertSame('View 2', $views[1]->getTitle());
	}


	public function testDisablePaginator()
	{
		$this->assertTrue($this->grid->isPaginatorEnabled());

		$this->grid->disablePaginator();

		$this->assertFalse($this->grid->isPaginatorEnabled());

		$this->grid->enablePaginator();

		$this->assertTrue($this->grid->isPaginatorEnabled());
	}


	public function testItemsPerPage()
	{
		$this->assertInternalType('int', $this->grid->getItemsPerPage());

		$this->grid->setItemsPerPage(50);

		$this->assertSame(50, $this->grid->getItemsPerPage());
	}


	public function testGetData_withoutPaginator()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$this->grid->disablePaginator();

		$this->assertSame($data, $this->grid->getData());
	}


	public function testGetTotalCount_withPaginator()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('getCount')->once()->andReturn(5)->getMock()
			->shouldReceive('limit')->once()->getMock()
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$this->assertSame(5, $this->grid->getTotalCount());
	}


	public function testGetTotalCount_withoutPaginator()
	{
		$data = [1, 2, 3, 4, 5];

		$this->source
			->shouldReceive('getCount')->once()->andReturn(5)->getMock()
			->shouldReceive('fetchData')->once()->andReturn($data)->getMock();

		$this->grid->disablePaginator();

		$this->assertSame(5, $this->grid->getTotalCount());
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

		$this->assertSame([
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

		$this->assertSame($data, $this->grid->getData());
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

		$this->assertSame($data, $this->grid->getData());
		$this->assertTrue($viewCalled);
	}

}
