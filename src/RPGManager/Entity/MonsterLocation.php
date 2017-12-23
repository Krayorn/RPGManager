<?php

namespace RPGManager\Entity;

/**
 * @Entity @Table(name="monsterlocation")
 **/
class MonsterLocation
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
     * @ManyToOne(targetEntity="Monster", inversedBy="monsterlocations", cascade={"persist"})
     * @JoinColumn(name="monster_id", referencedColumnName="id")
     */
    private $monster;
	
	/**
	 * @var int
	 * @Column(name="number", type="integer")
	 */
	private $number;

    /**
     * @ManyToOne(targetEntity="Place", inversedBy="monsterlocations", cascade={"persist"})
     * @JoinColumn(name="place_id", referencedColumnName="id")
     */
    private $place;

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
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @param mixed $number
	 */
	public function setNumber($number)
	{
		$this->number = $number;
	}

    /**
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

}

