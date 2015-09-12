<?php

namespace Carrooi\NoGrid\DataSource;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
abstract class BaseDoctrineDataSource
{


	/** @var int */
	private $hydrationMode = AbstractQuery::HYDRATE_OBJECT;

	/** @var bool */
	private $useOutputWalkers;

	/** @var bool */
	private $fetchJoinCollections = true;


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
	 * @param \Doctrine\ORM\Query $query
	 * @param int $hydrationMode
	 * @param null|int $maxResults
	 * @param null|int $firstResult
	 * @param bool $fetchJoinCollections
	 * @param null|bool $useOutputWalkers
	 * @return array
	 */
	protected static function fetchDataFromQuery(Query $query, $hydrationMode = Query::HYDRATE_OBJECT, $maxResults = null, $firstResult = null, $fetchJoinCollections = true, $useOutputWalkers = null)
	{
		$query->setHydrationMode($hydrationMode);

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

}
