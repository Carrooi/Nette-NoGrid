<?php

namespace Carrooi\NoGrid;

use Nette\Application\UI\Control;
use Nette\Utils\Paginator;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class VisualPaginator extends Control
{


	/** @var \Nette\Utils\Paginator */
	private $paginator;

	/** @var \Carrooi\NoGrid\IPaginatorTemplateProvider */
	private $templateProvider;

	/** @var string */
	private $templatePath;


	/** @var int @persistent */
	public $page = 1;


	public function __construct(IPaginatorTemplateProvider $templateProvider)
	{
		parent::__construct();

		$this->templateProvider = $templateProvider;
		$this->paginator = new Paginator;
	}


	/**
	 * @return \Nette\Utils\Paginator
	 */
	public function getPaginator()
	{
		return $this->paginator;
	}


	/**
	 * @return string
	 */
	public function getTemplatePath()
	{
		if (!$this->templatePath) {
			$this->templatePath = $this->templateProvider->getTemplatePath();
		}

		return $this->templatePath;
	}


	/**
	 * @param string $path
	 * @return $this
	 */
	public function setTemplatePath($path)
	{
		$this->templatePath = $path;
		return $this;
	}


	/**
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}


	/**
	 * @return array
	 */
	public function getSteps()
	{
		$paginator = $this->getPaginator();

		$page = $paginator->page;

		if ($paginator->pageCount < 2) {
			$steps = [$page];

		} else {
			$arr = range(max($paginator->firstPage, $page - 3), min($paginator->lastPage, $page + 3));
			$count = 4;
			$quotient = ($paginator->pageCount - 1) / $count;

			for ($i = 0; $i <= $count; $i++) {
				$arr[] = round($quotient * $i) + $paginator->firstPage;
			}

			sort($arr);

			$steps = array_values(array_unique($arr));
		}

		return $steps;
	}


	/**
	 * @param array $params
	 */
	public function loadState(array $params)
	{
		parent::loadState($params);

		$this->paginator->setPage($this->page);
	}


	public function render()
	{
		$this->template->paginator = $this->paginator;
		$this->template->steps = $this->getSteps();

		$this->template->setFile($this->getTemplatePath());
		$this->template->render();
	}

}
