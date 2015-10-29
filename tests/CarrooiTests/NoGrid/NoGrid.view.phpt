<?php

/**
 * Test: Carrooi\NoGrid\NoGrid
 *
 * @testCase CarrooiTests\NoGrid\NoGrid_ViewTest
 */

namespace CarrooiTests\NoGrid;

use Carrooi\NoGrid\NoGrid;
use Carrooi\NoGrid\View;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../bootstrap.php';

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGrid_ViewTest extends TestCase
{


	public function tearDown()
	{
		\Mockery::close();
	}


	public function testName()
	{
		$view = new View('name', 'title', function() {});

		Assert::same('name', $view->getName());
	}


	public function testTitle()
	{
		$view = new View('name', 'title', function() {});

		Assert::same('title', $view->getTitle());
	}


	public function testLink()
	{
		$views = [
			new View('view1', 'View 1', function() {}),
			new View('view2', 'View 2', function() {}),
		];

		$grid = \Mockery::mock(NoGrid::class)
			->shouldReceive('getViews')->twice()->andReturn($views)->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => '', 'paginator-page' => null])->andReturn('link')->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => 'view2', 'paginator-page' => null])->andReturn('link')->getMock();

		Assert::null($views[0]->getLink());
		Assert::null($views[1]->getLink());

		$views[0]->onAttached($grid);
		$views[1]->onAttached($grid);

		Assert::same('link', $views[0]->getLink());
		Assert::same('link', $views[1]->getLink());
	}


	public function testCurrent()
	{
		$views = [
			new View('view1', 'View 1', function() {}),
			new View('view2', 'View 2', function() {}),
		];

		$grid = \Mockery::mock(NoGrid::class)
			->shouldReceive('getViews')->twice()->andReturn($views)->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => '', 'paginator-page' => null])->andReturn('link')->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => 'view2', 'paginator-page' => null])->andReturn('link')->getMock();

		$grid->view = 'view1';

		Assert::false($views[0]->isCurrent());
		Assert::false($views[1]->isCurrent());

		$views[0]->onAttached($grid);
		$views[1]->onAttached($grid);

		Assert::true($views[0]->isCurrent());
		Assert::false($views[1]->isCurrent());
	}

}


run(new NoGrid_ViewTest);