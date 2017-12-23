<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="monster_inventory")
 **/
class MonsterInventory
{
    /**
     * @var int
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Monster", inversedBy="monsterInventories", cascade={"persist"})
     * @JoinColumn(name="monster_id", referencedColumnName="id")
     */
    private $monster;

    /**
     * @ManyToOne(targetEntity="Item", inversedBy="monsterInventories", cascade={"persist"})
     * @JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

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
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

}

