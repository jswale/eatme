<?php

namespace App\Domain;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @Entity
 * @Table(name="user")
 **/
class User extends BaseEntity implements AdvancedUserInterface {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string", length=255, unique=true)
	 */
	protected $username;

	/**
	 * @Column(type="string", length=255)
	 */
	protected $name;

	/**
	 * @Column(type="string", length=255)
	 */
	protected $password;

	/**
	 * @Column(type="string", length=255)
	 */
	protected $salt;

	/**
	 * @Column(type="boolean")
	 */
	protected $admin;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return User
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	public function setSalt($salt)
	{

		$this->salt = $salt;

		return $this;
	}

	/**
	 * Get salt
	 *
	 * @return string
	 */
	public function getSalt()
	{
		return $this->salt;
	}

	/**
	 * Get admin
	 *
	 * @return boolean
	 */
	public function isAdmin()
	{
		return $this->admin;
	}

	/**
	 * Set admin
	 *
	 * @param boolean $admin
	 *
	 * @return User
	 */
	public function setAdmin($admin)
	{
		$this->admin = $admin;

		return $this;
	}

	/**
	 * Get roles
	 *
	 * @return string
	 */
	public function getRoles()
	{
		return $this->isAdmin() ? array("ROLE_ADMIN","ROLE_USER") : array("ROLE_USER");
	}

	public function isAccountNonExpired()
	{
		return true;
	}

	public function isAccountNonLocked()
	{
		return true;
	}

	public function isCredentialsNonExpired()
	{
		return true;
	}

	public function isEnabled()
	{
		return true;
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials() {

	}
}