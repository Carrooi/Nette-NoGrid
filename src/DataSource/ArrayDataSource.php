<?php

namespace Carrooi\NoGrid\DataSource;

use Carrooi\NoGrid\Condition;
use Carrooi\NoGrid\InvalidStateException;
use Carrooi\NoGrid\NotImplementedException;
use Nette\Utils\Strings;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class ArrayDataSource implements IDataSource
{


	/** @var array */
	private $dataDefinition;

	/** @var array */
	private $data;


	/**
	 * @param array $data
	 */
	public function __construct(array $data)
	{
		$this->dataDefinition = $data;
	}


	/**
	 * @return int
	 */
	public function getCount()
	{
		$this->fetchData();
		return count($this->data);
	}


	/**
	 * @return array
	 */
	public function &getData()
	{
		return $this->dataDefinition;
	}


	/**
	 * @return array
	 */
	public function fetchData()
	{
		if ($this->data === null) {
			$this->data = $this->dataDefinition;
		}

		return $this->data;
	}


	/**
	 * @param \Carrooi\NoGrid\Condition[] $conditions
	 */
	public function filter(array $conditions)
	{
		foreach ($conditions as $condition) {
			if($condition->getType() === Condition::CALLBACK){
				$result = call_user_func($condition->getOptions()[Condition::CALLBACK], $this->fetchData(), $condition->getValue());

				if($result === null){
					throw new InvalidStateException('Filter callback must return filtered array for ArrayDataSource, null given.');
				}

				$this->data = $result;

			} else {
				$this->data = array_filter($this->fetchData(), function($row) use ($condition) {
					if (!isset($row[$condition->getColumn()])) {
						return false;
					}

					return $this->compare($condition->getType(), $row[$condition->getColumn()], $condition->getValue(), $condition->getOptions());
				});
			}
		}
	}


	/**
	 * @param int $offset
	 * @param int $limit
	 */
	public function limit($offset, $limit)
	{
		$this->data = array_slice($this->fetchData(), $offset, $limit);
	}


	/**
	 * @param int $type
	 * @param mixed $actual
	 * @param mixed $expected
	 * @param array $options
	 * @return bool
	 */
	private function compare($type, $actual, $expected = null, array $options = [])
	{
		if (isset($options[Condition::CASE_INSENSITIVE]) && $options[Condition::CASE_INSENSITIVE]) {
			$actual = Strings::lower($actual);
			$expected = Strings::lower($expected);
		}

		if ($type === Condition::SAME) {
			return $actual === $expected;

		} elseif ($type === Condition::NOT_SAME) {
			return $actual !== $expected;

		} elseif ($type === Condition::IS_NULL) {
			return $actual === null;

		} elseif ($type === Condition::IS_NOT_NULL) {
			return $actual !== null;

		} elseif ($type === Condition::LIKE) {
			$actual = Strings::toAscii($actual);
			$expected = Strings::toAscii($expected);

			$pattern = str_replace('%', '(.|\s)*', preg_quote($expected, '/'));
			return (bool) preg_match("/^{$pattern}$/i", $actual);

		} else {
			throw new NotImplementedException('Filtering condition is not implemented.');
		}
	}

}
