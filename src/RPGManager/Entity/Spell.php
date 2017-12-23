<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="spell")
 **/
class Spell
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
     * @var string
     * @Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @OneToMany(targetEntity="SpellStat", mappedBy="spell", cascade={"persist"})
     */
    private $spellStats;

    /**
     * @OneToMany(targetEntity="CharacterSpell", mappedBy="spell", cascade={"persist"})
     */
    private $characterSpells;

    /**
     * @OneToMany(targetEntity="MonsterSpell", mappedBy="spell", cascade={"persist"})
     */
    private $monsterSpells;

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
	public function getSpellStats()
	{
		return $this->spellStats;
	}
	
	/**
	 * @param mixed $spellStats
	 */
	public function setSpellStats($spellStats)
	{
		$this->spellStats = $spellStats;
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

}
