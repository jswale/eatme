<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Category;

class CategoryManager extends BaseManager
{

	public function create($name)
	{
		$bean = new Category();
		$this->updateInternal($bean, $name);
		parent::persist($bean);

		return $bean;
	}

	public function update(Category $bean, $name)
	{
		$this->updateInternal($bean, $name);
 		parent::persist($bean);
	}

	protected function updateInternal(Category $bean, $name)
	{
		$bean->setName($name);
	}

}