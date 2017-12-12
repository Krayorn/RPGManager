<?php

/**
 * @Entity @Table(name="spellstat")
 **/
class SpellStat
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
     * @ManyToOne(targetEntity="Spell", inversedBy="spellstats", cascade={"persist"})
     * @JoinColumn(name="spell_id", referencedColumnName="id")
     */
    private $spell;

    /**
     * @ManyToOne(targetEntity="Stat", inversedBy="spellstats", cascade={"persist"})
     * @JoinColumn(name="stat_id", referencedColumnName="id")
     */
    private $stat;

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
    public function getSpell()
    {
        return $this->spell;
    }

    /**
     * @param mixed $spell
     */
    public function setSpell($spell)
    {
        $this->spell = $spell;
    }

    /**
     * @return mixed
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * @param mixed $stat
     */
    public function setStat($stat)
    {
        $this->stat = $stat;
    }

}

