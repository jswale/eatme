<?php

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="recipie")
 **/
class Recipie extends BaseEntity {

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
	protected $author;

	/**
	 * @Column(type="string", length=255, nullable=true)
	 */
	protected $quantity;

	/**
	 * @Column(type="string", length=2000, nullable=true)
	 */
	protected $description;

	/**
	 * @Column(type="datetime")
	 */
	protected $createDate;

	/**
	 * @Column(type="datetime", nullable=true)
	 */
	protected $updateDate;

	/**
	 * @ManyToOne(targetEntity="App\Domain\Category")
	 */
	protected  $category;

	/**
	 * @ManyToOne(targetEntity="App\Domain\User")
	 */
	protected $user;

	/**
	 * @ManyToMany(targetEntity="App\Domain\Tag")
	 * @JoinTable(name="recipie_tags",
	 * 		joinColumns={@JoinColumn(name="recipie_id", referencedColumnName="id")},
	 * 		inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
	 * )
	 */
	private $tags;

	/**
	 * @OneToMany(targetEntity="App\Domain\Timer", mappedBy="recipie",cascade={"persist", "remove"})
	 */
	private $timers;

	/**
	 * @OneToMany(targetEntity="App\Domain\Image", mappedBy="recipie",cascade={"persist", "remove"})
	 */
	private $images;

	/**
	 * @OneToMany(targetEntity="App\Domain\IngredientGroup", mappedBy="recipie",cascade={"persist", "remove"})
	 */
	private $ingredientGroups;

	/**
	 * @OneToMany(targetEntity="App\Domain\StepGroup", mappedBy="recipie",cascade={"persist", "remove"})
	 */
	private $stepGroups;

	public function __construct () {
		$this->tags = new ArrayCollection();
		$this->images = new ArrayCollection();
		$this->timers = new ArrayCollection();
		$this->ingredientGroups = new ArrayCollection();
		$this->stepGroups = new ArrayCollection();
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
	 * @return Recipie
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
	 * Set author
	 *
	 * @param string $author
	 *
	 * @return Recipie
	 */
	public function setAuthor($author)
	{
		$this->author = $author;

		return $this;
	}

	/**
	 * Get author
	 *
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * Set quantity
	 *
	 * @param string $quantity
	 *
	 * @return Recipie
	 */
	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;

		return $this;
	}

	/**
	 * Get quantity
	 *
	 * @return string
	 */
	public function getQuantity()
	{
		return $this->quantity;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return Recipie
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
	 * Set createDate
	 *
	 * @param \DateTime $createDate
	 *
	 * @return Recipie
	 */
	public function setCreateDate($createDate)
	{
		$this->createDate = $createDate;

		return $this;
	}

	/**
	 * Get createDate
	 *
	 * @return \DateTime
	 */
	public function getCreateDate()
	{
		return $this->createDate;
	}

	/**
	 * Set updateDate
	 *
	 * @param \DateTime $updateDate
	 *
	 * @return Recipie
	 */
	public function setUpdateDate($updateDate)
	{
		$this->updateDate = $updateDate;

		return $this;
	}

	/**
	 * Get updateDate
	 *
	 * @return \DateTime
	 */
	public function getUpdateDate()
	{
		return $this->updateDate;
	}

	/**
	 * Set category
	 *
	 * @param \App\Domain\Category $category
	 *
	 * @return Recipie
	 */
	public function setCategory(\App\Domain\Category $category = null)
	{
		$this->category = $category;

		return $this;
	}

	/**
	 * Get category
	 *
	 * @return \App\Domain\Category
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * Set user
	 *
	 * @param \App\Domain\User $user
	 *
	 * @return Recipie
	 */
	public function setUser(\App\Domain\User $user = null)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return \App\Domain\User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Add tag
	 *
	 * @param \App\Domain\Tag $tag
	 *
	 * @return Recipie
	 */
	public function addTag(\App\Domain\Tag $tag)
	{
		$this->tags[] = $tag;

		return $this;
	}

	/**
	 * Remove tag
	 *
	 * @param \App\Domain\Tag $tag
	 */
	public function removeTag(\App\Domain\Tag $tag)
	{
		$this->tags->removeElement($tag);
	}

	/**
	 * Get tags
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Add timer
	 *
	 * @param \App\Domain\Timer $timer
	 *
	 * @return Recipie
	 */
	public function addTimer(\App\Domain\Timer $timer)
	{
		$this->timers[] = $timer;

		return $this;
	}

	/**
	 * Remove timer
	 *
	 * @param \App\Domain\Timer $timer
	 */
	public function removeTimer(\App\Domain\Timer $timer)
	{
		$this->timers->removeElement($timer);
	}

	/**
	 * Get timers
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTimers()
	{
		return $this->timers;
	}


	/**
	 * Add image
	 *
	 * @param \App\Domain\Image $image
	 *
	 * @return Recipie
	 */
	public function addImage(\App\Domain\Image $image)
	{
		$this->images[] = $image;

		return $this;
	}

	/**
	 * Remove image
	 *
	 * @param \App\Domain\Image $image
	 */
	public function removeImage(\App\Domain\Image $image)
	{
		$this->images->removeElement($image);
	}

	/**
	 * Get images
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getImages()
	{
		return $this->images;
	}

	/**
	 * Add ingredientGroup
	 *
	 * @param \App\Domain\IngredientGroup $ingredientGroup
	 *
	 * @return Recipie
	 */
	public function addIngredientGroup(\App\Domain\IngredientGroup $ingredientGroup)
	{
		$this->ingredientGroups[] = $ingredientGroup;

		return $this;
	}

	/**
	 * Remove ingredientGroup
	 *
	 * @param \App\Domain\IngredientGroup $ingredientGroup
	 */
	public function removeIngredientGroup(\App\Domain\IngredientGroup $ingredientGroup)
	{
		$this->ingredientGroups->removeElement($ingredientGroup);
	}

	/**
	 * Get ingredientGroups
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getIngredientGroups()
	{
		return $this->ingredientGroups;
	}

	/**
	 * Add stepGroup
	 *
	 * @param \App\Domain\StepGroup $stepGroup
	 *
	 * @return Recipie
	 */
	public function addStepGroup(\App\Domain\StepGroup $stepGroup)
	{
		$this->stepGroups[] = $stepGroup;

		return $this;
	}

	/**
	 * Remove stepGroup
	 *
	 * @param \App\Domain\StepGroup $stepGroup
	 */
	public function removeStepGroup(\App\Domain\StepGroup $stepGroup)
	{
		$this->stepGroups->removeElement($stepGroup);
	}

	/**
	 * Get stepGroups
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getStepGroups()
	{
		return $this->stepGroups;
	}

	/**
	 * Get name to URL display
	 *
	 * @return string
	 */
	public function getCleanName()
	{
		return str_replace(
			array(" ", "'", '"', "&"),
			array("-", "", "", ""),
			$this->getName()
		);
	}
}