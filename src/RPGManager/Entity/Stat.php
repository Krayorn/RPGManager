<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="stat")
 **/
class Stat
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
     * @var int
     * @Column(name="value", type="integer")
     */
    private $value;

    /**
     * @OneToMany(targetEntity="CharacterStat", mappedBy="stat", cascade={"persist"})
     */
    private $characterStats;

    /**
     * @OneToMany(targetEntity="MonsterStat", mappedBy="stat", cascade={"persist"})
     */
    private $monsterStats;

    /**
     * @OneToMany(targetEntity="ItemStat", mappedBy="stat", cascade={"persist"})
     */
    private $itemStats;

    /**
     * @OneToMany(targetEntity="SpellStat", mappedBy="stat", cascade={"persist"})
     */
    private $spellStats;

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
	public function getCharacterStats()
	{
		return $this->characterStats;
	}
	
	/**
	 * @param mixed $characterStats
	 */
	public function setCharacterStats($characterStats)
	{
		$this->characterStats = $characterStats;
	}
	
	/**
	 * @return mixed
	 */
	public function getMonsterStats()
	{
		return $this->monsterStats;
	}
	
	/**
	 * @param mixed $monsterStats
	 */
	public function setMonsterStats($monsterStats)
	{
		$this->monsterStats = $monsterStats;
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

}
