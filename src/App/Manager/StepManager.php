<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Step;
use App\Domain\StepGroup;

class StepManager extends BaseManager
{

	public function create(StepGroup $stepGroup, $description)
	{
		$bean = new Step();
		$bean->setStepGroup($stepGroup);
		$this->updateInternal($bean, $description);
		parent::persist($bean);

		return $bean;
	}

	public function update(Step $bean, $description)
	{
		$this->updateInternal($bean, $description);
		parent::persist($bean);
	}

	protected function updateInternal(Step $bean, $description)
	{
		$bean->setDescription($description);
	}

}