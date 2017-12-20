<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="stat")
 **/
class Stat
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
     * @var \int
     *
     * @Column(name="value", type="integer")
     */
    private $value;

    /**
     * @OneToMany(targetEntity="CharacterStat", mappedBy="stat", cascade={"persist"})
     */
    private $characterstats;

    /**
     * @OneToMany(targetEntity="MonsterStat", mappedBy="stat", cascade={"persist"})
     */
    private $monsterstats;

    /**
     * @OneToMany(targetEntity="ItemStat", mappedBy="stat", cascade={"persist"})
     */
    private $itemstats;

    /**
     * @OneToMany(targetEntity="SpellStat", mappedBy="stat", cascade={"persist"})
     */
    private $spellstats;

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
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
    public function getSpellstats()
    {
        return $this->spellstats;
    }

    /**
     * @param mixed $spellstats
     */
    public function setSpellstats($spellstats)
    {
        $this->spellstats = $spellstats;
    }

}
