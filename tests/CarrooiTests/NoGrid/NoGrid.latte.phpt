<?php

/**
 * Test: Carrooi\NoGrid\NoGrid
 *
 * @testCase CarrooiTests\NoGrid\NoGrid_LatteTest
 */

namespace CarrooiTests\NoGrid;

use Carrooi\NoGrid\Latte\Macros;
use Carrooi\NoGrid\NoGrid;
use Carrooi\NoGrid\View;
use Latte;
use Latte\CompileException;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGrid_LatteTest extends TestCase
{


	/** @var \Latte\Engine */
	private $latte;

	/** @var \Carrooi\NoGrid\NoGrid|\Mockery\MockInterface */
	private $grid;


	public function setUp()
	{
		parent::setUp();

		$this->latte = new Latte\Engine;
		Macros::install($this->latte->getCompiler());

		$this->grid = \Mockery::mock(NoGrid::class);
	}


	public function tearDown()
	{
		\Mockery::close();
	}


	public function testMacros()
	{
		$views = [
			new View('first', 'View 1', function() {}),
			new View('second', 'View 2', function() {}),
		];

		$data = [
			['number' => 1],
			['number' => 2],
			['number' => 3],
		];

		$this->grid
			->shouldReceive('getViews')->times(3)->andReturn($views)->getMock()
			->shouldReceive('getCount')->once()->andReturn(3)->getMock()
			->shouldReceive('getData')->once()->andReturn($data)->getMock()
			->shouldReceive('isPaginatorEnabled')->once()->andReturn(false)->getMock()
			->shouldReceive('link')->twice()->andReturn('link')->getMock()
			->shouldReceive('hasFilteringForm')->twice()->andReturn(false)->getMock();

		$this->grid->view = 'second';

		$views[0]->onAttached($this->grid);
		$views[1]->onAttached($this->grid);

		Assert::equal(
			file_get_contents(__DIR__. '/expected/noGrid.html'),
			$this->latte->renderToString(
				__DIR__. '/templates/noGrid.latte',
				['_control' => ['grid' => $this->grid]]
			)
		);
	}


	public function testMacros_empty()
	{
		$this->grid
			->shouldReceive('getCount')->once()->andReturn(0)->getMock()
			->shouldReceive('getData')->once()->andReturn([])->getMock()
			->shouldReceive('hasFilteringForm')->twice()->andReturn(false)->getMock();

		Assert::equal(
			file_get_contents(__DIR__. '/expected/noGrid.empty.html'),
			$this->latte->renderToString(
				__DIR__. '/templates/noGrid.empty.latte',
				['_control' => ['grid' => $this->grid]]
			)
		);
	}


	public function testMacros_notInGrid()
	{
		Assert::exception(function() {
			$this->latte->renderToString(
				__DIR__. '/templates/noGrid.notInGrid.latte'
			);
		}, CompileException::class, "Thrown exception 'Macro no-grid-data-as must be inside of no-grid macro.' in .../templates/noGrid.notInGrid.latte:3");
	}


	public function testMacros_nested()
	{
		Assert::exception(function() {
			$this->latte->renderToString(
				__DIR__. '/templates/noGrid.nested.latte'
			);
		}, CompileException::class, "Thrown exception 'Nesting no-grid macros is not allowed.' in .../templates/noGrid.nested.latte:2");
	}

}


(new NoGrid_LatteTest)->run();