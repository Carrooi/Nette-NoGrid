<?php

namespace Carrooi\NoGrid\DataSource\Doctrine;

use Carrooi\NoGrid\Condition;
use Carrooi\NoGrid\InvalidArgumentException;
use Carrooi\NoGrid\NotImplementedException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kdyby\Doctrine\NativeQueryBuilder;
use Kdyby\Doctrine\QueryBuilder;
use Nette\Utils\Strings;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class BaseDataSource
{


	/** @var int */
	private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

	/** @var bool */
	private $useOutputWalkers;

	/** @var bool */
	private $fetchJoinCollections = true;


	/** @var int */
	private static $parametersCount = 0;


	/**
	 * @return int
	 */
	public function getHydrationMode()
	{
		return $this->hydrationMode;
	}


	/**
	 * @param int $hydrationMode
	 * @return $this
	 */
	public function setHydrationMode($hydrationMode)
	{
		$this->hydrationMode = $hydrationMode;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasUseOutputWalkers()
	{
		return $this->useOutputWalkers !== null;
	}


	/**
	 * @return bool
	 */
	public function getUseOutputWalkers()
	{
		return $this->useOutputWalkers;
	}


	/**
	 * @param bool $useOutputWalkers
	 * @return $this
	 */
	public function setUseOutputWalkers($useOutputWalkers)
	{
		$this->useOutputWalkers = (bool) $useOutputWalkers;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function getFetchJoinCollections()
	{
		return $this->fetchJoinCollections;
	}


	/**
	 * @param bool $fetchJoinCollections
	 * @return $this
	 */
	public function setFetchJoinCollections($fetchJoinCollections)
	{
		$this->fetchJoinCollections = (bool) $fetchJoinCollections;
		return $this;
	}


	/**
	 * @param \Kdyby\Doctrine\QueryBuilder $qb
	 * @param int $hydrationMode
	 * @param \Carrooi\NoGrid\Condition[] $conditions
	 * @param null|int $maxResults
	 * @param null|int $firstResult
	 * @param array $hints
	 * @param bool $fetchJoinCollections
	 * @param null|bool $useOutputWalkers
	 * @return array
	 */
	protected static function fetchDataFromQuery(QueryBuilder $qb, $hydrationMode = Query::HYDRATE_OBJECT, array $conditions, $maxResults = null, $firstResult = null, array $hints = [], $fetchJoinCollections = true, $useOutputWalkers = null)
	{
		foreach ($conditions as $condition) {
			self::makeWhere($qb, $condition);
		}

		$query = $qb->getQuery();
		$query->setHydrationMode($hydrationMode);

		foreach ($hints as $name => $value) {
			$query->setHint($name, $value);
		}

		if ($maxResults !== null || $firstResult !== null) {
			$result = new Paginator($query, $fetchJoinCollections);

			if ($useOutputWalkers !== null) {
				$result->setUseOutputWalkers($useOutputWalkers);
			}
		} else {
			$result = $query->getResult();
		}

		$data = [];
		foreach ($result as $item) {
			$data[] = is_array($item) && array_key_exists(0, $item) ? $item[0] : $item;
		}

		return $data;
	}


	/**
	 * @param \Kdyby\Doctrine\QueryBuilder|\Kdyby\Doctrine\NativeQueryBuilder $qb
	 * @param \Carrooi\NoGrid\Condition $condition
	 * @throws \Carrooi\NoGrid\NotImplementedException
	 */
	public static function makeWhere($qb, Condition $condition)
	{
		if (!$qb instanceof QueryBuilder && !$qb instanceof NativeQueryBuilder) {
			throw new InvalidArgumentException;
		}

		$column = $condition->getColumn();
		$value = $condition->getValue();

		if (!Strings::contains($column, '.')) {
			$column = current($qb->getRootAliases()). '.'. $column;
		}

		$parameter = ':grid'. self::$parametersCount;

		$options = $condition->getOptions();
		$lower = isset($options[Condition::CASE_INSENSITIVE]) && $options[Condition::CASE_INSENSITIVE];

		if ($lower) {
			$column = 'lower('. $column. ')';
			$parameter = 'lower('. $parameter. ')';
		}

		if ($condition->getType() === Condition::SAME) {
			$qb->andWhere($column. ' = '. $parameter);

		} elseif ($condition->getType() === Condition::NOT_SAME) {
			$qb->andWhere($column. ' != '. $parameter);

		} elseif ($condition->getType() === Condition::IS_NULL) {
			$qb->andWhere($column. ' IS NULL');

		} elseif ($condition->getType() === Condition::IS_NOT_NULL) {
			$qb->andWhere($column. ' IS NOT NULL');

		} elseif ($condition->getType() === Condition::LIKE) {
			$qb->andWhere($column. ' LIKE '. $parameter);

		} else {
			throw new NotImplementedException('Filtering condition is not implemented.');
		}

		if (!in_array($condition->getType(), [Condition::IS_NULL, Condition::IS_NOT_NULL])) {
			$qb->setParameter('grid'. self::$parametersCount, $value);
		}

		self::$parametersCount++;
	}

}
