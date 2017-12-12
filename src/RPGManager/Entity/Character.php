<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="character")
 **/
class Character
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
     * @Column(name="description", type="text")
     */
    private $description;

    /**
     * @OneToMany(targetEntity="CharacterInventory", mappedBy="character", cascade={"persist"})
     */
    private $characterinventories;

    /**
     * @OneToMany(targetEntity="CharacterStat", mappedBy="character", cascade={"persist"})
     */
    private $characterstats;

    /**
     * @OneToMany(targetEntity="CharacterSpell", mappedBy="character", cascade={"persist"})
     */
    private $characterspells;

    /**
     * @var \int
     *
     * @Column(name="location", type="integer")
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
    public function getCharacterinventories()
    {
        return $this->characterinventories;
    }

    /**
     * @param mixed $characterinventories
     */
    public function setCharacterinventories($characterinventories)
    {
        $this->characterinventories = $characterinventories;
    }

    /**
     * @return mixed
     */
    public function getCharacterstats()
    {
        return $this->characterstats;
    }

    /**
     * @param mixed $characterstats
     */
    public function setCharacterstats($characterstats)
    {
        $this->characterstats = $characterstats;
    }

    /**
     * @return mixed
     */
    public function getCharacterspells()
    {
        return $this->characterspells;
    }

    /**
     * @param mixed $characterspells
     */
    public function setCharacterspells($characterspells)
    {
        $this->characterspells = $characterspells;
    }

    /**
     * @return int
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param int $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

}
