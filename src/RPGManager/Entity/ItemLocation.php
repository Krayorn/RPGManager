<?php

namespace RPGManager\Entity;

/**
 * @Entity
 * @Table(name="item_location")
 **/
class ItemLocation
{
    /**
     * @var int
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Item", inversedBy="itemLocations", cascade={"persist"})
     * @JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;
	
	/**
	 * @var int
	 * @Column(name="number", type="integer")
	 */
    private $number;

    /**
     * @ManyToOne(targetEntity="Place", inversedBy="itemLocations", cascade={"persist"})
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
	
	/**
	 * @return int
	 */
	public function getNumber()
	{
		return $this->number;
	}
	
	/**
	 * @param int $number
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

