<?php

namespace Carrooi\NoGrid;

use Carrooi\NoGrid\DataSource\IDataSource;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Control;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGrid extends Control
{


	/** @var \Carrooi\NoGrid\DataSource\IDataSource */
	private $dataSource;

	/** @var array */
	private $views = [];

	/** @var bool */
	private $paginatorEnabled = true;

	/** @var int */
	private $itemsPerPage = 10;

	/** @var array */
	private $data;

	/** @var int */
	private $count;

	/** @var int */
	private $totalCount;


	/** @var string @persistent */
	public $view = '';


	/**
	 * @param \Carrooi\NoGrid\DataSource\IDataSource $dataSource
	 */
	public function __construct(IDataSource $dataSource)
	{
		parent::__construct();

		$this->dataSource = $dataSource;
	}


	/**
	 * @param string $name
	 * @param string $title
	 * @param callable $fn
	 * @return $this
	 */
	public function addView($name, $title, callable $fn)
	{
		$this->views[$name] = (object) [
			'name' => $name,
			'title' => $title,
			'fn' => $fn,
		];

		return $this;
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasView($name)
	{
		return isset($this->views[$name]);
	}


	/**
	 * @return array
	 */
	public function getViews()
	{
		$first = true;

		return array_map(function($view) use (&$first) {
			$v = [
				'title' => $view->title,
			];

			if ($this->getPresenter(false)) {
				$name = $first ? '' : $view->name;
				$first = false;

				$v['link'] = $this->link('this!', ['view' => $name, 'paginator-page' => null]);
			} else {
				$v['link'] = null;
			}

			return (object) $v;
		}, $this->views);
	}


	/**
	 * @return $this
	 */
	public function disablePaginator()
	{
		$this->paginatorEnabled = false;
		return $this;
	}


	/**
	 * @return $this
	 */
	public function enablePaginator()
	{
		$this->paginatorEnabled = true;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isPaginatorEnabled()
	{
		return $this->paginatorEnabled === true;
	}


	/**
	 * @return int
	 */
	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}


	/**
	 * @param int $itemsPerPage
	 * @return $this
	 */
	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = (int) $itemsPerPage;
		return $this;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		if ($this->count === null) {
			$this->count = count($this->getData());
		}

		return $this->count;
	}


	/**
	 * @return int
	 */
	public function getTotalCount()
	{
		if ($this->totalCount === null) {
			$this->getData();
		}

		return $this->totalCount;
	}


	/**
	 * @return array
	 */
	public function getData()
	{
		if ($this->data === null) {
			if ($this->view !== '') {
				$data = &$this->dataSource->getData();
				$fn = $this->views[$this->view]->fn;

				$fn($data);
			}

			$this->totalCount = $this->dataSource->getCount();

			if ($this->paginatorEnabled) {
				$vp = $this['paginator'];
				$paginator = $vp->getPaginator();

				$paginator->setItemsPerPage($this->getItemsPerPage());
				$paginator->setItemCount($this->totalCount);

				$this->dataSource->limit($paginator->getOffset(), $paginator->getItemsPerPage());
			}

			$this->data = $this->dataSource->fetchData();
		}

		return $this->data;
	}


	/**
	 * @return \Carrooi\NoGrid\VisualPaginator
	 */
	public function getVisualPaginator()
	{
		return $this['paginator'];
	}


	/**
	 * @return \Carrooi\NoGrid\VisualPaginator
	 */
	protected function createComponentPaginator()
	{
		return new VisualPaginator;
	}


	public function renderPaginator()
	{
		$this->template->setFile(__DIR__. '/templates/paginatorContainer.latte');
		$this->template->render();
	}


	/**
	 * @param array $params
	 * @throws \Nette\Application\BadRequestException
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);

		if ($this->view !== '') {
			if (!$this->hasView($this->view)) {
				throw new BadRequestException;
			}
		} elseif (!empty($this->views)) {
			reset($this->views);
			$this->view = key($this->views);
		}
	}


	/**
	 * @throws \Carrooi\NoGrid\LogicException
	 */
	public function render()
	{
		throw new LogicException('NoGrid '. $this->getName(). ' could not be automatically rendered.');
	}

}
