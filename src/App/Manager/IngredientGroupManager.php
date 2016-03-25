<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Recipie;
use App\Domain\IngredientGroup;

class IngredientGroupManager extends BaseManager
{

	public function create(Recipie $recipie, $name)
	{
		$bean = new IngredientGroup();
		$bean->setRecipie($recipie);
		$this->updateInternal($bean, $name);
		parent::persist($bean);

		return $bean;
	}

	public function update(IngredientGroup $bean, $name)
	{
		$this->updateInternal($bean, $name);
		parent::persist($bean);
	}

	protected function updateInternal(IngredientGroup $bean, $name)
	{
		$bean->setName($name);
	}

}