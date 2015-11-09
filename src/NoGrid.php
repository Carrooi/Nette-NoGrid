<?php

namespace Carrooi\NoGrid;

use Carrooi\NoGrid\DataSource\IDataSource;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGrid extends Control
{


	/** @var \Carrooi\NoGrid\DataSource\IDataSource */
	private $dataSource;

	/** @var \Carrooi\NoGrid\IPaginatorTemplateProvider */
	private $paginatorTemplateProvider;

	/** @var \Carrooi\NoGrid\View[] */
	private $views = [];

	/** @var \Nette\Application\UI\Form|null */
	private $filteringForm;

	/** @var array */
	private $filteringData;

	/** @var array */
	private $filteringConditions = [];

	/** @var bool */
	private $paginatorEnabled = true;

	/** @var callable */
	private $transformData;

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
	 * @param \Carrooi\NoGrid\IPaginatorTemplateProvider $paginatorTemplateProvider
	 */
	public function __construct(IDataSource $dataSource, IPaginatorTemplateProvider $paginatorTemplateProvider)
	{
		parent::__construct();

		$this->dataSource = $dataSource;
		$this->paginatorTemplateProvider = $paginatorTemplateProvider;

		$this->monitor('Nette\Application\UI\Presenter');
	}


	/**
	 * @param \Nette\Application\UI\Presenter $presenter
	 */
	public function attached($presenter)
	{
		parent::attached($presenter);

		if ($presenter instanceof Presenter) {
			foreach ($this->views as $view) {
				$view->onAttached($this);
			}
		}
	}


	/**
	 * @param string $name
	 * @param string $title
	 * @param callable $fn
	 * @return $this
	 */
	public function addView($name, $title, callable $fn)
	{
		$this->views[$name] = new View($name, $title, $fn);

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
	 * @return \Carrooi\NoGrid\View[]
	 */
	public function getViews()
	{
		return array_values($this->views);
	}


	/**
	 * @return bool
	 */
	public function hasFilteringForm()
	{
		return $this->filteringForm !== null;
	}


	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param string|null $resetButton
	 * @return $this
	 */
	public function setFilteringForm(Form $form, $resetButton = null)
	{
		$form->onSuccess[] = function(Form $form, array $values) use ($resetButton) {
			if ($resetButton && $form[$resetButton]->isSubmittedBy()) {
				$this->getPresenter()->redirect('this');
			}

			$this->filteringData = $values;
		};

		$this->filteringForm = $form;

		return $this;
	}


	/**
	 * @param string $column
	 * @param int $type
	 * @param array $options
	 * @param callable $value
	 * @return $this
	 */
	public function addFilter($column, $type = Condition::SAME, array $options = [], callable $value = null)
	{
		$this->filteringConditions[$column] = [
			'type' => $type,
			'options' => $options,
			'value' => $value,
		];

		return $this;
	}


	/**
	 * @param array $data
	 * @return array
	 */
	private function createConditions(array $data)
	{
		$conditions = [];

		foreach ($data as $column => $value) {
			if (isset($this->filteringConditions[$column])) {
				$type = $this->filteringConditions[$column]['type'];
				$options = $this->filteringConditions[$column]['options'];

				if ($this->filteringConditions[$column]['value']) {
					$value = call_user_func($this->filteringConditions[$column]['value'], $value);
				}
			} else {
				$type = Condition::SAME;
				$options = [];
			}

			$conditions[] = new Condition($column, $value, $type, $options);
		}

		return $conditions;
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
	 * @param callable $fn
	 * @return $this
	 */
	public function transformData(callable $fn)
	{
		$this->transformData = $fn;
		return $this;
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
			if ($this->data === null) {
				$this->getData();
			}

			if ($this->totalCount === null) {
				$this->totalCount = $this->dataSource->getCount();
			}
		}

		return $this->totalCount;
	}


	/**
	 * @return array
	 */
	public function getData()
	{
		if ($this->data === null) {
			if ($this->hasView($this->view)) {
				$data = &$this->dataSource->getData();
				$this->views[$this->view]->limitData($data);
			}

			if ($this->paginatorEnabled) {
				$this->totalCount = $this->dataSource->getCount();

				$vp = $this['paginator'];
				$paginator = $vp->getPaginator();

				$paginator->setItemsPerPage($this->getItemsPerPage());
				$paginator->setItemCount($this->totalCount);

				if ($this->totalCount === 0) {
					return $this->data = [];
				}

				$this->dataSource->limit($paginator->getOffset(), $paginator->getItemsPerPage());

				if ($this->hasFilteringForm() && !empty($this->filteringData)) {
					$conditions = $this->createConditions($this->filteringData);
					$this->dataSource->filter($conditions);
				}
			}

			$this->data = $this->dataSource->fetchData();

			if ($this->transformData) {
				$this->data = array_map($this->transformData, $this->data);
			}
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
		return new VisualPaginator($this->paginatorTemplateProvider);
	}


	public function renderPaginator()
	{
		$this->template->setFile(__DIR__. '/templates/paginatorContainer.latte');
		$this->template->render();
	}


	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentFilteringForm()
	{
		return $this->filteringForm;
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
