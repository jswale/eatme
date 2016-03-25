<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Recipie;
use App\Domain\StepGroup;

class StepGroupManager extends BaseManager
{

	public function create(Recipie $recipie, $name)
	{
		$bean = new StepGroup();
		$bean->setRecipie($recipie);
		$this->updateInternal($bean, $name);
		parent::persist($bean);

		return $bean;
	}

	public function update(StepGroup $bean, $name)
	{
		$this->updateInternal($bean, $name);
		parent::persist($bean);
	}

	protected function updateInternal(StepGroup $bean, $name)
	{
		$bean->setName($name);
	}

}