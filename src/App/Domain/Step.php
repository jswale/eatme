<?php

namespace App\Domain;

/**
 * @Entity
 * @Table(name="eatme_step")
 **/
class Step extends BaseEntity {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @Column(type="string", length=2000)
	 */
	protected $description;

	/**
	 * @ManyToOne(targetEntity="App\Domain\StepGroup", inversedBy="steps",cascade={"persist"})
	 * @JoinColumn(name="stepGroup_id", referencedColumnName="id")
	 */
	private $stepGroup;


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
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Step
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set stepGroup
	 *
	 * @param \App\Domain\StepGroup $stepGroup
	 *
	 * @return Step
	 */
	public function setStepGroup(\App\Domain\StepGroup $stepGroup = null)
	{
		$this->stepGroup = $stepGroup;

		return $this;
	}

	/**
	 * Get stepGroup
	 *
	 * @return \App\Domain\StepGroup
	 */
	public function getStepGroup()
	{
		return $this->stepGroup;
	}
}

