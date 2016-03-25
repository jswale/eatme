<?php

namespace App\Domain;

/**
 * @Entity
 * @Table(name="eatme_timer")
 **/
class Timer extends BaseEntity {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string", length=255)
	 */
	protected $name;

	/**
	 * @Column(type="string", length=255)
	 */
	protected $value;

	/**
	 * @ManyToOne(targetEntity="App\Domain\Recipie", inversedBy="timers")
	 * @JoinColumn(name="recipie_id", referencedColumnName="id")
	 */
	private $recipie;

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
	 * @return Timer
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
	 * Set value
	 *
	 * @param float $value
	 *
	 * @return Timer
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return float
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Set recipie
	 *
	 * @param \App\Domain\Recipie $recipie
	 *
	 * @return Timer
	 */
	public function setRecipie(\App\Domain\Recipie $recipie = null)
	{
		$this->recipie = $recipie;

		return $this;
	}

	/**
	 * Get recipie
	 *
	 * @return \App\Domain\Recipie
	 */
	public function getRecipie()
	{
		return $this->recipie;
	}
}

