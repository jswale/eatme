<?php

namespace App\Domain;

/**
 * @Entity
 * @Table(name="ingredient")
 **/
class Ingredient extends BaseEntity {

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
	 * @Column(type="string", length=255, nullable=true)
	 */
	protected $unit;

	/**
	 * @Column(type="float")
	 */
	protected $quantity;

	/**
	 * @ManyToOne(targetEntity="App\Domain\Recipie")
	 * @JoinColumn(name="ref_id", referencedColumnName="id", nullable=true)
	 */
	protected $ref;

	/**
	 * @OneToOne(targetEntity="App\Domain\Recipie")
	 * @JoinColumn(name="recipie_id", referencedColumnName="id")
	 */
	protected $recipie;

	/**
	 * @ManyToOne(targetEntity="IngredientGroup", inversedBy="ingredients",cascade={"persist"})
	 * @JoinColumn(name="ingredientGroup_id", referencedColumnName="id")
	 */
	private $ingredientGroup;

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
	 * @return Ingredient
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
	 * Set unit
	 *
	 * @param string $unit
	 *
	 * @return Ingredient
	 */
	public function setUnit($unit)
	{
		$this->unit = $unit;

		return $this;
	}

	/**
	 * Get unit
	 *
	 * @return string
	 */
	public function getUnit()
	{
		return $this->unit;
	}

	/**
	 * Set quantity
	 *
	 * @param float $quantity
	 *
	 * @return Ingredient
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * Get quantity
	 *
	 * @return float
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * Set recipie
	 *
	 * @param \App\Domain\Recipie $recipie
	 *
	 * @return Ingredient
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
	 * Set ref
	 *
	 * @param \App\Domain\Recipie $ref
	 *
	 * @return Ingredient
	 */
	public function setRef(\App\Domain\Recipie $ref = null)
	{
		$this->ref = $ref;

		return $this;
	}

	/**
	 * Get ref
	 *
	 * @return \App\Domain\Recipie
	 */
	public function getRef()
	{
		return $this->ref;
	}

	/**
	 * Set ingredientGroup
	 *
	 * @param \App\Domain\IngredientGroup $ingredientGroup
	 *
	 * @return Ingredient
	 */
	public function setIngredientGroup(\App\Domain\IngredientGroup $ingredientGroup = null)
	{
		$this->ingredientGroup = $ingredientGroup;

		return $this;
	}

	/**
	 * Get ingredientGroup
	 *
	 * @return \App\Domain\IngredientGroup
	 */
	public function getIngredientGroup()
	{
		return $this->ingredientGroup;
	}
	}