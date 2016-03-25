<?php

namespace App\Manager;

use Silex\Application;
use Doctrine\ORM\QueryBuilder;

use App\Domain\BaseEntity;

class BaseManager
{

	protected $app;
	protected $em;
	protected $qb;
	protected $class;

	function __construct(Application $app, $class)
	{
		$this->app = $app;
		$this->class = $class;
		$this->em = $this->app['orm.em'];
		$this->qb = $this->app['orm.qb'];
	}

	public function getById($id)
	{
		return $this->em->find($this->class, $id);
	}

	public function getRandom()
	{
		$qb = $this->em->createQueryBuilder();
		$qb
		->select("b")
		->addSelect('RAND() as HIDDEN rand')->orderBy('rand')
		->from($this->class, "b")
		->setMaxResults(1)
		;
		return	$qb->getQuery()->getOneOrNullResult();
	}

	public function getSearchResult($page = 1)
	{
		return $this->__getSearchResultBy($this->em->createQueryBuilder()->from($this->class, "b"), $page);
	}

	public function getAll()
	{
		return
		$this
		->em
		->createQuery('SELECT b FROM ' . $this->class . ' b')
		->getResult();
	}


	public function getAllAsList()
	{
		$list = array();
		foreach($this->getAll() as $bean) {
			$list[$bean->getId()] = $bean;
		}
		return $list;
	}

	public function getCount()
	{
		return
		$this
		->em
		->createQuery('SELECT count(b.id) FROM ' . $this->class . ' b')
		->getSingleScalarResult();
	}

	public function persist(BaseEntity $entity)
	{
		$this->em->persist($entity);
		$this->flush();

		return $this;
	}

	public function remove(BaseEntity $entity)
	{
		$this->em->remove($entity);
		$this->flush();

		return $this;
	}

	public function flush()
	{
		$this->em->flush();

		return $this;
	}


	protected function __getSearchResultBy(QueryBuilder $qb, $page = 1)
	{
		$per_page = $this->app['searchResult.perPage'];

		$qb->select("DISTINCT b")->orderBy('b.id', 'desc');
		$query = $qb->getQuery();

		$query->setMaxResults($per_page);
		$query->setFirstResult($per_page * ($page-1));
		$result = $query->getResult();

		// Total
		$qb->select("count(DISTINCT b.id)");
		$query = $qb->getQuery();
		$total = $query->getSingleScalarResult();

		return $this->__getSearchResult($page, $result, $total, $per_page);
	}

	private function extractIds($results)
	{
		$ids = array();
		foreach($results as $result)
		{
			array_push($ids, $result["id"]);
		}
		return $ids;
	}

	protected function __getIdsResultBy(QueryBuilder $qb)
	{
		$qb->select("DISTINCT b.id");
		$query = $qb->getQuery();

		return $this->extractIds($query->getScalarResult());
	}

	private function __getSearchResult($page, $result, $total, $per_page) {
		return array(
				'page' => $page,
				'per_page' => $per_page,
				'records' => $result,
				'total' => $total,
				'total_page' => ceil($total/$per_page),
		);
	}

}