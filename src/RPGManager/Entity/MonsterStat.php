<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="monster_stat")
 **/
class MonsterStat
{
    /**
     * @var int
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Monster", inversedBy="monsterStats", cascade={"persist"})
     * @JoinColumn(name="monster_id", referencedColumnName="id")
     */
    private $monster;

    /**
     * @ManyToOne(targetEntity="Stat", inversedBy="monsterStats", cascade={"persist"})
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
    public function getMonster()
    {
        return $this->monster;
    }

    /**
     * @param mixed $monster
     */
    public function setMonster($monster)
    {
        $this->monster = $monster;
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

