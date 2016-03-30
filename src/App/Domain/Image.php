<?php

namespace App\Domain;

/**
 * @Entity
 * @Table(name="image")
 **/
class Image extends BaseEntity {

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
	 * @Column(type="string", length=50)
	 */
	protected $extension;

	/**
	 * @ManyToOne(targetEntity="App\Domain\Recipie", inversedBy="images")
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
	 * @return Image
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
	 * Set extension
	 *
	 * @param string $extension
	 *
	 * @return Image
	 */
	public function setExtension($extension)
	{
		$this->extension = $extension;

		return $this;
	}

	/**
	 * Get extension
	 *
	 * @return string
	 */
	public function getExtension()
	{
		return $this->extension;
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

