<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="player")
 **/
class Character
{
    /**
     * @var int
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @OneToMany(targetEntity="CharacterInventory", mappedBy="character", cascade={"persist"})
     */
    private $characterInventories;

    /**
     * @OneToMany(targetEntity="CharacterStat", mappedBy="character", cascade={"persist"})
     */
    private $characterStats;

    /**
     * @OneToMany(targetEntity="CharacterSpell", mappedBy="character", cascade={"persist"})
     */
    private $characterSpells;

    /**
     * @ManyToOne(targetEntity="Place", inversedBy="characters", cascade={"persist"})
     * @JoinColumn(name="place_id", referencedColumnName="id")
     */
    private $location;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

	/**
	 * @return mixed
	 */
	public function getCharacterInventories()
	{
		return $this->characterInventories;
	}

	/**
	 * @param mixed $characterInventories
	 */
	public function setCharacterInventories($characterInventories)
	{
		$this->characterInventories = $characterInventories;
	}

	/**
	 * @return mixed
	 */
	public function getStats()
	{
		return $this->characterStats;
	}

	/**
	 * @param mixed $characterStats
	 */
	public function setStats($characterStats)
	{
		$this->characterStats = $characterStats;
	}

	/**
	 * @return mixed
	 */
	public function getCharacterSpells()
	{
		return $this->characterSpells;
	}

	/**
	 * @param mixed $characterSpells
	 */
	public function setCharacterSpells($characterSpells)
	{
		$this->characterSpells = $characterSpells;
	}

	/**
	 * @return mixed
	 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @param mixed $location
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	}

}
