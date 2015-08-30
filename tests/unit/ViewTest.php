<?php

namespace CarrooiTests\Unit;

use Carrooi\NoGrid\View;
use Codeception\TestCase\Test;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ViewTest extends Test
{


	public function testName()
	{
		$view = new View('name', 'title', function() {});

		$this->assertSame('name', $view->getName());
	}


	public function testTitle()
	{
		$view = new View('name', 'title', function() {});

		$this->assertSame('title', $view->getTitle());
	}


	public function testLink()
	{
		$views = [
			new View('view1', 'View 1', function() {}),
			new View('view2', 'View 2', function() {}),
		];

		$grid = \Mockery::mock('Carrooi\NoGrid\NoGrid')
			->shouldReceive('getViews')->twice()->andReturn($views)->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => '', 'paginator-page' => null])->andReturn('link')->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => 'view2', 'paginator-page' => null])->andReturn('link')->getMock();

		$this->assertNull($views[0]->getLink());
		$this->assertNull($views[1]->getLink());

		$views[0]->onAttached($grid);
		$views[1]->onAttached($grid);

		$this->assertSame('link', $views[0]->getLink());
		$this->assertSame('link', $views[1]->getLink());
	}


	public function testCurrent()
	{
		$views = [
			new View('view1', 'View 1', function() {}),
			new View('view2', 'View 2', function() {}),
		];

		$grid = \Mockery::mock('Carrooi\NoGrid\NoGrid')
			->shouldReceive('getViews')->twice()->andReturn($views)->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => '', 'paginator-page' => null])->andReturn('link')->getMock()
			->shouldReceive('link')->once()->with('this!', ['view' => 'view2', 'paginator-page' => null])->andReturn('link')->getMock();

		$grid->view = 'view1';

		$this->assertFalse($views[0]->isCurrent());
		$this->assertFalse($views[1]->isCurrent());

		$views[0]->onAttached($grid);
		$views[1]->onAttached($grid);

		$this->assertTrue($views[0]->isCurrent());
		$this->assertFalse($views[1]->isCurrent());
	}

}
