<?php

namespace CarrooiTests\Unit;

use Carrooi\NoGrid\Latte\Macros;
use Carrooi\NoGrid\View;
use Codeception\TestCase\Test;
use Latte;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGrid_LatteTest extends Test
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

		$this->grid = \Mockery::mock('Carrooi\NoGrid\NoGrid');
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
			->shouldReceive('link')->twice()->andReturn('link')->getMock();

		$this->grid->view = 'second';

		$views[0]->onAttached($this->grid);
		$views[1]->onAttached($this->grid);

		$this->assertEquals(
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
			->shouldReceive('getData')->once()->andReturn([])->getMock();

		$this->assertEquals(
			file_get_contents(__DIR__. '/expected/noGrid.empty.html'),
			$this->latte->renderToString(
				__DIR__. '/templates/noGrid.empty.latte',
				['_control' => ['grid' => $this->grid]]
			)
		);
	}


	public function testMacros_notInGrid()
	{
		$this->setExpectedException('Latte\CompileException', 'Macro no-grid-data-as must be inside of no-grid macro.');

		$this->latte->renderToString(
			__DIR__. '/templates/noGrid.notInGrid.latte'
		);
	}


	public function testMacros_nested()
	{
		$this->setExpectedException('Latte\CompileException', 'Nesting no-grid macros is not allowed.');

		$this->latte->renderToString(
			__DIR__. '/templates/noGrid.nested.latte'
		);
	}

}
