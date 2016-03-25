<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Tag;

class TagManager extends BaseManager
{

	public function create($name)
	{
		$bean = new Tag();
		$this->updateInternal($bean, $name);
		parent::persist($bean);

		return $bean;
	}

	public function update(Tag $bean, $name)
	{
		$this->updateInternal($bean, $name);
 		parent::persist($bean);
	}

	public function getByName($name)
	{
		return
		$this
		->em
		->createQuery('SELECT b FROM ' . $this->class . ' b WHERE b.name = :name')->setParameter(':name', $name)
		->getOneOrNullResult();
	}

	protected function updateInternal(Tag $bean, $name)
	{
		$bean->setName($name);
	}

}