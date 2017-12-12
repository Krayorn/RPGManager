<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="monsterspell")
 **/
class MonsterSpell
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
     * @ManyToOne(targetEntity="Monster", inversedBy="monsterspells", cascade={"persist"})
     * @JoinColumn(name="monster_id", referencedColumnName="id")
     */
    private $monsters;

    /**
     * @ManyToOne(targetEntity="Spell", inversedBy="monsterspells", cascade={"persist"})
     * @JoinColumn(name="spell_id", referencedColumnName="id")
     */
    private $spells;

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
     * @return mixed
     */
    public function getMonsters()
    {
        return $this->monsters;
    }

    /**
     * @param mixed $monsters
     */
    public function setMonsters($monsters)
    {
        $this->monsters = $monsters;
    }

    /**
     * @return mixed
     */
    public function getSpells()
    {
        return $this->spells;
    }

    /**
     * @param mixed $spells
     */
    public function setSpells($spells)
    {
        $this->spells = $spells;
    }

}