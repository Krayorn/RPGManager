<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="place")
 **/
class Place
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
     * @OneToMany(targetEntity="Direction", mappedBy="placeStart", cascade={"persist"})
     */
    private $directions;

    /**
     * @OneToMany(targetEntity="MonsterLocation", mappedBy="place", cascade={"persist"})
     */
    private $monsterLocations;

    /**
     * @OneToMany(targetEntity="ItemLocation", mappedBy="place", cascade={"persist"})
     */
    private $itemLocations;

    /**
     * @OneToMany(targetEntity="NpcLocation", mappedBy="place", cascade={"persist"})
     */
    private $npcLocations;

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
    public function getDirections()
    {
        return $this->directions;
    }

    /**
     * @param mixed $directions
     */
    public function setDirections($directions)
    {
        $this->directions = $directions;
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
	public function getNpcLocations()
	{
		return $this->npcLocations;
	}
	
	/**
	 * @param mixed $npcLocations
	 */
	public function setNpcLocations($npcLocations)
	{
		$this->npcLocations = $npcLocations;
	}

}
