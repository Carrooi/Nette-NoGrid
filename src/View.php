<?php

namespace Carrooi\NoGrid;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class View
{


	/** @var string */
	private $name;

	/** @var string */
	private $title;

	/** @var string */
	private $link;

	/** @var bool */
	private $current = false;

	/** @var callable */
	private $fn;


	/**
	 * @param string $name
	 * @param string $title
	 * @param callable $fn
	 */
	public function __construct($name, $title, callable $fn)
	{
		$this->name = (string) $name;
		$this->title = $title;
		$this->fn = $fn;
	}


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * @return string
	 */
	public function getLink()
	{
		return $this->link;
	}


	/**
	 * @return bool
	 */
	public function isCurrent()
	{
		return $this->current;
	}


	/**
	 * @param \Carrooi\NoGrid\NoGrid $grid
	 */
	public function onAttached(NoGrid $grid)
	{
		$isFirst = $grid->getViews()[0] === $this;
		$name = $isFirst ? '' : $this->name;

		$this->link = $grid->link('this!', ['view' => $name, 'paginator-page' => null]);

		if ($grid->view === $this->name) {
			$this->current = true;
		}
	}


	/**
	 * @param mixed $data
	 */
	public function limitData(&$data)
	{
		$fn = $this->fn;
		$fn($data);
	}

}
