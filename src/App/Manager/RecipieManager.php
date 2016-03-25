<?php

namespace App\Manager;

use Doctrine\ORM\QueryBuilder;

use App\Manager\BaseManager;
use App\Domain\Category;
use App\Domain\Recipie;
use App\Domain\Tag;
use App\Domain\User;

class RecipieManager extends BaseManager
{

	public function create(User $user, $name, $description, $quantity, Category $category)
	{
		$bean = new Recipie();
		$bean->setCreateDate(new \DateTime());
		$bean->setUser($user);
		$this->updateInternal($bean, $name, $description, $quantity, $category);
		parent::persist($bean);

		return $bean;
	}

	public function update(Recipie $bean, $name, $description, $quantity, Category $category)
	{
		$bean->setUpdateDate(new \DateTime());
		$this->updateInternal($bean, $name, $description, $quantity);
 		parent::persist($bean);
	}

	public function removeUser(User $user)
	{
		$qb = $this->em->createQueryBuilder();
		$qb->update($this->class, "b")
		->set('b.author', ':name')->setParameter(':name', $user->getName())
		->set('b.user', 'null')
		->where('b.user = :user')->setParameter(':user', $user)
		;

		$query = $qb->getQuery();

		$query->execute();
	}

	public function getGroupByCategoryCount()
	{
		$qb = $this->em->createQueryBuilder();
		$qb
		->select("c.id, count(1) as cnt")
		->from($this->class, "b")
		->innerJoin("b.category", "c")
		->groupBy("c.id")
		->orderBy("cnt DESC, c.name");

		$query = $qb->getQuery();

		$categories = $this->app['category.manager']->getAllAsList();

		$data = array();
		foreach($query->getResult() as $result) {
			array_push($data, array(
					'total'    => $result["cnt"],
					'group' => $categories[$result["id"]]
			));
		}

		return $data;
	}

	public function getGroupByTagCount()
	{
		$qb = $this->em->createQueryBuilder();
		$qb
		->select("t.id, count(1) as cnt")
		->from($this->class, "b")
		->innerJoin("b.tags", "t")
		->groupBy("t.id")
		->orderBy("cnt DESC, t.name")
		->setMaxResults(10)
		;
		$query = $qb->getQuery();

		$tags = $this->app['tag.manager']->getAllAsList();

		$data = array();
		foreach($query->getResult() as $result) {
			array_push($data, array(
					'total'    => $result["cnt"],
					'group' => $tags[$result["id"]]
			));
		}

		return $data;
	}

	public function getGroupByUserCount()
	{
		$qb = $this->em->createQueryBuilder();
		$qb
		->select("u.id, count(1) as cnt")
		->from($this->class, "b")
		->innerJoin("b.user", "u")
		->groupBy("b.user")
		->orderBy("cnt DESC, u.name");

		$query = $qb->getQuery();

		$users = $this->app['user.manager']->getAllAsList();

		$data = array();
		foreach($query->getResult() as $result) {
			array_push($data, array(
					'total' => $result["cnt"],
					'group' => $users[$result["id"]],
			));
		}

		return $data;
	}

	public function getSearchResultByCategory(Category $category, $page = 1)
	{
		return parent::__getSearchResultBy($this->byCategoryBaseCriteria($this->em->createQueryBuilder(), $category), $page);
	}

	public function getSearchResultByTag(Tag $tag, $page = 1)
	{
		return parent::__getSearchResultBy($this->byTagBaseCriteria($this->em->createQueryBuilder(), $tag), $page);
	}

	public function getSearchResultByUser(User $user, $page = 1)
	{
		return parent::__getSearchResultBy($this->byUserBaseCriteria($this->em->createQueryBuilder(), $user), $page);
	}

	public function getSearchResultByQuery($query, $page = 1)
	{
		return parent::__getSearchResultBy($this->byQueryBaseCriteria($this->em->createQueryBuilder(), $query), $page);
	}

	public function getSearchResultByIngredients($category, $includes, $excludes, $page = 1)
	{
		return parent::__getSearchResultBy($this->byIngredientBaseCriteria($this->em->createQueryBuilder(), $category, $includes, $excludes), $page);
	}

	private function byTagBaseCriteria(QueryBuilder $qb, Tag $tag) {
		return $qb
		->from($this->class, "b")
		->innerJoin("b.tags", "t")
		->where("t.id = :id")->setParameter(':id', $tag->getId());
	}

	private function byQueryBaseCriteria(QueryBuilder $qb, $query) {
		return $qb
		->from($this->class, "b")
		->leftJoin("b.ingredientGroups", "ig")
		->leftJoin("ig.ingredients", "i")
		->leftJoin("b.stepGroups", "sg")
		->leftJoin("sg.steps", "s")
		->where("b.name like :query")
		->orWhere("b.description like :query")
		->orWhere("i.name like :query")
		->orWhere("s.description like :query")
		->setParameter(':query', '%' . $query .'%')
		;
	}

	/**
	 * @param QueryBuilder $qb
	 * @param Category $category
	 * @param array $includes
	 * @param array $excludes
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function byIngredientBaseCriteria(QueryBuilder $qb, Category $category, $includes, $excludes) {

		$includeIds = array();
		if(count($includes) > 0) {
			$qbs = $this->em->createQueryBuilder();
			$qbs->from($this->class, "b");
			foreach($includes as $idx => $include) {
				$include = trim($include);
				if("" != $include) {
					$qbs
					->innerJoin("b.ingredientGroups", "ig" . $idx)
					->innerJoin("ig" . $idx . ".ingredients", "i" . $idx)
					->andWhere("i" . $idx . ".name like :query" . $idx)
					->setParameter(':query' . $idx, '%' . $include .'%');
				}
			}
			$includeIds = parent::__getIdsResultBy($qbs);
		}

		$excludeIds = array();
		if(count($excludes) > 0) {
			$qbs = $this->em->createQueryBuilder();
			$qbs->from($this->class, "b");
			foreach($excludes as $idx => $exclude) {
				$exclude = trim($exclude);
				if("" != $exclude) {
					$qbs
					->leftJoin("b.ingredientGroups", "ig" . $idx)
					->leftJoin("ig" . $idx . ".ingredients", "i" . $idx)
					->orWhere("i" . $idx . ".name like :query" . $idx)
					->setParameter(':query' . $idx, '%' . $exclude .'%');
				}
			}
			$excludeIds = parent::__getIdsResultBy($qbs);
		}

		$qb->from($this->class, "b");
		$qb->where("b.category = :category")->setParameter(":category", $category);
		if(count($includeIds)>0) {
			$qb->andWhere("b.id in(:includes)")->setParameter(':includes', $includeIds);
		}
		if(count($excludeIds)>0) {
			$qb->andWhere("b.id not in(:excludes)")->setParameter(':excludes', $excludeIds);
		}

		return $qb;
	}

	private function byCategoryBaseCriteria(QueryBuilder $qb, Category $category) {
		return $qb
		->from($this->class, "b")
		->where("b.category = :id")->setParameter(':id', $category->getId());
	}

	private function byUserBaseCriteria(QueryBuilder $qb, User $user) {
		return $qb
		->from($this->class, "b")
		->where("b.user = :user")->setParameter(':user', $user);
	}

	protected function updateInternal(Recipie $bean, $name, $description, $quantity, Category $category)
	{
		$bean->setName($name);
		$bean->setDescription($description);
		$bean->setQuantity($quantity);
		$bean->setCategory($category);
	}

}