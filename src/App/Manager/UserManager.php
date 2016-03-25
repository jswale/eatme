<?php

namespace App\Manager;

use App\Manager\BaseManager;
use App\Domain\User;

class UserManager extends BaseManager
{

	public function create($username, $name, $admin, $password)
	{
		$bean = new User();
		$this->updateInternal($bean, $name, $username, $admin);
		$this->setPassword($bean, $password);
		parent::persist($bean);

		return $bean;
	}

	public function update(User $bean, $username, $name, $admin)
	{
		$this->updateInternal($bean, $name, $username, $admin);
 		parent::persist($bean);
	}

	public function changePassword(User $bean, $password)
	{
		$this->setPassword($bean, $password);
 		parent::persist($bean);
	}

	public function delete(User $bean)
	{
		$this->app['recipie.manager']->removeUser($bean);

 		parent::remove($bean);
	}

	public function getByUsername($username)
	{
		return
		$this
		->em
		->createQuery('SELECT b FROM ' . $this->class . ' b WHERE b.username = :username')->setParameter(':username', $username)
		->getOneOrNullResult();
	}

	protected function updateInternal(User $bean, $name, $username, $admin)
	{
		$bean->setName($name);
		$bean->setUsername($username);
		$bean->setAdmin($admin);
	}

	protected function setPassword(User $bean, $password)
	{
		$encoder = $this->app['security.encoder_factory']->getEncoder($bean);
		$bean->setSalt(md5(uniqid(null, true)));
		$bean->setPassword($encoder->encodePassword($password, $bean->getSalt()));
	}

}