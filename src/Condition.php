<?php

namespace Carrooi\NoGrid;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Condition
{


	const FORM_OPTION = 'no-grid-condition';

	const CASE_INSENSITIVE = 'insensitive';

	const SAME = 1;
	const NOT_SAME = 2;
	const IS_NULL = 3;
	const IS_NOT_NULL = 4;
	const LIKE = 5;
	const CALLBACK = 6;


	/** @var string */
	private $column;

	/** @var string */
	private $value;

	/** @var int */
	private $type = self::SAME;

	/** @var array */
	private $options;


	/**
	 * @param string $column
	 * @param string $value
	 * @param int $type
	 * @param array $options
	 */
	public function __construct($column, $value, $type = self::SAME, array $options = [])
	{
		$this->column = $column;
		$this->value = $value;
		$this->type = $type;
		$this->options = $options;

		if($type === self::CALLBACK && ( ! array_key_exists(self::CALLBACK, $options) || ! is_callable($options[self::CALLBACK]))){
			throw new InvalidStateException('CALLBACK option must be defined and callable for condition of CALLBACK type.');
		}
	}


	/**
	 * @return string
	 */
	public function getColumn()
	{
		return $this->column;
	}


	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}


	/**
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options;
	}

}
