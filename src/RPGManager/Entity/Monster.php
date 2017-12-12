<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="monster")
 **/
class Monster
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
     * @OneToMany(targetEntity="MonsterInventory", mappedBy="monster", cascade={"persist"})
     */
    private $monsterinventories;

    /**
     * @OneToMany(targetEntity="MonsterStat", mappedBy="monster", cascade={"persist"})
     */
    private $monsterstats;

    /**
     * @OneToMany(targetEntity="MonsterSpell", mappedBy="monster", cascade={"persist"})
     */
    private $monsterspells;

    /**
     * @OneToMany(targetEntity="MonsterLocation", mappedBy="monster", cascade={"persist"})
     */
    private $monsterlocations;

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

    /**
     * @return mixed
     */
    public function getMonsterstats()
    {
        return $this->monsterstats;
    }

    /**
     * @param mixed $monsterstats
     */
    public function setMonsterstats($monsterstats)
    {
        $this->monsterstats = $monsterstats;
    }

    /**
     * @return mixed
     */
    public function getMonsterspells()
    {
        return $this->monsterspells;
    }

    /**
     * @param mixed $monsterspells
     */
    public function setMonsterspells($monsterspells)
    {
        $this->monsterspells = $monsterspells;
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

}
