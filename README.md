# NoGrid

Definitely not a grid, just a simple control for printing data in customized templates with paginator.

It's not a good thing to always just show some automatically generated grid with data, mainly in frontend and that 
package is for that moments.

## Features

**It has:**

* Paginator with custom templates option
* Latte macros for simplified templates
* Views (eg. for archived and not archived data)
* Different data sources

**It hasn't:**

* Default grid template
* CSS styles
* JS scripts
* Sorting
* Filtering
* Forms

It may get some of these features in future.

## Installation

```
$ composer require carrooi/no-grid
```

Now you can register Nette's extension

```yaml
extensions:
	grid: Carrooi\NoGrid\DI\NoGridExtension
```

## Configuration

```yaml
grid:
	itemsPerPage: 20
	paginator:
		template: %appDir%/paginator.latte
```

* `itemsPerPage`: default is 10
* `paginator/template`: not required

## Definition

```php

use Carrooi\NoGrid\DataSource\ArrayDataSource;
use Nette\Application\UI\Presenter;

class BooksPresenter extends Presenter
{

	/** @var \Carrooi\NoGrid\INoGridFactory @inject */
	public $gridFactory;
	
	protected function createComponentBooksGrid()
	{
		$dataSource = new ArrayDataSource([			// Read more about data sources below
			['title' => 'Lord of the Rings'],
			['title' => 'Harry Potter'],
			['title' => 'Narnia'],
		]);
	
		$grid = $this->gridFactory->create($dataSource);
		
		return $grid;
	}

}
```

## Printing

```smarty
<table n:no-grid="booksGrid">
	<thead>
		<tr>
			<th>Title</th>
		</tr>
	</thead>
	<tbody>
		<tr n:no-grid-data-as="$line">
			<td>{$line[title]}</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th>Display {$noGrid->getCount()} items from {$noGrid->getTotalCount()}</th>
		</tr>
		<tr>
			<td n:no-grid-not-empty>{control booksGrid:paginator}</td>
			<td n:no-grid-empty>No data found...</td>
		</tr>
	</tfoot>
</table>
```

### Latte macros

* `no-grid`: Begin grid rendering (similar to [{form}](http://doc.nette.org/cs/2.3/forms#toc-manualni-vykreslovani) macro)
* `no-grid-data-as`: Iterate over data from data source and save current line to given variable
* `no-grid-views-as`: Iterate over views from current NoGrid and save view data to given variable (see more about views below)
* `no-grid-not-empty`: Content will be processed only if there are some data
* `no-grid-empty`: Content will be processed only if there are no data

Also you can see that paginator can be rendered with `{control booksGrid:paginator}`.

## Views

Imagine that you want to create for example two tabs - "Active books" and "Sold books". This can be easily done with 
views.

**Definition:**

```php
protected function createComponentBooksGrid()
{
	$dataSource = new ArrayDataSource([			// Read more about data sources below
		[
			'title' => 'Lord of the Rings,
			'sold' => true,
		'],
		[
			'title' => 'Harry Potter,
			'sold' => false,
		'],
		[
			'title' => 'Narnia,
			'sold' => false,
		'],
	]);

	$grid = $this->gridFactory($dataSource);
	
	$grid->addView('active', 'Active', function(array &$data) {
		$data = array_filter($data, function($book) {
			return !$book['sold'];
		});
	});
	
	$grid->addView('sold', 'Sold out', function(array &$data) {
		$data = array_filter($data, function($book) {
			return $book['sold'];
		});
	});
	
	return $grid;
}
```

**Display:**

```smarty
<div n:no-grid="booksGrid">
	<ul>
		<li n:no-grid-views-as="$view">
			<a href="{$view->link}">
				{$view->title}
			</a>
		</li>
	</ul>
	<table>
		<!-- same like previous example -->
	</table>
</div>
```

## Data sources

* `Carrooi\NoGrid\DataSource\ArrayDataSource(array)`
* `Carrooi\NoGrid\DataSource\DoctrineDataSource(Doctrine\ORM\QueryBuilder)`
* `Carrooi\NoGrid\DataSource\DoctrineQueryObjectDataSource(Kdyby\Doctrine\QueryObject, Kdyby\Persistence\Queryable)`

## Changelog

* 1.0.0
	+ Initial version
