<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="spell")
 **/
class Spell
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
     * @var \string
     *
     * @Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @OneToMany(targetEntity="SpellStat", mappedBy="spell", cascade={"persist"})
     */
    private $spellstats;

    /**
     * @OneToMany(targetEntity="CharacterSpell", mappedBy="spell", cascade={"persist"})
     */
    private $characterspells;

    /**
     * @OneToMany(targetEntity="MonsterSpell", mappedBy="spell", cascade={"persist"})
     */
    private $monsterspells;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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

}
