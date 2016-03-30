<?php

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="stepgroup")
 **/
class StepGroup extends BaseEntity {

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
	 * @ManyToOne(targetEntity="App\Domain\Recipie", inversedBy="stepGroups")
	 * @JoinColumn(name="recipie_id", referencedColumnName="id")
	 */
	private $recipie;

	/**
	 * @OneToMany(targetEntity="App\Domain\Step", mappedBy="stepGroup",cascade={"persist", "remove"})
	 */
	private $steps;

	public function __construct () {
		$this->steps = new ArrayCollection();
	}

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
	 * @return StepGroup
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
	 * Set recipie
	 *
	 * @param \App\Domain\Recipie $recipie
	 *
	 * @return StepGroup
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

	/**
	 * Add step
	 *
	 * @param \App\Domain\Step $step
	 *
	 * @return StepGroup
	 */
	public function addStep(\App\Domain\Step $step)
	{
		$this->steps[] = $step;

		return $this;
	}

	/**
	 * Remove step
	 *
	 * @param \App\Domain\Step $step
	 */
	public function removeStep(\App\Domain\Step $step)
	{
		$this->steps->removeElement($step);
	}

	/**
	 * Get steps
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getSteps()
	{
		return $this->steps;
	}
}

