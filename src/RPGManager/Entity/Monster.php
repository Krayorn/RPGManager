<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="monster")
 **/
class Monster
{

    private $temporaryStats;

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
     * @OneToMany(targetEntity="MonsterInventory", mappedBy="monster", cascade={"persist"})
     */
    private $monsterInventories;

    /**
     * @OneToMany(targetEntity="MonsterStat", mappedBy="monster", cascade={"persist"})
     */
    private $monsterStats;

    /**
     * @OneToMany(targetEntity="MonsterSpell", mappedBy="monster", cascade={"persist"})
     */
    private $monsterSpells;

    /**
     * @OneToMany(targetEntity="MonsterLocation", mappedBy="monster", cascade={"persist"})
     */
    private $monsterLocations;

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
	public function getMonsterInventories()
	{
		return $this->monsterInventories;
	}

	/**
	 * @param mixed $monsterInventories
	 */
	public function setMonsterInventories($monsterInventories)
	{
		$this->monsterInventories = $monsterInventories;
	}

	/**
	 * @return mixed
	 */
	public function getStats()
	{
		return $this->monsterStats;
	}

	/**
	 * @param mixed $monsterStats
	 */
	public function setStats($monsterStats)
	{
		$this->monsterStats = $monsterStats;
	}

	/**
	 * @return mixed
	 */
	public function getMonsterSpells()
	{
		return $this->monsterSpells;
	}

	/**
	 * @param mixed $monsterSpells
	 */
	public function setMonsterSpells($monsterSpells)
	{
		$this->monsterSpells = $monsterSpells;
	}

	/**
	 * @return mixed
	 */
	public function getMonsterLocations()
	{
		return $this->monsterLocations;
	}

	/**
	 * @param mixed $monsterLocations
	 */
	public function setMonsterLocations($monsterLocations)
	{
		$this->monsterLocations = $monsterLocations;
	}

	public function getTemporaryStats()
	{
		return $this->temporaryStats;
	}

	public function setTemporaryStats($temporaryStats)
	{
		$this->temporaryStats = $temporaryStats;
	}

}
