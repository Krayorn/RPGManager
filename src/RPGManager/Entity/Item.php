<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="item")
 **/
class Item
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
     * @OneToMany(targetEntity="ItemStat", mappedBy="item", cascade={"persist"})
     */
    private $itemstats;

    /**
     * @OneToMany(targetEntity="ItemLocation", mappedBy="item", cascade={"persist"})
     */
    private $itemlocations;

    /**
     * @OneToMany(targetEntity="CharacterInventory", mappedBy="item", cascade={"persist"})
     */
    private $characterinventories;

    /**
     * @OneToMany(targetEntity="MonsterInventory", mappedBy="item", cascade={"persist"})
     */
    private $monsterinventories;

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
    public function getItemstats()
    {
        return $this->itemstats;
    }

    /**
     * @param mixed $itemstats
     */
    public function setItemstats($itemstats)
    {
        $this->itemstats = $itemstats;
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
    public function getMonsterinventories()
    {
        return $this->monsterinventories;
    }

    /**
     * @param mixed $monsterinventories
     */
    public function setMonsterinventories($monsterinventories)
    {
        $this->monsterinventories = $monsterinventories;
    }

}
