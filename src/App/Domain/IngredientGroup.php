<?php

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="eatme_ingredientgroup")
 **/
class IngredientGroup extends BaseEntity {

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
	 * @ManyToOne(targetEntity="App\Domain\Recipie", inversedBy="ingredientGroups")
	 * @JoinColumn(name="recipie_id", referencedColumnName="id")
	 */
	private $recipie;

	/**
	 * @OneToMany(targetEntity="App\Domain\Ingredient", mappedBy="ingredientGroup" ,cascade={"persist", "remove"})
	 */
	private $ingredients;

	public function __construct () {
		$this->ingredients = new ArrayCollection();
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
	 * @return IngredientGroup
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
	 * @return IngredientGroup
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
	 * Add ingredient
	 *
	 * @param \App\Domain\Ingredient $ingredient
	 *
	 * @return IngredientGroup
	 */
	public function addIngredient(\App\Domain\Ingredient $ingredient)
	{
		$this->ingredients[] = $ingredient;

		return $this;
	}

	/**
	 * Remove ingredient
	 *
	 * @param \App\Domain\Ingredient $ingredient
	 */
	public function removeIngredient(\App\Domain\Ingredient $ingredient)
	{
		$this->ingredients->removeElement($ingredient);
	}

	/**
	 * Get ingredients
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getIngredients()
	{
		return $this->ingredients;
	}
	}