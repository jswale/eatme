<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\Recipie;
use App\Domain\Timer;

class TimerManager extends BaseManager
{

	public function create(Recipie $recipie, $name, $value)
	{
		$bean = new Timer();
		$bean->setRecipie($recipie);
		$this->updateInternal($bean, $name, $value);
		parent::persist($bean);

		return $bean;
	}

	public function update(Timer $bean, $name, $value)
	{
		$this->updateInternal($bean, $name, $value);
		parent::persist($bean);
	}

	protected function updateInternal(Timer $bean, $name, $value)
	{
		$bean->setName($name);
		$bean->setValue($value);
	}

}