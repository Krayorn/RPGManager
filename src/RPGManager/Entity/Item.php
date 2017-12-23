<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="item")
 **/
class Item
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
     * @OneToMany(targetEntity="ItemStat", mappedBy="item", cascade={"persist"})
     */
    private $itemStats;

    /**
     * @OneToMany(targetEntity="ItemLocation", mappedBy="item", cascade={"persist"})
     */
    private $itemLocations;

    /**
     * @OneToMany(targetEntity="CharacterInventory", mappedBy="item", cascade={"persist"})
     */
    private $characterInventories;

    /**
     * @OneToMany(targetEntity="MonsterInventory", mappedBy="item", cascade={"persist"})
     */
    private $monsterInventories;

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
	public function getItemStats()
	{
		return $this->itemStats;
	}
	
	/**
	 * @param mixed $itemStats
	 */
	public function setItemStats($itemStats)
	{
		$this->itemStats = $itemStats;
	}
	
	/**
	 * @return mixed
	 */
	public function getItemLocations()
	{
		return $this->itemLocations;
	}
	
	/**
	 * @param mixed $itemLocations
	 */
	public function setItemLocations($itemLocations)
	{
		$this->itemLocations = $itemLocations;
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

}
