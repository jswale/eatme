<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Recipie;
use App\Domain\Ingredient;
use App\Domain\IngredientGroup;

class IngredientManager extends BaseManager
{

	public function create(IngredientGroup $ingredientGroup, $name, $quantity, $unit, Recipie $ref = null)
	{
		$bean = new Ingredient();
		$bean->setIngredientGroup($ingredientGroup);
		$this->updateInternal($bean, $name, $quantity, $unit, $ref);
		parent::persist($bean);

		return $bean;
	}

	public function update(Ingredient $bean, $name, $quantity, $unit, Recipie $ref = null)
	{
		$this->updateInternal($bean, $name, $quantity, $unit, $ref);
		parent::persist($bean);
	}

	protected function updateInternal(Ingredient $bean, $name, $quantity, $unit, Recipie $ref = null)
	{
		$bean->setName($name);
		$bean->setQuantity($quantity);
		$bean->setUnit($unit);
		$bean->setRef($ref);
	}

}