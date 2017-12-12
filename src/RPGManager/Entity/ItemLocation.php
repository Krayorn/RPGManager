<?php

/**
 * @Entity @Table(name="itemlocation")
 **/
class ItemLocation
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
     * @ManyToOne(targetEntity="Item", inversedBy="itemlocations", cascade={"persist"})
     * @JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;

    /**
     * @ManyToOne(targetEntity="Place", inversedBy="itemlocations", cascade={"persist"})
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

