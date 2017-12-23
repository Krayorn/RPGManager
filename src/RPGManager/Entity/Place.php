<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="place")
 **/
class Place
{
    /**
     * @var int
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \string
     *
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
    private $monsterlocations;

    /**
     * @OneToMany(targetEntity="ItemLocation", mappedBy="place", cascade={"persist"})
     */
    private $itemlocations;

    /**
     * @OneToMany(targetEntity="NpcLocation", mappedBy="place", cascade={"persist"})
     */
    private $npclocations;

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
    public function getMonsterlocations()
    {
        return $this->monsterlocations;
    }

    /**
     * @param mixed $monsterlocations
     */
    public function setMonsterlocations($monsterlocations)
    {
        $this->monsterlocations = $monsterlocations;
    }

    /**
     * @return mixed
     */
    public function getItemlocations()
    {
        return $this->itemlocations;
    }

    /**
     * @param mixed $itemlocations
     */
    public function setItemlocations($itemlocations)
    {
        $this->itemlocations = $itemlocations;
    }
	
	/**
	 * @return mixed
	 */
	public function getNpclocations()
	{
		return $this->npclocations;
	}
	
	/**
	 * @param mixed $npclocations
	 */
	public function setNpclocations($npclocations)
	{
		$this->npclocations = $npclocations;
	}

}
